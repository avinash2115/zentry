import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { filter, takeUntil, withLatestFrom } from 'rxjs/operators';
import { FilterService } from '../../filter.service';
import { IFilter } from '../../../../../../vendor/vp-ngx-jsonapi/interfaces/IFilter';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';

@Component({
    selector: 'app-assistant-filter-controls-select',
    templateUrl: './select.component.html',
    styleUrls: ['./select.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class SelectComponent extends BaseDetachedComponent implements OnInit {
    @Input() searchable: boolean = true;
    @Input() clearable: boolean = true;
    @Input() multiple: boolean = true;
    @Input() filterKey;

    filterValues: Array<{ label: string, value: string }> = [];
    filterName: string;
    control: FormControl;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected filterService: FilterService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.control = new FormControl([]);

        this.filterService
            .filtersAvailable
            .pipe(
                filter((value: Array<IFilter>) => !!value.length),
                takeUntil(this._destroy$)
            )
            .subscribe((availableFilters: Array<IFilter>) => {
                const filterObj: IFilter = availableFilters.find((obj: IFilter) => obj.attribute === this.filterKey);
                this.filterValues = [...filterObj.values.sort((a, b) => a.label.localeCompare(b.label))];
                this.filterName = filterObj.label;
                if (!this.filterValues.length) {
                    this.control.disable({emitEvent: false, onlySelf: true});
                }
            });

        this.filterService
            .filtering
            .pipe(takeUntil(this._destroy$))
            .subscribe((isFiltering: boolean) => {
                if (this.filterValues.length && !isFiltering) {
                    this.control.enable({emitEvent: false, onlySelf: true});
                } else {
                    this.control.disable({emitEvent: false, onlySelf: true});
                }
            });

        this.control
            .valueChanges
            .pipe(
                withLatestFrom(this.filterService.filters),
                takeUntil(this._destroy$)
            )
            .subscribe(([controlValue, filters]: [Array<string>, object]) => {
                if (controlValue && controlValue.length) {
                    if (filters && Object.keys(filters).length) {
                        filters[this.filterKey] = controlValue.length === 1 ? controlValue[0] : controlValue;

                        this.filterService.filtersUpdate(filters);
                    } else {
                        this.filterService.filtersUpdate({[this.filterKey]: controlValue.length === 1 ? controlValue[0] : controlValue});
                    }
                } else {
                    delete filters[this.filterKey];

                    this.filterService.filtersUpdate(Object.keys(filters).length ? filters : null);
                }
            });

        this.filterService
            .filters
            .pipe(takeUntil(this._destroy$))
            .subscribe((filters: object) => {
                if (filters && Object.keys(filters).length) {
                    const data = filters[this.filterKey] ? filters[this.filterKey] : [];
                    this.control.patchValue(Array.isArray(data) ? data : [data], {emitEvent: false});
                } else {
                    this.control.patchValue('', {emitEvent: false});
                }
            });
    }

    onOpenEvent(): void {
        this.filterService.preventScrollChange(true);
    }

    onCloseEvent(): void {
        this.filterService.preventScrollChange(false);
    }
}
