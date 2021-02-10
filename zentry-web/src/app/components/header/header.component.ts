import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { UserJsonapiResource } from '../../resources/user/user.jsonapi.service';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { BaseDetachedComponent } from '../../shared/classes/abstracts/component/base-detached-component';
import { filter, takeUntil } from 'rxjs/operators';
import { ActivatedRoute, NavigationEnd, Router, RouterEvent } from '@angular/router';
import { DataError } from '../../shared/classes/data-error';
import { EchoService } from '../../shared/services/echo.service';
import { LoaderService } from '../../shared/services/loader.service';

@Component({
    selector: 'app-header',
    templateUrl: './header.component.html',
    styleUrls: ['./header.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class HeaderComponent extends BaseDetachedComponent implements OnInit {
    public authUser: UserJsonapiResource;

    public term: string | null = null;

    private _currentUrl: string = '';

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected echoService: EchoService,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr);
    }

    get isAuthorized(): boolean {
        return this.authenticationService.isAuthorized;
    }

    ngOnInit(): void {
        this.authenticationService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((user: UserJsonapiResource) => {
                this.authUser = user;

                this.detectChanges();
            });

        this.router
            .events
            .pipe(
                takeUntil(this._destroy$),
                filter((event: RouterEvent) => event instanceof NavigationEnd)
            )
            .subscribe((event: NavigationEnd) => {
                this._currentUrl = event.url;
                this.detectChanges();
            });

        this._currentUrl = this.router.url;
        this.detectChanges();
    }

    logout(): void {
        this.loaderService.show();

        this.authenticationService
            .logout()
            .pipe(takeUntil(this._destroy$))
            .subscribe(({redirectTo}: { redirectTo: string }) => {
                this.loaderService.hide();
                this.router.navigate([redirectTo]);
                this.echoService.disconnect();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    searchAvailable(): boolean {
        return !this._currentUrl.includes('/session/recorded');
    }

    submit(): void {
        this.router.navigate(['/session/recorded'], {queryParams: {term: this.term}});
        this.reset();
    }

    reset(): void {
        this.term = null;
        this.detectChanges();
    }

    termChange(value: string): void {
        this.term = value;
        this.detectChanges();
    }
}
