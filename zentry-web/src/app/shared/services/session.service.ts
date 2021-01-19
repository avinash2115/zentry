import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs/internal/BehaviorSubject';
import { Subject } from 'rxjs/internal/Subject';
import { HttpClient, HttpRequest, HttpResponse } from '@angular/common/http';
import { Observable } from 'rxjs/internal/Observable';
import { catchError, filter, switchMap, take } from 'rxjs/operators';
import { of } from 'rxjs/internal/observable/of';
import { throwError } from 'rxjs/internal/observable/throwError';
import { AuthenticationService } from '../../modules/authentication/authentication.service';

@Injectable({
    providedIn: 'root'
})
export class SessionService {
    private isRefreshing$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);
    private isRefreshed$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private authenticationService: AuthenticationService,
        private http: HttpClient
    ) {
    }

    get isRefreshing(): Observable<boolean> {
        return this.isRefreshing$.asObservable();
    }

    get isRefreshingValue(): boolean {
        return this.isRefreshing$.getValue();
    }

    get isRefreshed(): Observable<boolean> {
        return this.isRefreshed$.asObservable();
    }

    uncontrollable(): Observable<boolean> {
        if (!this.isRefreshNeeded()) {
            return of(true);
        }

        if (this.isRefreshingValue) {
            return this.isRefreshing.pipe(filter((result: boolean) => !result), take(1));
        } else {
            return this.refresh();
        }
    }

    controllable(request: HttpRequest<any>): Observable<boolean> {
        if (this.isLogoutUrl(request) || this.isRefreshTokenUrl(request)) {
            return of(true);
        }

        if (!this.isRefreshNeeded()) {
            return of(true);
        }

        if (this.isRefreshingValue) {
            return this.isRefreshing.pipe(filter((result: boolean) => !result), take(1));
        } else {
            return this.refresh();
        }
    }

    isRefreshNeeded(): boolean {
        return this.authenticationService.isRotationNeeded(300);
    }

    private refresh(): Observable<boolean> {
        this.isRefreshing$.next(true);

        return this.http.post(`${window.endpoints.api}/auth/token/refresh`, null, {
            observe: 'response'
        }).pipe(
            catchError((err: Error) => {
                return throwError(err);
            }),
            switchMap((response: any) => {
                if (response instanceof HttpResponse && response.status === 200) {
                    const refreshedToken: string = (<HttpResponse<any>>response).headers.get('authorization').split(' ')[1];

                    if (!!refreshedToken) {
                        this.authenticationService.rotate(refreshedToken);
                        this.isRefreshed$.next(true);
                    }

                    this.isRefreshing$.next(false);

                    return of(true);
                }
            })
        );
    }

    private isRefreshTokenUrl(request: HttpRequest<any>): boolean {
        return request.url.includes('token/refresh');
    }

    private isLogoutUrl(request: HttpRequest<any>): boolean {
        return request.url.includes('logout');
    }
}
