import { Injectable } from '@angular/core';
import {
    ActivatedRouteSnapshot,
    CanActivate,
    CanActivateChild,
    Router,
    RouterStateSnapshot,
    UrlTree
} from '@angular/router';
import { Observable } from 'rxjs';
import { catchError, switchMap, take } from 'rxjs/operators';
import { of } from 'rxjs/internal/observable/of';
import { Observer } from 'rxjs/internal/types';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { SharedService } from '../../modules/shared/shared.service';

@Injectable({
    providedIn: 'root'
})
export class AuthGuard implements CanActivate, CanActivateChild {
    constructor(
        private router: Router,
        private authenticationService: AuthenticationService,
        private sharedService: SharedService,
    ) {
    }

    canActivate(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot
    ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
        return this.isAuthorized(route, state).pipe(
            switchMap(() => of(true)),
            catchError(() => of(false))
        );
    }

    canActivateChild(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot
    ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
        return this.canActivate(route, state);
    }

    private isAuthorized(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot
    ): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.authenticationService
                .load()
                .pipe(take(1))
                .subscribe((value: boolean) => {
                    if (this.sharedService.isSharing && this.sharedService.isAllowed(state.url)) {
                        value = true;
                    }

                    if (!value) {
                        this.router.navigate(['/auth/login']);
                    }

                    if (window.config.native) {
                        if (!/session\/widget/i.test(state.url)) {
                            this.router.navigate(['/session/widget']);

                            value = false;
                        }
                    }

                    observer.next(value);
                    observer.complete();
                });
        });
    }
}
