import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    HostListener,
    Input,
    OnInit,
    ViewChild
} from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { filter, takeUntil, withLatestFrom } from 'rxjs/operators';
import * as moment from 'moment';
import { FilterService } from '../../filter.service';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { IFilter } from '../../../../../../vendor/vp-ngx-jsonapi/interfaces/IFilter';

enum EDatePresets {
    custom = 'custom',
    today = 'today',
    yesterday = 'yesterday',
    week = 'week',
    month = 'month',
}

const DatePresetsLabels: { [key in keyof typeof EDatePresets]?: string } = {
    [EDatePresets.custom]: 'Custom',
    [EDatePresets.today]: 'Today',
    [EDatePresets.yesterday]: 'Yesterday',
    [EDatePresets.week]: 'This Week',
    [EDatePresets.month]: 'This Month'
};

interface IDatePreset {
    date: { from: Date, to: Date }
}

@Component({
    selector: 'app-assistant-filter-controls-datepicker',
    templateUrl: './datepicker.component.html',
    styleUrls: ['./datepicker.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class DatepickerComponent extends BaseDetachedComponent implements OnInit {
    // @ViewChild('dp', {static: true}) datepicker: BsDaterangepickerDirective;
    @Input() filterKey: string;
    // public bsConfig: Partial<BsDatepickerConfig> = {
    //     dateInputFormat: 'MM/DD/YY',
    //     containerClass: 'theme-blue',
    //     showWeekNumbers: false,
    //     customTodayClass: 'highlight-today'
    // };

    public form: FormGroup;
    public innerWidth: any;
    public objectKeys: ((o: {}) => string[]) = Object.keys;
    public filterName: string;

    public datesPreset: { [key in keyof typeof EDatePresets]?: IDatePreset | string };
    public readonly datePresetLabels = {...DatePresetsLabels};

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected filterService: FilterService
    ) {
        super(cdr);
    }

    @HostListener('window:resize', ['$event'])
    onResize(): void {
        this.innerWidth = window.innerWidth;
    }

    ngOnInit(): void {
        this.innerWidth = window.innerWidth;

        this.form = this.fb.group({
            date: [],
            preset: []
        });

        this.filterService
            .filtersAvailable
            .pipe(
                takeUntil(this._destroy$),
                filter((value: Array<IFilter>) => !!value.length)
            )
            .subscribe((value: Array<IFilter>) => {
                const filterObj: IFilter = value.find((r: IFilter) => r.attribute === this.filterKey);
                this.filterName = filterObj.label;
                if (filterObj.values.length) {
                    const sortedDates: Array<string> = filterObj.values.map(date => date.value)
                        .sort((left, right) => moment.utc(left).diff(moment.utc(right)));
                    // this.bsConfig = Object.assign(this.bsConfig, {
                    //     minDate: moment(sortedDates[0]).toDate(),
                    //     maxDate: moment(sortedDates[sortedDates.length - 1]).toDate()
                    // });
                    // this.datepicker.setConfig();
                    this.initDatePresets(true);
                } else {
                    this.initDatePresets();
                }
            });

        this.filterService
            .filtering
            .pipe(takeUntil(this._destroy$))
            .subscribe((value: boolean) => {
                if (value) {
                    this.form.disable({emitEvent: false, onlySelf: true});
                } else {
                    this.form.enable({emitEvent: false, onlySelf: true});
                }
            });

        this.filterService
            .filters
            .pipe(takeUntil(this._destroy$))
            .subscribe((filters: object) => {
                if (filters && Object.keys(filters).length && filters[this.filterKey]) {
                    if (filters[this.filterKey].hasOwnProperty('from') && filters[this.filterKey].hasOwnProperty('to')) {
                        this.form.get('date').patchValue(
                            [DatepickerComponent.parseDate(filters[this.filterKey].from), DatepickerComponent.parseDate(filters[this.filterKey].to)],
                            {emitEvent: false}
                        );
                        if (!this.form.get('preset').value || this.form.get('preset').value !== EDatePresets.custom) {
                            this.form.get('preset').patchValue(EDatePresets.custom, {emitEvent: false});
                        }
                    } else {
                        this.form.get('preset').patchValue(filters[this.filterKey], {emitEvent: false});
                    }
                    return;
                }
                this.form.reset([], {emitEvent: false});
            });

        this.form
            .get('date')
            .valueChanges
            .pipe(
                withLatestFrom(this.filterService.filters),
                takeUntil(this._destroy$)
            )
            .subscribe(([value, filters]: [Array<Date>, object]) => {
                if (value && value.length) {
                    const data = {
                        from: moment(value[0]).startOf('day').toJSON(),
                        to: moment(value[1]).endOf('day').toJSON()
                    };
                    if (!this.form.get('preset').value) {
                        this.form.get('preset').patchValue(EDatePresets.custom, {emitEvent: false});
                    }
                    if (filters && Object.keys(filters).length) {
                        filters[this.filterKey] = data;
                        this.filterService.filtersUpdate(filters);
                    } else {
                        this.filterService.filtersUpdate({[this.filterKey]: data});
                    }
                } else {
                    delete filters[this.filterKey];
                    this.form.get('preset').patchValue('', {emitEvent: false});
                    this.filterService.filtersUpdate(Object.keys(filters).length ? filters : null);
                }
            });

        this.form
            .get('preset')
            .valueChanges
            .pipe(
                withLatestFrom(this.filterService.filters),
                takeUntil(this._destroy$)
            )
            .subscribe(([value, filters]: [EDatePresets, object]) => {
                if (value) {
                    switch (true) {
                        case value === EDatePresets.custom:
                            // this.datepicker.show();
                            break;
                        default:
                            const {from, to} = (this.datesPreset[value] as IDatePreset).date;
                            this.form.get('date').patchValue([from, to]);
                            break;
                    }
                } else {
                    this.form.get('date').reset({});
                }
            });
    }

    onOpenEvent(): void {
        this.filterService.preventScrollChange(true);
    }

    onCloseEvent(): void {
        this.filterService.preventScrollChange(false);
    }

    private static parseDate(date: string): Date {
        return moment(date).toDate();
    }

    private initDatePresets(shouldFilter: boolean = false): void {
        const dataObj = Object.keys(EDatePresets).reduce((result: { [key: string]: IDatePreset }, key: EDatePresets) => {
            switch (EDatePresets[key]) {
                case EDatePresets.custom: {
                    result[key] = {date: {from: null, to: null}};
                    break;
                }
                case EDatePresets.today: {
                    const date = moment().toDate();
                    result[key] = {date: {from: date, to: date}};
                    break;
                }
                case EDatePresets.yesterday: {
                    const date = moment().startOf('day').subtract(1, 'day').toDate();
                    result[key] = {date: {from: date, to: date}};
                    break;
                }
                case EDatePresets.week: {
                    result[key] = {
                        date: {from: moment().startOf('isoWeek').toDate(), to: moment().endOf('isoWeek').toDate()}
                    };
                    break;
                }
                case EDatePresets.month: {
                    result[key] = {
                        date: {from: moment().startOf('month').toDate(), to: moment().endOf('month').toDate()}
                    };
                    break;
                }
            }

            return result;
        }, {});

        if (shouldFilter) {
            this.datesPreset = this.filterPresets(dataObj);
        } else {
            this.datesPreset = dataObj;
        }
    };

    private filterPresets(dataObj: { [key: string]: IDatePreset }): ({ [key: string]: IDatePreset }) {
        return Object.keys(EDatePresets).reduce((result: { [key: string]: IDatePreset }, key: EDatePresets) => {
            switch (EDatePresets[key]) {
                case EDatePresets.custom: {
                    result[key] = dataObj[key];
                    break;
                }
                default: {
                    const {from, to} = dataObj[key].date;
                    // if (
                    //     moment(from).isBetween(this.bsConfig.minDate, this.bsConfig.maxDate) &&
                    //     moment(to).isBetween(this.bsConfig.minDate, this.bsConfig.maxDate)
                    // ) {
                    //     result[key] = dataObj[key];
                    // }
                    break;
                }
            }
            return result;
        }, {});
    }
}
