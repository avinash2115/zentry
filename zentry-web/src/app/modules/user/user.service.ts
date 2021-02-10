import { Injectable, OnDestroy } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs/internal/Observable';
import { UserJsonapiResource, UserJsonapiService } from '../../resources/user/user.jsonapi.service';
import { map, takeUntil } from 'rxjs/operators';
import { DeviceJsonapiResource, DeviceJsonapiService } from '../../resources/user/device/device.jsonapi.service';
import firstLoadedCollection from '../../shared/operators/first-loaded-collection';
import { ICollection } from '../../../vendor/vp-ngx-jsonapi/interfaces';
import { PoiJsonapiService } from '../../resources/user/poi/poi.jsonapi.service';
import { BacktrackJsonapiService } from '../../resources/user/backtrack/backtrack.jsonapi.service';
import { ConnectingPayloadJsonapiService as DeviceConnectingPayloadJsonapiService } from '../../resources/user/device/connecting-payload/connecting-payload.jsonapi.service';
import { ProfileJsonapiService } from '../../resources/user/profile/profile.jsonapi.service';
import firstLoadedResource from '../../shared/operators/first-loaded-resource';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observer, of } from 'rxjs';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { DataError } from '../../shared/classes/data-error';
import { StorageJsonapiResource, StorageJsonapiService, } from '../../resources/user/storage/storage.jsonapi.service';
import {
    DriverJsonapiResource as StorageDriverJsonapiResource,
    DriverJsonapiService as StorageDriverJsonapiService,
    EDriver
} from '../../resources/user/storage/driver/driver.jsonapi.service';
import { ParticipantService } from './participant/participant.service';
import { TeamService } from './team/team.service';
import { SourceJsonapiService as CrmSourceJsonapiService } from '../../resources/crm/source/source.jsonapi.service';
import { CrmJsonapiResource, CrmJsonapiService} from '../../resources/user/crm/crm.jsonapi.service';
import {
    DriverJsonapiResource as CrmDriverJsonapiResource,
    DriverJsonapiService as CrmDriverJsonapiService,
    EDriver as ECrmDriver
} from '../../resources/user/crm/driver/driver.jsonapi.service';
import { SyncLogJsonapiService as CrmSyncLogJsonapiService } from '../../resources/crm/sync-log/sync-log.jsonapi.service';

@Injectable()
export class UserService implements OnDestroy {
    private _entity$: BehaviorSubject<UserJsonapiResource | null> = new BehaviorSubject<UserJsonapiResource | null>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private http: HttpClient,
        public userJsonapiService: UserJsonapiService,
        public profileJsonapiService: ProfileJsonapiService,
        public poiJsonapiService: PoiJsonapiService,
        public backtrackJsonapiService: BacktrackJsonapiService,
        public deviceJsonapiService: DeviceJsonapiService,
        public deviceConnectingPayloadJsonapiService: DeviceConnectingPayloadJsonapiService,
        public storageJsonapiService: StorageJsonapiService,
        public storageDriverJsonapiService: StorageDriverJsonapiService,
        public crmJsonapiService: CrmJsonapiService,
        public crmDriverJsonapiService: CrmDriverJsonapiService,
        public crmSourceJsonapiService: CrmSourceJsonapiService,
        public crmSyncLogJsonapiService: CrmSyncLogJsonapiService,
        public participantService: ParticipantService,
        public teamService: TeamService
    ) {
    }

    get entity(): Observable<UserJsonapiResource | null> {
        return this._entity$.asObservable();
    }

    get devices(): Observable<Array<DeviceJsonapiResource>> {
        return this.deviceJsonapiService
            .all()
            .pipe(
                firstLoadedCollection(),
                map((data: ICollection<DeviceJsonapiResource>) => {
                    return data.$toArray
                })
            )
    }

    get deviceConnectingQR(): Observable<Blob> {
        const headers: HttpHeaders = new HttpHeaders().set('Accept', 'image/svg+xml');

        return this.http
            .get(`${window.endpoints.api}${this.deviceJsonapiService.path}/qr`, {
                headers: headers,
                responseType: 'blob'
            })
            .pipe(
                map((response: Blob) => {
                    return new Blob([response], {type: 'image/svg+xml'});
                })
            );
    }

    get storages(): Observable<Array<StorageJsonapiResource>> {
        return this.storageJsonapiService.all({
            beforepath: `${this._entity$.value.path}/relationships`,
        }).pipe(
            firstLoadedCollection(),
            map((data: ICollection<StorageJsonapiResource>) => data.$toArray)
        );
    }

    get storagesDrivers(): Observable<Array<StorageDriverJsonapiResource>> {
        return this.storageDriverJsonapiService.all({
            beforepath: `${this._entity$.value.path}/relationships/${this.storageJsonapiService.path}`,
        }).pipe(
            firstLoadedCollection(),
            map((data: ICollection<StorageDriverJsonapiResource>) => data.$toArray)
        );
    }

    get crms(): Observable<Array<CrmJsonapiResource>> {
        return this.crmJsonapiService.all({
            beforepath: `${this._entity$.value.path}/relationships`
        }).pipe(
            firstLoadedCollection(),
            map((data: ICollection<CrmJsonapiResource>) => data.$toArray)
        );
    }

    get crmsDrivers(): Observable<Array<CrmDriverJsonapiResource>> {
        return this.crmDriverJsonapiService.all({
            beforepath: `${this._entity$.value.path}/relationships/crms`
        }).pipe(
            firstLoadedCollection(),
            map((data: ICollection<CrmDriverJsonapiResource>) => data.$toArray)
        );
    }

    ngOnDestroy(): void {
        this._entity$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    get(id: string, includes: Array<string> = ['*']): Observable<UserJsonapiResource> {
        return this.userJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                map((resource: UserJsonapiResource) => {
                    this._entity$.next(resource);

                    return resource;
                })
            );
    }

    save(): Observable<boolean> {
        if (!this._entity$.value.dirty) {
            return of(true);
        }

        return new Observable<boolean>((observer: Observer<boolean>) => {
            this._entity$
                .value
                .save({
                    preserveRelationships: true
                })
                .then((response: IDataObject) => {
                    const resource: UserJsonapiResource = this.userJsonapiService.new();

                    Converter.build(response, resource);

                    this._entity$.next(resource);

                    observer.next(true);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    refresh(): void {
        this
            .get(this._entity$.value.id)
            .pipe(takeUntil(this.destroy$))
            .subscribe((entity: UserJsonapiResource) => {
                this._entity$.next(entity);
            });
    }

    createStorage(driver: EDriver, config: { [key: string]: any }): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            const resource: StorageJsonapiResource = this.storageJsonapiService.new();

            resource
                .customCall({
                    method: 'POST',
                    params: {
                        beforepath: `${this._entity$.value.path}/relationships`,
                    },
                    body: {
                        data: {
                            id: '',
                            type: resource.type,
                            attributes: {
                                driver,
                                config
                            }
                        }
                    }
                })
                .then(() => {
                    observer.next(true);
                    observer.complete;
                }, (error: DataError) => {
                    observer.error(error);
                })
        })
    }

    enableStorage(resource: StorageJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            resource
                .customCall({
                    method: 'POST',
                    params: {
                        afterpath: 'enable'
                    }
                })
                .then(() => {
                    observer.next(true);
                    observer.complete;
                }, (error: DataError) => {
                    observer.error(error);
                })
        })
    }

    createCRM(driver: ECrmDriver, config: { [key: string]: any }): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            const resource: CrmJsonapiResource = this.crmJsonapiService.new();

            resource
                .customCall({
                    method: 'POST',
                    params: {
                        beforepath: `${this._entity$.value.path}/relationships`
                    },
                    body: {
                        data: {
                            id: '',
                            type: resource.type,
                            attributes: {
                                driver,
                                config
                            }
                        }
                    }
                })
                .then(() => {
                    observer.next(true);
                    observer.complete;
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    changeCRM(resource: CrmJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            resource
                .save({},() => {
                    observer.next(true);
                    observer.complete;
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }
}
