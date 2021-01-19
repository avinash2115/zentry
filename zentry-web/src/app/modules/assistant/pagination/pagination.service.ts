import { Injectable, OnDestroy } from '@angular/core';
import { Observer } from 'rxjs/internal/types';
import { Observable } from 'rxjs/internal/Observable';
import { BehaviorSubject } from 'rxjs/internal/BehaviorSubject';
import { filter } from 'rxjs/operators';
import { EResponseAction, IBagState, IMetaState, IPageState } from './abstractions/base.abstract';

@Injectable()
export class PaginationService implements OnDestroy {
    private bag$: BehaviorSubject<IBagState | null> = new BehaviorSubject<IBagState>(null);
    private page$: BehaviorSubject<IPageState | null> = new BehaviorSubject<IPageState>(null);

    private processing$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);

    private _metaState: IMetaState = {
        page: 1
    };

    constructor() {
    }

    get bag(): Observable<IBagState> {
        return this.bag$.asObservable().pipe(filter((bag: IBagState) => !!bag));
    }

    get page(): Observable<IPageState | null> {
        return this.page$.asObservable();
    }

    get processing(): Observable<boolean> {
        return this.processing$.asObservable();
    }

    ngOnDestroy(): void {
        this.page$.complete();
        this.bag$.complete();
        this.processing$.complete();
    }

    init(queryParams: object): Observable<void> {
        return new Observable<void>((observer: Observer<void>) => {
            if (queryParams.hasOwnProperty('page')) {
                let page: number = Number(queryParams['page']);
                page = page && !isNaN(page) ? page : 1;

                this.page$.next({
                    page: page,
                    silent: false,
                    emit: true,
                    action: EResponseAction.reload
                });
            } else {
                this.page$.next({page: 1, silent: false, emit: true, action: EResponseAction.reload});
            }

            observer.next();
            observer.complete();
        });
    }

    updateBag(limit: number, page: number, force: boolean = false): void {
        let currentValue: IBagState | null = this.bag$.value;

        if (!currentValue || force) {
            currentValue = {
                limit: limit,
                page: page,
                pages: [page]
            };

            this.bag$.next(currentValue);

            this._metaState.page = page;

            this.page$.next({
                page: this._metaState.page,
                emit: true,
                silent: true,
                action: EResponseAction.append
            });
        } else {
            currentValue.limit = limit;

            if (this.lastPage() === page) {
                this.page$.next({
                    page: this._metaState.page,
                    emit: true,
                    silent: true,
                    action: EResponseAction.append
                });
            }

            currentValue.page = page;

            if (currentValue.pages.indexOf(page) === -1) {
                currentValue.pages.push(page);
                currentValue.pages = currentValue.pages.sort((a: number, b: number) => a - b);
            }

            this.bag$.next(currentValue);
        }
    }

    limit(): number {
        return this.bag$.value.limit;
    }

    reachedTop(): boolean {
        return this._metaState.page === 1;
    }

    firstPage(): number {
        return this.bag$.value.pages.length > 0 ? this.bag$.value.pages[0] : 1;
    }

    lastPage(): number {
        return this.bag$.value.pages.length > 0 ? this.bag$.value.pages[this.bag$.value.pages.length - 1] : 1;
    }

    processingChange(value: boolean): void {
        this.processing$.next(value);
    }

    up(): void {
        if (!this.processing$.value) {
            this.page$.next({
                page: this.firstPage() <= 1 ? 1 : this.firstPage() - 1,
                emit: false,
                silent: false,
                action: EResponseAction.prepend
            });
        }
    }

    down(): void {
        if (!this.processing$.value) {
            this.page$.next({
                page: this.lastPage() + 1,
                emit: false,
                silent: false,
                action: EResponseAction.append
            });
        }
    }

    upSoft(page: number): void {
        if (page < 1) {
            page = 1;
        }

        if (page < this.firstPage()) {
            page = this.firstPage();
        }

        if (page < this._metaState.page) {
            this._metaState.page = page;
            this.page$.next({
                page: this._metaState.page,
                emit: true,
                silent: true,
                action: EResponseAction.append
            });
        }
    }

    downSoft(page: number): void {
        if (page < 1) {
            page = 1;
        }

        if (page <= this._metaState.page) {

            page = page + 1;

            if (page > this.lastPage()) {
                page = this.lastPage();
            }

            this._metaState.page = page;

            this.page$.next({
                page: this._metaState.page,
                emit: true,
                silent: true,
                action: EResponseAction.append
            });
        }
    }

    goToFirstPage(): void {
        if (!this.processing$.value) {
            this.bag$.next(null);
            this.page$.next({
                page: 1,
                emit: true,
                silent: false,
                action: EResponseAction.reload
            });
        }
    }

    reloadCurrentPage(): void {
        const {page}: IBagState = this.bag$.value;
        this.bag$.next(null);
        this.page$.next({
            page: page,
            emit: true,
            silent: false,
            action: EResponseAction.reload
        });
    }
}
