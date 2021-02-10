import { Injectable, OnDestroy } from '@angular/core';
import { BehaviorSubject, Observable, Subject } from 'rxjs';
import { Observer } from 'rxjs/internal/types';

@Injectable()
export class SearchService implements OnDestroy {
    private term$: BehaviorSubject<string | null> = new BehaviorSubject<string>(null);
    private destroy$: Subject<boolean> = new Subject<boolean>();

    constructor() {
    }

    get term(): Observable<string | null> {
        return this.term$.asObservable();
    }

    ngOnDestroy(): void {
        this.term$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    init(queryParams: object): Observable<void> {
        return new Observable<void>((observer: Observer<void>) => {
            if (queryParams.hasOwnProperty('term')) {
                console.log(queryParams['term']);
                this.term$.next(queryParams['term']);
            }

            observer.next();
            observer.complete();
        });
    }

    reset(): void {
        this.term$.next(null);
    }
}
