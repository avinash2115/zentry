import { Injectable, OnDestroy } from '@angular/core';
import { BehaviorSubject, Observable, Subject } from 'rxjs';
import * as moment from 'moment';
import { Observer } from 'rxjs/internal/types';
import { EStates } from './abstractions/base.abstract';
import { EFilterTypes, IFilter } from '../../../../vendor/vp-ngx-jsonapi/interfaces/IFilter';

@Injectable()
export class FilterService implements OnDestroy {
    private state$: BehaviorSubject<EStates> = new BehaviorSubject<EStates>(EStates.close);
    private filters$: BehaviorSubject<object | null> = new BehaviorSubject<object | null>(null);
    private filtersAvailable$: BehaviorSubject<Array<IFilter>> = new BehaviorSubject<Array<IFilter>>([]);
    private filtering$: Subject<boolean> = new Subject<boolean>();
    private preventScroll$: Subject<boolean> = new Subject<boolean>();

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor() {
    }

    get state(): Observable<EStates> {
        return this.state$.asObservable();
    }

    get filters(): Observable<object | null> {
        return this.filters$.asObservable();
    }

    get filtersAvailable(): Observable<Array<IFilter>> {
        return this.filtersAvailable$.asObservable();
    }

    get filtering(): Observable<boolean> {
        return this.filtering$.asObservable();
    }

    get preventScroll(): Observable<boolean> {
        return this.preventScroll$.asObservable();
    }

    ngOnDestroy(): void {
        this._destroy$.next(true);
        this._destroy$.complete();

        this.state$.complete();
        this.filtersAvailable$.complete();
        this.filters$.complete();
        this.filtering$.complete();
        this.preventScroll$.complete();
    }

    init(queryParams: { filters?: object }): Observable<void> {
        return new Observable<void>((observer: Observer<void>) => {
            if (queryParams.filters && Object.keys(queryParams.filters).length) {
                this.filters$.next(queryParams.filters);
            }

            observer.next();
            observer.complete();
        });
    }

    stateChange(value: EStates): void {
        this.state$.next(value);
    }

    filteringChange(value: boolean): void {
        this.filtering$.next(value);
    }

    filtersUpdate(filters: object | null, merge: boolean = false): void {
        if (merge) {
            this.filters$.next(Object.assign({}, this.filters$.value, filters));
        } else {
            this.filters$.next(filters);
        }
    }

    filtersAvailableUpdate(filters: Array<IFilter>): void {
        this.filtersAvailable$.next(filters.sort((a: IFilter, b: IFilter) => a.weight - b.weight));
    }

    filtersUpdateSpecific(attribute: string, type: EFilterTypes, value?: string): void {
        const filters: object | null = this.filters$.value;

        switch (type) {
            case EFilterTypes.select:
                if (Array.isArray(filters[attribute])) {
                    const index: number = (filters[attribute] as Array<string>).findIndex((filterAttr: string) => filterAttr === value);
                    if (index !== -1) {
                        if ((filters[attribute] as Array<string>).length > 1) {
                            (filters[attribute] as Array<string>).splice(index, 1);
                        } else {
                            delete filters[attribute];
                        }
                    }
                } else {
                    delete filters[attribute];
                }
                break;
            case EFilterTypes.datepicker:
                delete filters[attribute];
                break;
        }

        this.filters$.next(Object.keys(filters).length ? filters : null);
    }

    filtersMutate(filters: object): Array<IFilter> {
        return this.filtersAvailable$.value.reduce((result: Array<IFilter>, obj: IFilter) => {
            if (filters.hasOwnProperty(obj.attribute)) {
                const data: IFilter = {attribute: obj.attribute, type: obj.type, label: obj.label, values: []};

                switch (obj.type) {
                    case EFilterTypes.datepicker:
                        data.values.push({label: FilterService.dateMutate(filters[obj.attribute].from), value: ''});
                        data.values.push({label: FilterService.dateMutate(filters[obj.attribute].to), value: ''});
                        break;
                    case EFilterTypes.select:
                        let filterData = filters[obj.attribute];
                        if (!Array.isArray(filterData)) {
                            filterData = [filterData];
                        }
                        data.values = obj.values.filter(
                            (value: { label: string, value: string }) => filterData.includes(value.value)
                        );
                        break;
                }
                result.push(data);
            }
            return result;
        }, []);
    }

    preventScrollChange(value: boolean): void {
        this.preventScroll$.next(value);
    }

    private static dateMutate(date: string): string {
        return moment(date).format('MMM D, YYYY');
    }
}
