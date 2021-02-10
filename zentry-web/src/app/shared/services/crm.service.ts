import { Injectable } from '@angular/core';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { UserService } from '../../modules/user/user.service';
import { switchMap, take } from 'rxjs/operators';
import { UserJsonapiResource } from '../../resources/user/user.jsonapi.service';
import { CrmJsonapiResource } from '../../resources/user/crm/crm.jsonapi.service';
import { EDriver } from '../../resources/user/crm/driver/driver.jsonapi.service';
import { DataError } from '../classes/data-error';
import { Observable } from 'rxjs/internal/Observable';
import { Observer } from 'rxjs/internal/types';
import { of } from 'rxjs/internal/observable/of';
import { BehaviorSubject } from 'rxjs/internal/BehaviorSubject';
import { EType, SyncLogJsonapiResource } from '../../resources/crm/sync-log/sync-log.jsonapi.service';
import { IDataCollection } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-collection';
import { ICollection } from '../../../vendor/vp-ngx-jsonapi/interfaces';
import { Base } from '../../../vendor/vp-ngx-jsonapi/services/base';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';

@Injectable({
    providedIn: 'root'
})
export class CrmService {
    public readonly types: typeof EType = EType;

    private _entity$: BehaviorSubject<CrmJsonapiResource | null> = new BehaviorSubject<CrmJsonapiResource>(null);
    private _loaded$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);

    constructor(
        private authService: AuthenticationService,
        private userService: UserService
    ) {
    }

    get entity(): Observable<CrmJsonapiResource | null> {
        return this._entity$.asObservable();
    }

    get loaded(): Observable<boolean> {
        return this._loaded$.asObservable();
    }

    get connected(): Observable<boolean> {
        return this.init().pipe(switchMap((r: CrmJsonapiResource | null) => of(r instanceof CrmJsonapiResource)));
    }

    sync(type: EType): Observable<boolean> {
        return this.init()
            .pipe(
                switchMap((r: CrmJsonapiResource | null) => {
                    return new Observable<boolean>((observer: Observer<boolean>) => {
                        r.customCall({
                            method: 'POST',
                            params: {
                                afterpath: `sync/${type}`
                            }
                        }, () => {
                            observer.next(true);
                            observer.complete();
                        }, (error: DataError) => observer.error(error));
                    });
                })
            );
    }

    syncLogs(type: EType): Observable<Array<SyncLogJsonapiResource>> {
        return this.init()
            .pipe(
                switchMap((r: CrmJsonapiResource | null) => {
                    return new Observable<Array<SyncLogJsonapiResource>>((observer: Observer<Array<SyncLogJsonapiResource>>) => {
                        this.userService.crmSyncLogJsonapiService
                            .all({
                                beforepath: `${r.path}`,
                                afterpath: type
                            }, (response: IDataCollection) => {
                                const collection: ICollection<SyncLogJsonapiResource> = Base.newCollection();

                                Converter.build(response, collection);

                                observer.next(collection.$toArray.sort((a: SyncLogJsonapiResource, b: SyncLogJsonapiResource) => (new Date(b.createdAt)).getTime() - (new Date(a.createdAt).getTime())));
                                observer.complete();
                            }, (error: DataError) => observer.error(error));
                    });
                })
            );
    }

    private init(): Observable<CrmJsonapiResource | null> {
        if (this._loaded$.value) {
            return of(this._entity$.value);
        }

        return this.authService
            .entityLoaded
            .pipe(
                take(1),
                switchMap((r: UserJsonapiResource) => {
                    return this.userService.get(r.id);
                }),
                switchMap((r: UserJsonapiResource) => {
                    return this.userService.crms;
                }),
                switchMap((data: Array<CrmJsonapiResource>) => {
                    const resource: CrmJsonapiResource | undefined = data.find((r: CrmJsonapiResource) => r.driver === EDriver.therapylog);

                    if (resource instanceof CrmJsonapiResource) {
                        return of(resource);
                    }

                    return of(null);
                }),
                switchMap((r: CrmJsonapiResource | null) => {
                    this._entity$.next(r);
                    this._loaded$.next(true);

                    return of(r);
                })
            );
    }
}
