import {
    ChangeDetectorRef,
    Component,
    ContentChild,
    EventEmitter,
    Input,
    OnInit,
    Output,
    TemplateRef
} from '@angular/core';
import { ListService } from '../../list.service';
import { SearchService } from '../../../search/search.service';
import { SortingService } from '../../../sorting/sorting.service';
import { PaginationService } from '../../../pagination/pagination.service';
import { BaseList, EStates, IServicePath } from '../../abstractions/base.abstract';
import { Resource } from '../../../../../../vendor/vp-ngx-jsonapi';
import { ListHeaderTemplateDirective } from '../../directives/list-header-template.directive';
import { ListBodyTemplateDirective } from '../../directives/list-body-template.directive';
import { ListBodyStatesDirective } from '../../directives/list-body-states.directive';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { takeUntil, withLatestFrom } from 'rxjs/operators';
import {
    EDirection,
    ISortable,
    ISortableAttribute,
    ISortableRelation
} from '../../../sorting/abstractions/base.abstract';

@Component({
    selector: 'app-assistant-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    providers: [
        ListService,
        SearchService,
        SortingService,
        PaginationService
    ]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    /**
     * NOTICE: If you use list with pagination you need to add trackBy to *ngFor.
     * */
    @Input() service: BaseList;
    @Input() servicePath: IServicePath = {};

    @Input() filterIncludes: Array<string> = ['*'];
    @Input() filterRemote: object = {};
    @Input() filterBy: object = {};

    @Input() sortableDefault: ISortableAttribute | ISortableRelation;

    @Input() pagination: boolean = true;
    @Input() paginationLimit: number = 10;
    @Input() paginationButtonShift: number;
    @Input() paginationExtendArea: boolean = false;
    @Input() paginationSelector: string;

    @Input() reorderSortByKeys: boolean = false;

    @Input() query: boolean = true;
    @Input() toolbar: boolean = true;
    @Input() search: boolean = true;

    @Input() useMeta: boolean = false;

    @Output() fetching: EventEmitter<boolean> = new EventEmitter<boolean>();
    @Output() response: EventEmitter<Array<Resource>> = new EventEmitter<Array<Resource>>();
    @Output() meta: EventEmitter<object> = new EventEmitter<object>();

    @ContentChild(ListHeaderTemplateDirective, {read: TemplateRef, static: false}) headerTemplate: TemplateRef<any>;
    @ContentChild(ListBodyTemplateDirective, {read: TemplateRef, static: false}) bodyTemplate: TemplateRef<any>;
    @ContentChild(ListBodyStatesDirective, {read: TemplateRef, static: false}) bodyStatesTemplate: TemplateRef<any>;

    public stateActive: EStates = EStates.hasItems;

    public readonly statesAvailable: typeof EStates = EStates;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected listService: ListService,
        protected sortingService: SortingService,
        protected searchService: SearchService
    ) {
        super(cdr);
    }

    get sortable(): ISortable {
        return this.service;
    }

    get sortableDefaultData(): ISortableAttribute | ISortableRelation {
        return this.sortableDefault || this.sortable.getSortableDefault();
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.updateFilterIncludes(this.filterIncludes);
        this.updateFilterRemote(this.filterRemote);
        this.updateFilterBy(this.filterBy);
        this.updatePaginationLimit(this.paginationLimit);
        this.shouldReorderSortByKeys(this.reorderSortByKeys);

        this.listService
            .fetching
            .pipe(takeUntil(this._destroy$))
            .subscribe((value: boolean) => {
                if (value) {
                    this.loadingTrigger();
                } else {
                    this.loadingCompleted();
                }

                this.fetching.next(value);
            });

        this.listService
            .state
            .pipe(
                takeUntil(this._destroy$),
                withLatestFrom(this.listService.response)
            )
            .subscribe(([state, response]: [EStates, Array<Resource>]) => {
                this.stateActive = state;
                this.response.next(response);
            });

        if (this.useMeta) {
            this.listService
                .meta
                .pipe(takeUntil(this._destroy$))
                .subscribe((meta: object) => this.meta.next(meta));
        }

        this.listService.init(
            this.service,
            this.servicePath,
            this.query,
            this.pagination,
            this.useMeta
        );

        if (!this.toolbar) {
            this.sortingService.sortingChanged({
                [this.sortable.getSortableNamespace()]: [(this.sortableDefaultData.defaultDirection === EDirection.DESC ? '-' : '') + this.sortableDefaultData['column']]
            });
        }
    }

    updateFilterIncludes(includes: Array<string>): void {
        this.listService.updateFilterIncludes(includes);
    }

    updateFilterRemote(remotefilter: object): void {
        this.listService.updateFilterRemote(remotefilter);
    }

    updateFilterBy(filterBy: object): void {
        this.listService.updateFilterBy(filterBy);
    }

    updatePaginationLimit(paginationLimit: number): void {
        this.listService.updatePaginationLimit(paginationLimit);
    }

    reloadDataWithCurrentPage(): void {
        this.listService.reloadDataWithCurrentPage();
    }

    shouldReorderSortByKeys(value: boolean): void {
        this.listService.shouldReorderSortByKeys(value);
    }
}
