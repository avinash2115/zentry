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
import { catchError, switchMap } from 'rxjs/operators';
import { of } from 'rxjs/internal/observable/of';
import { Observer } from 'rxjs/internal/types';
import { AuthenticationService } from '../../modules/authentication/authentication.service';

@Injectable({
    providedIn: 'root'
})
export class GuestGuard implements CanActivate, CanActivateChild {
    constructor(
        private router: Router,
        private authenticationService: AuthenticationService
    ) {
    }

    canActivate(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot
    ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
        return this.isAuthorized().pipe(
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

    private isAuthorized(): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.authenticationService
                .load()
                .subscribe((value: boolean) => {
                    if (value) {
                        if (window.config.native) {
                            this.router.navigate(['/session/widget']);
                        } else {
                            this.router.navigate(['/dashboard']);
                        }
                    }

                    observer.next(value);
                    observer.complete();
                })
        });
    }
}
