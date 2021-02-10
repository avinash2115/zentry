import { Injectable } from '@angular/core';
import {
    HttpErrorResponse,
    HttpEvent,
    HttpHandler,
    HttpInterceptor,
    HttpRequest,
    HttpResponse
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { HeaderService } from '../services/header.service';
import { catchError, switchMap, tap } from 'rxjs/operators';
import { throwError } from 'rxjs/internal/observable/throwError';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { SessionService } from '../services/session.service';
import { iif } from 'rxjs/internal/observable/iif';
import { Observer } from 'rxjs/internal/types';
import { DataError, IDataError } from '../classes/data-error';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { Router } from '@angular/router';
import { UtilsService } from '../services/utils.service';

@Injectable()
export class JsonapiInterceptor implements HttpInterceptor {

    constructor(
        private headerService: HeaderService,
        private sessionService: SessionService,
        private authenticationService: AuthenticationService,
        private router: Router
    ) {
    }

    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        if (UtilsService.isExternalUrl(request.url)) {
            return next.handle(request);
        }

        request = this.headerService.setRequestHeaders(request);

        return this.sessionService
            .controllable(request)
            .pipe(
                switchMap((result: boolean) => iif<HttpEvent<any>>(
                    () => result,
                    next.handle(this.headerService.setRequestHeaders(request))
                        .pipe(
                            tap((response: HttpResponse<IDataObject>) => {
                                if (response.body && !response.body.data) {
                                    return throwError(new Error('Unknown error. Response came back without data attribute'));
                                }
                            }),
                            catchError((error: HttpErrorResponse) => {
                                if (error.status === 0) {
                                    return throwError(new Error('Check you internet connection. Seems like you are offline!'));
                                }

                                if (error.status === 401) {
                                    if (request.url.includes('logout')) {
                                        this.router.navigate(['/auth/login']);
                                        this.authenticationService.terminate();
                                    } else {
                                        this.authenticationService
                                            .logout()
                                            .subscribe(({redirectTo}: { redirectTo: string }) => {
                                                this.router.navigate([redirectTo]);
                                                this.authenticationService.terminate();
                                            }, (error: DataError) => {
                                                throwError(error);
                                            });
                                    }
                                    return;
                                }

                                if (error.error instanceof Blob) {
                                    const errorBlob: Blob = new Blob([error.error], {type: error.error.type});
                                    const fr: FileReader = new FileReader();
                                    return new Observable<any>((observer: Observer<any>) => {
                                        fr.onload = (e: ProgressEvent) => {
                                            const dataError: { data: IDataError } = JSON.parse((e.target as any).result);
                                            if (dataError.hasOwnProperty('data')) {
                                                observer.error(new DataError(dataError.data.id, dataError.data.status, dataError.data.title, dataError.data.detail, dataError.data.meta));
                                            } else {
                                                observer.error(new Error('Unknown error'));
                                            }
                                        };
                                        fr.readAsText(errorBlob);
                                    });
                                } else {
                                    const dataError: { data: IDataError } = error.error;
                                    return throwError(new DataError(dataError.data.id, dataError.data.status, dataError.data.title, dataError.data.detail, dataError.data.meta));
                                }
                            })
                        ),
                    throwError(new Error())
                    )
                )
            );
    }
}
