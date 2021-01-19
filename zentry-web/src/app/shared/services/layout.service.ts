import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { Title } from '@angular/platform-browser';

@Injectable({
    providedIn: 'root'
})
export class LayoutService {
    private title$: BehaviorSubject<string> = new BehaviorSubject<string>('Dashboard');
    private backButtonVisibility$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);
    private presentationVisibility$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(true);
    private contentWrap$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(true);

    constructor(
        private titleService: Title,
    ) {
    }

    get title(): Observable<string> {
        return this.title$.asObservable();
    }

    get isBackButtonVisible(): Observable<boolean> {
        return this.backButtonVisibility$.asObservable();
    }

    get isPresentationVisible(): Observable<boolean> {
        return this.presentationVisibility$.asObservable();
    }

    get isContentWrapped(): Observable<boolean> {
        return this.contentWrap$.asObservable();
    }

    showBackButton(): void {
        this.backButtonVisibility$.next(true)
    }

    hideBackButton(): void {
        this.backButtonVisibility$.next(false)
    }

    changeTitle(title: string): void {
        this.title$.next(title);
        this.titleService.setTitle(title);
    }

    hidePresentation(): void {
        this.presentationVisibility$.next(false);
    }

    showPresentation(): void {
        this.presentationVisibility$.next(true);
    }

    wrapContent(): void {
        this.contentWrap$.next(true);
    }

    unwrapContent(): void {
        this.contentWrap$.next(false);
    }
}
