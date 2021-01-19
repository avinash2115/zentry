import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { filter, flatMap, takeUntil } from 'rxjs/operators';
import { EFilterTypes, IFilter } from '../../../../../vendor/vp-ngx-jsonapi/interfaces/IFilter';
import { FilterService } from '../filter.service';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';

@Component({
    selector: 'app-assistant-filter-toolbar',
    templateUrl: './toolbar.component.html',
    styleUrls: ['./toolbar.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ToolbarComponent extends BaseDetachedComponent implements OnInit, OnDestroy {
    public filters: Array<IFilter> = [];
    public readonly filtersType: typeof EFilterTypes = EFilterTypes;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected filterService: FilterService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.filterService
            .filtersAvailable
            .pipe(
                filter((value: Array<IFilter>) => !!value.length),
                flatMap(() => this.filterService.filters),
                takeUntil(this._destroy$)
            )
            .subscribe((value: object) => {
                if (value) {
                    this.filters = this.filterService.filtersMutate(value);
                } else {
                    this.filters = [];
                }

                this.detectChanges();
            });
    }

    clear(attribute: string, type: EFilterTypes, value?: string): void {
        this.filterService.filtersUpdateSpecific(attribute, type, value);
    }

    reset(): void {
        this.filterService.filtersUpdate(null);
    }
}
