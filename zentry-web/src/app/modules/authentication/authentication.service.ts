import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Observable } from 'rxjs/internal/Observable';
import { IAcknowledgeResponse } from '../../shared/interfaces/acknowledge-response.interface';
import {
    PasswordResetJsonapiResource,
    PasswordResetJsonapiService
} from '../../resources/helpers/password/reset-jsonapi.service';
import { Observer } from 'rxjs/internal/types';
import firstLoadedResource from '../../shared/operators/first-loaded-resource';
import { UserJsonapiResource } from '../../resources/user/user.jsonapi.service';
import { filter, map, switchMap, take } from 'rxjs/operators';
import { of } from 'rxjs/internal/observable/of';
import { throwError } from 'rxjs/internal/observable/throwError';
import { JwtHelperService } from '@auth0/angular-jwt';
import { BehaviorSubject } from 'rxjs';
import { UserService } from '../user/user.service';
import {
    TokenJsonapiResource as LoginTokenJsonapiResource,
    TokenJsonapiService as LoginTokenJsonapiService
} from '../../resources/login/token/token.jsonapi.service';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { AuthenticationSocialService } from './authentication.social.service';
import { DriverJsonapiResource as SSODriverJsonapiResource } from '../../resources/sso/driver/driver.jsonapi.service';

@Injectable({
    providedIn: 'root'
})
export class AuthenticationService {
    private entity$: BehaviorSubject<UserJsonapiResource> = new BehaviorSubject<UserJsonapiResource>(null);

    constructor(
        private http: HttpClient,
        private router: Router,
        private authenticationSocialService: AuthenticationSocialService,
        private loginTokenJsonapiService: LoginTokenJsonapiService,
        private passwordResetJsonapiService: PasswordResetJsonapiService,
        private userService: UserService,
    ) {
    }

    get entity(): Observable<UserJsonapiResource> {
        return this.entity$.asObservable().pipe(filter((u: UserJsonapiResource | null) => u instanceof UserJsonapiResource));
    }

    get entityLoaded(): Observable<UserJsonapiResource> {
        return this.entity.pipe(take(1));
    }

    get identity(): string {
        if (this.isLoaded) {
            return this.entity$.getValue().id;
        }

        return '';
    }

    get isLoaded(): boolean {
        return this.entity$.getValue() instanceof UserJsonapiResource;
    }

    get isAuthorized(): boolean {
        if (this.isRotationNeeded()) {
            this.terminate();
        }

        return !!this.token;
    }

    get token(): string | null {
        return localStorage.getItem('token');
    }

    load(): Observable<boolean> {
        if (!this.isAuthorized) {
            return of(false);
        }

        if (this.isLoaded) {
            return of(true);
        }

        return this.userService
            .userJsonapiService
            .get('current', {
                include: ['*']
            })
            .pipe(
                firstLoadedResource(),
                map((u: UserJsonapiResource) => {
                    this.entity$.next(u);

                    return true;
                })
            );
    }

    reload(): void {
        this.userService
            .userJsonapiService
            .get('current', {
                include: ['*']
            })
            .pipe(
                firstLoadedResource(),
            )
            .subscribe((u: UserJsonapiResource) => {
                this.entity$.next(u);
            });
    }

    isRotationNeeded(offset?: number): boolean {
        const token: string | null = this.token;
        const helper: JwtHelperService = new JwtHelperService();
        return token && helper.isTokenExpired(token, offset);
    }

    rotate(token: string): void {
        this.authorize(token);
    }

    terminate(): void {
        localStorage.removeItem('token');
        this.entity$.next(null);
    }

    login(email: string, password: string, remember: boolean): Observable<{ redirectTo: string } | Error> {
        if (this.isAuthorized) {
            return this.throwError('User already logged in');
        }

        const loginData: IDataObject = {data: {type: 'session'}};
        loginData.data.attributes = {email, password, remember};

        return this.http
            .post<IDataObject>(`${window.endpoints.api}/auth/login`, loginData)
            .pipe(
                switchMap(({data}: IDataObject) => {
                    this.authorize(data.attributes.token);
                    return of({redirectTo: '/dashboard'});
                })
            );
    }

    social(driver: SSODriverJsonapiResource): Observable<{ redirectTo: string } | Error> {
        if (this.isAuthorized) {
            return this.throwError('User already logged in');
        }

        return this.authenticationSocialService
            .authorizeBy(driver)
            .pipe(
                switchMap(({data}: IDataObject) => {
                    this.authorize(data.attributes.token);
                    return of({redirectTo: '/dashboard'});
                })
            );
    }

    logout(): Observable<{ redirectTo: string } | Error> {
        if (!this.isAuthorized) {
            return this.throwError('User isn`t logged in');
        }

        return this.http
            .get(`${window.endpoints.api}/auth/logout`)
            .pipe(
                switchMap((acknowledge: IAcknowledgeResponse) => {
                    this.terminate();
                    return of({redirectTo: '/auth/login'});
                })
            );
    }

    registration(data: IDataObject): Observable<{ redirectTo: string } | Error> {
        if (this.isAuthorized) {
            return this.throwError('User already logged in');
        }

        return this.http.post<IDataObject>(`${window.endpoints.api}/auth/signup`, data)
            .pipe(
                switchMap(({data}: IDataObject) => {
                    this.authorize(data.attributes.token);
                    return of({redirectTo: '/dashboard'});
                })
            );
    }

    requestResetPassword(resource: PasswordResetJsonapiResource): Observable<IAcknowledgeResponse> {
        return new Observable((observer: Observer<IAcknowledgeResponse>) => {
            resource.customCall({
                method: 'POST', params: {applyPathToResource: true}
            }).then((response: IAcknowledgeResponse) => {
                observer.next(response);
                observer.complete();
            }, (error: Error) => {
                observer.error(error);
            });
        });
    }

    getResetPasswordById(id: string): Observable<PasswordResetJsonapiResource> {
        return this.passwordResetJsonapiService.get(id)
            .pipe(firstLoadedResource<PasswordResetJsonapiResource>());
    }

    resetPassword(resource: PasswordResetJsonapiResource): Observable<{ redirectTo: string }> {
        return new Observable((observer: Observer<{ redirectTo: string }>) => {
            resource.save()
                .then(({data}: IDataObject) => {
                    this.authorize(data.attributes.token);
                    observer.next({redirectTo: '/dashboard'});
                    observer.complete();
                }, (error: Error) => {
                    observer.error(error);
                });
        });
    }

    generateLoginToken(): Observable<LoginTokenJsonapiResource> {
        const requestData: IDataObject = {data: {type: 'login_tokens'}};

        requestData.data.attributes = {
            referer: 'widget'
        };

        return this.http
            .post(`${window.endpoints.api}/auth/login/token`, requestData)
            .pipe(
                map((response: IDataObject) => {
                    const resource: LoginTokenJsonapiResource = this.loginTokenJsonapiService.new();

                    Converter.build(response, resource);

                    return resource;
                })
            );
    }

    authorizeLoginToken(id: string): Observable<{ redirectTo: string } | Error> {
        const requestData: IDataObject = {data: {type: 'login_tokens'}};

        requestData.data.attributes = {
            referer: document.referrer || 'widget'
        };

        return this.http
            .post<IDataObject>(`${window.endpoints.api}/auth/login/token/${id}`, requestData)
            .pipe(
                switchMap(({data}: IDataObject) => {
                    this.authorize(data.attributes.token);
                    return of({redirectTo: '/dashboard'});
                })
            );
    }

    private authorize(token: string): void {
        localStorage.setItem('token', token);
    }

    private throwError(msg: string): Observable<Error> {
        console.error(msg);
        return throwError(new Error(msg));
    }
}
