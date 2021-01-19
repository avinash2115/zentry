import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { PerfectScrollbarComponent } from 'ngx-perfect-scrollbar';
import { filter, take, takeUntil } from 'rxjs/operators';
import { animate, keyframes, style, transition, trigger } from '@angular/animations';
import { FilterService } from './filter.service';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { EStates } from './abstractions/base.abstract';
import { EFilterTypes, IFilter } from '../../../../vendor/vp-ngx-jsonapi/interfaces/IFilter';

@Component({
    selector: 'app-assistant-filter',
    templateUrl: './filter.component.html',
    styleUrls: ['./filter.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    animations: [
        trigger('filter', [
            transition(':enter', [
                animate('0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940)', keyframes([
                    style({transform: 'translateX(-1000px)', opacity: 0}),
                    style({transform: 'translateX(0)', opacity: 1})
                ]))
            ]),
            transition(':leave', [
                animate('1s cubic-bezier(0.250, 0.460, 0.450, 0.940)', keyframes([
                    style({transform: 'translateX(0)', opacity: 1}),
                    style({transform: 'translateX(-1000px)', opacity: 0})
                ]))
            ])
        ]),
        trigger('backdrop', [
            transition(':enter', [
                animate('0.5s cubic-bezier(0.390, 0.575, 0.565, 1.000)', keyframes([
                    style({opacity: 0}),
                    style({opacity: 1})
                ]))
            ]),
            transition(':leave', [
                animate('0.5s cubic-bezier(0.390, 0.575, 0.565, 1.000)', keyframes([
                    style({opacity: 1}),
                    style({opacity: 0})
                ]))
            ])
        ])
    ]
})
export class FilterComponent extends BaseDetachedComponent implements OnInit {
    @ViewChild('ps', {static: false}) ps: PerfectScrollbarComponent;

    public filterKeys: Array<{ key: string, type: EFilterTypes }> = [];

    public readonly statesAvailable: typeof EStates = EStates;
    public readonly filterTypes: typeof EFilterTypes = EFilterTypes;

    constructor(
        protected cdr: ChangeDetectorRef,
        public filterService: FilterService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.filterService.filtersAvailableUpdate([]);

        this.filterService
            .filtersAvailable
            .pipe(
                filter((value: Array<IFilter>) => !!value.length),
                take(1)
            )
            .subscribe((availableFilters: Array<IFilter>) => {
                this.filterKeys = availableFilters.map((filter: IFilter) => ({
                    key: filter.attribute,
                    type: filter.type
                }));

                this.loadingCompleted()
            });

        this.filterService
            .preventScroll
            .pipe(takeUntil(this._destroy$))
            .subscribe((flag: boolean) => {
                const yPosition: number = this.ps.directiveRef.position(true).y as number;

                if (flag) {
                    this.ps.disabled = flag;
                    this.ps.directiveRef.elementRef.nativeElement.style.top = `-${yPosition}px`;
                    this.ps.directiveRef.elementRef.nativeElement.style.position = 'absolute';
                    this.ps.directiveRef.elementRef.nativeElement.style.left = 0;
                    this.ps.directiveRef.elementRef.nativeElement.style.right = 0;
                } else {
                    this.ps.disabled = flag;
                    this.ps.directiveRef.elementRef.nativeElement.style = {position: 'static'};
                }
            });
    }

    close(): void {
        this.filterService.stateChange(EStates.close);
    }

    apply(): void {
        this.close();
    }

    clear(): void {
        this.filterService.filtersUpdate(null);
    }
}
