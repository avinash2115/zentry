import { Injectable, OnDestroy } from '@angular/core';
import { BaseList, EStates, IParameters, IServicePath } from './abstractions/base.abstract';
import { EResponseAction, IPageState } from '../pagination/abstractions/base.abstract';
import { UtilsService } from '../../../shared/services/utils.service';
import { BehaviorSubject, combineLatest, Observable, of, Subject } from 'rxjs';
import { ICollection, Resource } from '../../../../vendor/vp-ngx-jsonapi';
import { FilterService } from '../filter/filter.service';
import { SortingService } from '../sorting/sorting.service';
import { SearchService } from '../search/search.service';
import { PaginationService } from '../pagination/pagination.service';
import { filter, flatMap, switchMap, take, takeUntil } from 'rxjs/operators';
import { ISorting } from '../sorting/abstractions/base.abstract';
import { fromPromise } from 'rxjs/internal-compatibility';
import { QueryParamsService } from '../../../shared/services/query-params.service';
import firstLoadedCollection from '../../../shared/operators/first-loaded-collection';

@Injectable()
export class ListService implements OnDestroy {
    private _service: BaseList;
    private _servicePath: IServicePath = {};

    private _params: IParameters = {
        action: EResponseAction.reload,
        silent: false,
        filterBy: {
            emit: true,
            data: {}
        },
        sortBy: {
            emit: true,
            data: {}
        },
        term: {
            emit: true,
            data: ''
        },
        page: {
            emit: true,
            data: 1
        }
    };

    private _lastParams: IParameters = UtilsService.deepObjectsClone(this._params);

    private _filterIncludes: Array<string> = ['*'];
    private _filterRemote: object = {};
    private _paginationLimit: number = 10;
    private _shouldReorderSortByKeys: boolean = false;
    private _useMeta: boolean;

    private _response$: BehaviorSubject<Array<Resource>> = new BehaviorSubject<Array<Resource>>([]);
    private _meta$: BehaviorSubject<object> = new BehaviorSubject<object>(null);
    private _state$: BehaviorSubject<EStates> = new BehaviorSubject<EStates>(null);

    private _fetching$: Subject<boolean> = new Subject<boolean>();
    private _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private filterService: FilterService,
        private sortingService: SortingService,
        private searchService: SearchService,
        private paginationService: PaginationService,
        private queryParamsService: QueryParamsService
    ) {
    }

    get response(): Observable<Array<Resource>> {
        return this._response$.asObservable();
    }

    get fetching(): Observable<boolean> {
        return this._fetching$.asObservable();
    }

    get meta(): Observable<object> {
        return this._meta$.asObservable().pipe(filter((meta: object) => !!meta));
    }

    get state(): Observable<EStates> {
        return this._state$.asObservable().pipe(filter((state: EStates) => !!state));
    }

    ngOnDestroy(): void {
        this._response$.complete();
        this._fetching$.complete();
        this._meta$.complete();
        this._state$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    init(
        service: BaseList,
        servicePath: IServicePath = {},
        modifyQuery: boolean = true,
        pagination: boolean = true,
        useMeta: boolean = false
    ): void {
        this._service = service;
        this._servicePath = servicePath;
        this._useMeta = useMeta;

        const clearPagination: () => void = () => {
            this._params.action = EResponseAction.reload;
            this._params.silent = false;
            this._params.page.data = 1;
            this._params.page.emit = true;
        };

        this.queryParamsService
            .init()
            .pipe(
                switchMap((params: object) => combineLatest([
                    this.filterService.init(params),
                    this.sortingService.init(params),
                    this.searchService.init(params),
                    this.paginationService.init(params)
                ]).pipe(take(1))),
                switchMap(() => combineLatest([
                    this.filterService.filters,
                    this.sortingService.sorting,
                    this.searchService.term
                ]).pipe(take(1))),
                switchMap(() => {
                    return combineLatest([
                        this.filterService.filters.pipe(flatMap((filterBy: object) => {
                            this._params.filterBy.data = filterBy;
                            clearPagination();
                            return of(filterBy);
                        })),
                        this.sortingService.sorting.pipe(flatMap((sortBy: ISorting) => {
                            this._params.sortBy.data = sortBy;
                            clearPagination();
                            return of(sortBy);
                        })),
                        this.searchService.term.pipe(flatMap((term: string) => {
                            this._params.term.data = term;
                            clearPagination();
                            return of(term);
                        })),
                        this.paginationService.page.pipe(flatMap((pageState: IPageState) => {
                            this._params.action = pageState.action;
                            this._params.silent = pageState.silent;
                            this._params.page.emit = pageState.emit;
                            this._params.page.data = pageState.page;
                            return of(pageState);
                        }))
                    ]).pipe(flatMap(() => of(this._params)));
                }),
                flatMap((params: IParameters) => {
                    if (!modifyQuery) {
                        return of(params);
                    }

                    return fromPromise(
                        this.queryParamsService
                            .update(
                                params.filterBy.emit ? params.filterBy.data : this._lastParams.filterBy.data,
                                params.sortBy.emit ? params.sortBy.data : this._lastParams.sortBy.data,
                                params.term.emit ? params.term.data : this._lastParams.term.data,
                                pagination ? (params.page.emit ? params.page.data : this._lastParams.page.data) : null
                            )
                    ).pipe(flatMap(() => of(params)));
                }),
                takeUntil(this._destroy$)
            )
            .subscribe((params: IParameters) => {
                this._lastParams = UtilsService.deepObjectsClone(params);

                if (!params.silent) {
                    this.fetchData(pagination);
                }
            }, (error: Error) => console.error(error));
    }

    updateFilterIncludes(includes: Array<string>): void {
        this._filterIncludes = includes;
    }

    updateFilterRemote(remotefilter: object): void {
        this._filterRemote = remotefilter;
    }

    updateFilterBy(filterBy: object, merge: boolean = true): void {
        this.filterService.filtersUpdate(filterBy, merge);
    }

    updatePaginationLimit(paginationLimit: number): void {
        this._paginationLimit = paginationLimit;
    }

    shouldReorderSortByKeys(value: boolean): void {
        this._shouldReorderSortByKeys = value;
    }

    fetchData(pagination: boolean = true): void {
        switch (this._params.action) {
            case EResponseAction.append:
            case EResponseAction.prepend:
                this.toggleLoaders(false, true);
                break;
            default:
                this.toggleLoaders(true, false);
                break;
        }

        this._service
            .all(
                {
                    filterBy: this._params.filterBy.data,
                    sortBy: this._params.sortBy.data,
                    sortByReorderKey: this._shouldReorderSortByKeys && this._service.getSortableNamespace(),
                    search: this._params.term.data,
                    pagination: pagination ? {page: this._params.page.data, limit: this._paginationLimit} : {
                        page: 1,
                        limit: this._paginationLimit
                    },
                    include: this._filterIncludes,
                    remotefilter: this._filterRemote,
                    ...this._servicePath
                }
            )
            .pipe(firstLoadedCollection())
            .subscribe((response: ICollection) => {
                switch (this._params.action) {
                    case EResponseAction.append: {
                        this._response$.next(
                            [...this._response$.getValue(), ...response.$toArray]
                                .filter((r: Resource, index: number, self: Array<Resource>) => {
                                    return self.findIndex((rs: Resource) => rs.id === r.id) === index;
                                })
                        );
                        break;
                    }
                    case EResponseAction.prepend: {
                        this._response$.next(
                            [...response.$toArray, ...this._response$.getValue()]
                                .filter((r: Resource, index: number, self: Array<Resource>) => {
                                    return self.findIndex((rs: Resource) => rs.id === r.id) === index;
                                })
                        );
                        break;
                    }
                    default: {
                        this._response$.next(response.$toArray);
                        break;
                    }
                }

                this.changeState();

                if (this._useMeta) {
                    this._meta$.next(response.meta);
                }

                this._params.silent = false;

                this.toggleLoaders(false, false);

                this.filterService.filtersAvailableUpdate(response.filters);

                this.paginationService.updateBag(response.pagination.limit, response.pagination.page, this._params.action === EResponseAction.reload);
            }, (error: Error) => {
                console.error(error);

                this.toggleLoaders(false, false);
            });
    }

    reloadDataWithCurrentPage(): void {
        this.paginationService.reloadCurrentPage();
    }

    resetQueryParams(): void {
        this.filterService.filtersUpdate(null, false);
        this.searchService.reset();
    }

    private toggleLoaders(globalLoaders: boolean, paginationLoaders: boolean): void {
        this._fetching$.next(globalLoaders);

        this.filterService.filteringChange(globalLoaders);

        this.paginationService.processingChange(paginationLoaders);
    }

    private changeState(): void {
        if (this._response$.getValue().length !== 0) {
            this._state$.next(EStates.hasItems);
        } else {
            const isFilter: boolean = this._params.filterBy.data && Object.keys(this._params.filterBy.data).length !== 0;
            const isSearch: boolean = !!this._params.term.data;

            switch (true) {
                case isFilter && isSearch:
                    this._state$.next(EStates.blankByFiltersSearch);
                    break;
                case isFilter:
                    this._state$.next(EStates.blankByFilters);
                    break;
                case isSearch:
                    this._state$.next(EStates.blankBySearch);
                    break;
                default:
                    this._state$.next(EStates.blank);
                    break;
            }
        }
    }
}
