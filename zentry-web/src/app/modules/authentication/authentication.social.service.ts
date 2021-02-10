import { Injectable } from '@angular/core';
import { GoogleLoginProvider, SocialAuthService, SocialUser } from 'angularx-social-login';
import { Observable, Observer } from 'rxjs';
import {
    DriverJsonapiResource as SSODriverJsonapiResource,
    DriverJsonapiService as SSODriverJsonapiService,
    EDriver
} from '../../resources/sso/driver/driver.jsonapi.service';
import firstLoadedCollection from '../../shared/operators/first-loaded-collection';
import { map, switchMap } from 'rxjs/operators';
import { ICollection } from '../../../vendor/vp-ngx-jsonapi/interfaces';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { HttpClient } from '@angular/common/http';
import { EDrivers } from '../../resources/user/data-provider/driver/driver.jsonapi.service';

@Injectable()
export class AuthenticationSocialService {
    constructor(
        private http: HttpClient,
        private socialAuthService: SocialAuthService,
        private SSODriverJsonapiService: SSODriverJsonapiService,
    ) {
    }

    get drivers(): Observable<Array<SSODriverJsonapiResource>> {
        return this.SSODriverJsonapiService.all({
            beforepath: `auth/sso`,
        }).pipe(
            firstLoadedCollection(),
            map((data: ICollection<SSODriverJsonapiResource>) => data.$toArray)
        );
    }

    authorizeByRaw(driver: EDriver|EDrivers, offlineAccess: boolean = false): Observable<SocialUser> {
        return new Observable<SocialUser>((observer: Observer<SocialUser>) => {
            switch (driver) {
                case EDriver.google:
                case EDrivers.googleCalendar:
                    this.socialAuthService
                        .signIn(GoogleLoginProvider.PROVIDER_ID, {
                            offline_access: offlineAccess,
                            approval_prompt: null
                        })
                        .then((resource: SocialUser) => {
                            observer.next(resource);
                            observer.complete();
                        }, (error: Error) => {
                            console.error(error);
                            observer.error(error);
                        })
                    break;
                default:
                    observer.error(new Error('Provider is not supported'));
                    break;
            }
        });
    }

    authorizeBy(driver: SSODriverJsonapiResource, offlineAccess: boolean = false): Observable<IDataObject> {
        return this.authorizeByRaw(driver.driverType, offlineAccess)
            .pipe(
                switchMap((response: SocialUser) => {
                    const config: { [key: string]: string } = {};

                    driver.config.forEach((key: string) => {
                        config[key] = response[key];
                    });

                    return this
                        .http
                        .post<IDataObject>(`${window.endpoints.api}/auth/sso`, {
                            data: {
                                id: '',
                                type: 'session',
                                attributes: {
                                    driver: driver.driverType,
                                    config
                                }
                            }
                        })
                })
            )
    }
}
