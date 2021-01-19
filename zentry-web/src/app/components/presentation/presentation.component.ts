import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../shared/classes/abstracts/component/base-detached-component';
import { filter, takeUntil } from 'rxjs/operators';
import { LayoutService } from '../../shared/services/layout.service';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { NavigationStart, NavigationEnd, Router, RouterEvent } from '@angular/router';

@Component({
    selector: 'app-presentation',
    templateUrl: './presentation.component.html',
    styleUrls: ['./presentation.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class PresentationComponent extends BaseDetachedComponent implements OnInit {
    public place: string = 'Dashboard';
    public isBackButtonVisible: boolean = false;
    private _previousUrl: string = '';
    private _currentUrl: string = '';

    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService,
        protected router: Router
    ) {
        super(cdr);
    }

    get isAuthorized(): boolean {
        return this.authenticationService.isAuthorized;
    }

    goBack(): void {
        this.router.navigate([this._previousUrl])
    }

    ngOnInit(): void {
        combineLatest([
            this.layoutService.title,
            this.layoutService.isBackButtonVisible
        ])
        .pipe(takeUntil(this._destroy$))
        .subscribe(([title, isBackButtonVisible]) => {
            this.place = title;
            this.isBackButtonVisible = !!this._previousUrl.length && isBackButtonVisible;
            this.detectChanges();
        });
        this.router
            .events
            .pipe(
                takeUntil(this._destroy$),
                filter((event: RouterEvent) => event instanceof NavigationEnd)
            )
            .subscribe((event: NavigationEnd) => {
                if (event.url.split('?')[0] !== this._currentUrl) {
                    this._previousUrl = this._currentUrl;
                    this._currentUrl = event.url.split('?')[0]
                }
                this.detectChanges();
            });
    }
}
