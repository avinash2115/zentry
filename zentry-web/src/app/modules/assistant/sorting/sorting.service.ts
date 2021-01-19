import { Injectable, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs';
import { BehaviorSubject } from 'rxjs/internal/BehaviorSubject';
import { Observable } from 'rxjs/internal/Observable';
import { filter } from 'rxjs/operators';
import { Observer } from 'rxjs/internal/types';
import { ISorting } from './abstractions/base.abstract';


@Injectable()
export class SortingService implements OnDestroy {
    private sorting$: BehaviorSubject<ISorting | null> = new BehaviorSubject<{}>(null);
    private destroy$: Subject<boolean> = new Subject<boolean>();

    private _initialValue: ISorting;

    constructor() {
    }

    get sorting(): Observable<ISorting> {
        return this.sorting$.asObservable().pipe(filter((val: ISorting) => !!val));
    }

    get initialValue(): ISorting {
        return this._initialValue;
    }

    ngOnDestroy(): void {
        this.sorting$.complete();
        this.destroy$.next(true);
        this.destroy$.complete();
    }

    init(queryParams: object): Observable<void> {
        return new Observable<void>((observer: Observer<void>) => {
            if (queryParams.hasOwnProperty('sorting')) {
                this._initialValue = queryParams['sorting'];
                this.sorting$.next(queryParams['sorting']);
            }

            observer.next();
            observer.complete();
        });
    }

    sortingChanged(value: ISorting): void {
        this.sorting$.next(value);
    }
}
