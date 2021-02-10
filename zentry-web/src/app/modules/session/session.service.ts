import { Injectable, Injector, OnDestroy } from '@angular/core';
import { UserService } from '../user/user.service';
import { SessionJsonapiResource, SessionJsonapiService } from '../../resources/session/session.jsonapi.service';
import { BehaviorSubject, Observable, Observer, of, Subscription, throwError } from 'rxjs';
import { Subject } from 'rxjs/internal/Subject';
import { catchError, filter, map, switchMap, take, takeUntil } from 'rxjs/operators';
import firstLoadedResource from '../../shared/operators/first-loaded-resource';
import { DataError } from '../../shared/classes/data-error';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import {
    PoiJsonapiResource as SessionPoiJsonapiResource,
    PoiJsonapiService as SessionPoiJsonapiService
} from '../../resources/session/poi/poi.jsonapi.service';
import {
    EType as ESessionStreamType,
    StreamJsonapiService as SessionStreamJsonapiService
} from '../../resources/session/stream/stream.jsonapi.service';
import { EPrivateChannelNames, SessionSubscriptionService } from './session.subscription.service';
import { ParticipantJsonapiResource } from '../../resources/user/participant/participant.jsonapi.service';
import { SessionParticipantService } from './session.participant.service';
import { SessionPoiService } from './session.poi.service';
import { ParticipantJsonapiService as SessionPoiParticipantJsonapiService } from '../../resources/session/poi/participant/participant.jsonapi.service';
import { TokenJsonapiService as SessionStreamTokenJsonapiService } from '../../resources/session/stream/token/token.jsonapi.service';
import { NoteJsonapiService as SessionNoteJsonapiService } from '../../resources/session/note/note.jsonapi.service';
import {
    ProgressJsonapiResource as SessionProgressJsonapiResource,
    ProgressJsonapiService as SessionProgressJsonapiService
} from '../../resources/session/progress/progress.jsonapi.service';
import { SessionProgressService } from './session.progress.service';
import firstLoadedCollection from '../../shared/operators/first-loaded-collection';
import { ICollection } from '../../../vendor/vp-ngx-jsonapi/interfaces';
import { SoapJsonapiService as SessionSoapJsonapiService } from '../../resources/session/soap/soap.jsonapi.service';
import { SessionSoapService } from './session.soap.service';
import { ServiceService } from '../service/service.service';
import { ServiceJsonapiResource } from '../../resources/service/service.jsonapi.service';

export enum EReleaseType {
    active,
    dead
}

@Injectable()
export class SessionService implements OnDestroy {
    private _entity$: BehaviorSubject<SessionJsonapiResource | null> = new BehaviorSubject<SessionJsonapiResource | null>(null);
    private _subscriptions: Array<Subscription> = [];

    private _progressService: SessionProgressService | null;
    private _soapService: SessionSoapService | null;
    private _participantService: SessionParticipantService | null;
    private _poiService: SessionPoiService | null;

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _http: HttpClient,
        public userService: UserService,
        public serviceService: ServiceService,
        public sessionJsonapiService: SessionJsonapiService,
        public sessionProgressJsonapiService: SessionProgressJsonapiService,
        public sessionSoapJsonapiService: SessionSoapJsonapiService,
        public sessionStreamJsonapiService: SessionStreamJsonapiService,
        public sessionStreamTokenJsonapiService: SessionStreamTokenJsonapiService,
        public sessionPoiJsonapiService: SessionPoiJsonapiService,
        public sessionPoiParticipantJsonapiService: SessionPoiParticipantJsonapiService,
        public sessionNoteJsonapiService: SessionNoteJsonapiService,
        private _sessionSubscriptionService: SessionSubscriptionService,
    ) {
    }

    get progressService(): SessionProgressService {
        if (!(this._progressService instanceof SessionProgressService)) {
            this.initProgressService();
        }

        return this._progressService;
    }

    get soapService(): SessionSoapService {
        if (!(this._soapService instanceof SessionSoapService)) {
            this.initSoapService();
        }

        return this._soapService;
    }

    get participantService(): SessionParticipantService {
        if (!(this._participantService instanceof SessionParticipantService)) {
            this.initParticipantService();
        }

        return this._participantService;
    }

    get poiService(): SessionPoiService {
        if (!(this._poiService instanceof SessionPoiService)) {
            this.initPoiService();
        }

        return this._poiService;
    }

    get entity(): Observable<SessionJsonapiResource | null> {
        return this._entity$.asObservable();
    }

    get entityLoaded(): Observable<SessionJsonapiResource> {
        return this.entity.pipe(
            filter((session: SessionJsonapiResource | null) => session instanceof SessionJsonapiResource),
            take(1),
        );
    }

    get identity(): string {
        if (!(this._entity$.value instanceof SessionJsonapiResource)) {
            throw new Error('No active session found');
        }

        return this._entity$.value.id;
    }

    get deviceConnectingQR(): Observable<Blob> {
        if (!(this._entity$.value instanceof SessionJsonapiResource) || this._entity$.value.isFinished) {
            console.error(this._entity$.value);
            return throwError(new Error('Your session is not started or already ended'));
        }

        const headers: HttpHeaders = new HttpHeaders().set('Accept', 'image/svg+xml');

        return this._http
            .get(`${window.endpoints.api}${this.sessionJsonapiService.path}/${this._entity$.value.id}/qr`, {
                headers: headers,
                responseType: 'blob'
            })
            .pipe(
                map((response: Blob) => {
                    return new Blob([response], {type: 'image/svg+xml'});
                })
            );
    }

    get isStarted(): boolean {
        return this._entity$.value instanceof SessionJsonapiResource && this._entity$.value.isActive;
    }

    get isFinished(): boolean {
        return this._entity$.value instanceof SessionJsonapiResource && this._entity$.value.isFinished;
    }

    ngOnDestroy(): void {
        this._entity$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get(id: string, includes: Array<string> = ['*']): Observable<SessionJsonapiResource> {
        return this.sessionJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                map((resource: SessionJsonapiResource) => {
                    this._entity$.next(resource);

                    return resource;
                })
            );
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<SessionJsonapiResource>> {
        return this.sessionJsonapiService
            .all({
                include: ['*'],
                remotefilter: filterBy,
                sortBy
            })
            .pipe(
                firstLoadedCollection(),
                map((r: ICollection<SessionJsonapiResource>) => r.$toArray)
            );
    }

    adhoc(name: string, service?: ServiceJsonapiResource | null): Observable<SessionJsonapiResource> {
        return new Observable<SessionJsonapiResource>((observer: Observer<SessionJsonapiResource>) => {
            this.sessionJsonapiService
                .new()
                .customCall(
                    {
                        method: 'POST',
                        postfixPath: 'start',
                        body: {
                            data: {
                                type: this.sessionJsonapiService.type,
                                attributes: {
                                    name
                                },
                                relationships: {
                                    ...(service instanceof ServiceJsonapiResource) && {
                                        service: {
                                            data: service.toObject().data
                                        }
                                    },
                                }
                            }
                        }
                    }
                )
                .then((result: IDataObject) => {
                    observer.next(this.hydrate(result));
                })
                .catch((error: DataError) => {
                    observer.error(error);
                });
        }).pipe(
            catchError((error: DataError) => {
                let observable: Observable<boolean>;

                switch (error.status) {
                    case 424:
                        observable = this.release(EReleaseType.active);
                        break;
                    case 409:
                        observable = this.release(EReleaseType.dead);
                        break;
                    case 423:
                    default:
                        return throwError(error);
                }

                return observable.pipe(switchMap(() => this.adhoc(name, service)));
            }),
            switchMap((resource: SessionJsonapiResource) => {
                return this.participantService
                    .start(resource)
                    .pipe(
                        switchMap((participants: Array<ParticipantJsonapiResource>) => {
                            resource.addRelationshipsArray(participants, 'participants');

                            return of(resource);
                        })
                    );
            }),
            switchMap((resource: SessionJsonapiResource) => {
                this._entity$.next(resource);

                this.subscribe();

                return of(resource);
            }),
            take(1)
        );
    }

    start(entity: SessionJsonapiResource): Observable<SessionJsonapiResource> {
        return new Observable<SessionJsonapiResource>((observer: Observer<SessionJsonapiResource>) => {
            entity
                .customCall(
                    {
                        method: 'POST',
                        postfixPath: 'start',
                        body: {
                            data: {
                                type: this.sessionJsonapiService.type
                            }
                        }
                    }
                )
                .then((result: IDataObject) => {
                    observer.next(this.hydrate(result));
                })
                .catch((error: DataError) => {
                    observer.error(error);
                });
        }).pipe(
            catchError((error: DataError) => {
                let observable: Observable<boolean>;

                switch (error.status) {
                    case 424:
                        observable = this.release(EReleaseType.active);
                        break;
                    case 409:
                        observable = this.release(EReleaseType.dead);
                        break;
                    case 423:
                    default:
                        return throwError(error);
                }

                return observable.pipe(switchMap(() => this.start(entity)));
            }),
            switchMap((resource: SessionJsonapiResource) => {
                return this.participantService
                    .pickup(resource)
                    .pipe(switchMap(() => of(resource)));
            }),
            switchMap((resource: SessionJsonapiResource) => {
                this._entity$.next(resource);

                this.subscribe();

                return of(resource);
            }),
            take(1)
        );
    }

    end(): Observable<SessionJsonapiResource> {
        if (!(this._entity$.value instanceof SessionJsonapiResource) || this._entity$.value.isFinished) {
            return throwError(new Error('Your session is not started or already ended'));
        }

        return new Observable<SessionJsonapiResource>((observer: Observer<SessionJsonapiResource>) => {
            this._entity$
                .value
                .customCall(
                    {
                        method: 'POST',
                        postfixPath: 'end'
                    }
                )
                .then((result: IDataObject) => {
                    this.unsubscribe();

                    this._entity$.next(this.hydrate(result));

                    observer.next(this._entity$.value);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        }).pipe(
            switchMap((session: SessionJsonapiResource) => {
                return this.participantService
                    .reboot()
                    .pipe(
                        switchMap(() => this.progressService.reboot()),
                        switchMap(() => this.soapService.reboot()),
                        switchMap(() => this.poiService.reboot()),
                        switchMap(() => of(session))
                    );
            }),
        );
    }

    merge(session: SessionJsonapiResource, type: ESessionStreamType): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.sessionStreamJsonapiService
                .new()
                .customCall({
                    method: 'POST',
                    params: {
                        beforepath: `${session.path}/relationships`,
                        afterpath: `partial/merge/${type}`
                    }
                })
                .then(() => {
                    observer.next(true);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                })
        });
    }

    wrap(session: SessionJsonapiResource): Observable<SessionJsonapiResource> {
        return new Observable<SessionJsonapiResource>((observer: Observer<SessionJsonapiResource>) => {
            session
                .customCall(
                    {
                        method: 'POST',
                        postfixPath: 'wrap'
                    }
                )
                .then((result: IDataObject) => {
                    observer.next(this.hydrate(result));
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    finish(): void {
        this._entity$.next(null);
    }

    make(): Observable<SessionJsonapiResource> {
        return new Observable<SessionJsonapiResource>((observer: Observer<SessionJsonapiResource>) => {
            const resource: SessionJsonapiResource = this.sessionJsonapiService.new();

            this._entity$.next(resource);

            observer.next(resource);
            observer.complete();
        });
    }

    save(preserve: boolean = true): Observable<SessionJsonapiResource> {
        return new Observable<SessionJsonapiResource>((observer: Observer<SessionJsonapiResource>) => {
            this._entity$
                .value
                .save({
                    afterpath: this._entity$.value.is_new ? 'schedule' : '',
                    preserveRelationships: preserve
                })
                .then(() => {
                    observer.next(this._entity$.value);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    remove(entity: SessionJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity
                .customCall({
                    method: 'DELETE'
                })
                .then(() => {
                    observer.next(true);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    private active(): Observable<SessionJsonapiResource> {
        return this.sessionJsonapiService
            .get('active', {
                include: ['*']
            })
            .pipe(firstLoadedResource());
    }

    private dead(): Observable<SessionJsonapiResource> {
        return this.sessionJsonapiService
            .get('dead', {
                include: ['*']
            })
            .pipe(firstLoadedResource());
    }

    private release(type: EReleaseType): Observable<boolean> {
        let observable: Observable<SessionJsonapiResource>;

        switch (type) {
            case EReleaseType.active:
                observable = this.active();
                break;
            case EReleaseType.dead:
                observable = this.dead();
                break;
            default:
                return throwError(new Error('Passed unknown release type'));
        }

        return observable.pipe(
            switchMap((resource: SessionJsonapiResource) => {
                return new Observable<boolean>((observer: Observer<boolean>) => {
                    resource.customCall({
                        method: 'DELETE'
                    }).then(() => {
                        observer.next(true);
                        observer.complete();
                    }, (error: DataError) => {
                        observer.error(error);
                    });
                })
            })
        );
    }

    private hydrate(data: IDataObject): SessionJsonapiResource {
        const resource: SessionJsonapiResource = this.sessionJsonapiService.new();
        Converter.build(data, resource);
        return resource;
    }

    private subscribe(): void {
        this._subscriptions.push(this._sessionSubscriptionService
            .changes
            .pipe(takeUntil(this._destroy$))
            .subscribe((session: SessionJsonapiResource) => {
                this._entity$.next(session);
            }));

        this._subscriptions.push(this._sessionSubscriptionService
            .ended
            .pipe(takeUntil(this._destroy$))
            .subscribe((session: SessionJsonapiResource) => {
                this._entity$.next(session);
            }));

        this._subscriptions.push(this._sessionSubscriptionService
            .wrapped
            .pipe(takeUntil(this._destroy$))
            .subscribe((session: SessionJsonapiResource) => {
                this._entity$.next(session);
            }));

        this._subscriptions.push(this.participantService
            .selected
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                if (this.isStarted) {
                    this._entity$.value.participants.forEach((r: ParticipantJsonapiResource) => {
                        this._entity$.value.removeRelationship('participants', r.id);
                    });

                    this._entity$.value.addRelationshipsArray(data, 'participants');

                    this._entity$.next(this._entity$.value);
                }
            }));

        this._subscriptions.push(this.poiService
            .pois
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<SessionPoiJsonapiResource>) => {
                if (this.isStarted) {
                    this._entity$.value.pois.forEach((r: SessionPoiJsonapiResource) => {
                        this._entity$.value.removeRelationship('pois', r.id);
                    });

                    this._entity$.value.addRelationshipsArray(data, 'pois');

                    this._entity$.next(this._entity$.value);
                }
            }));

        this._subscriptions.push(this.progressService
            .list
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<SessionProgressJsonapiResource>) => {
                if (this.isStarted) {
                    this._entity$.value.progress.forEach((r: SessionProgressJsonapiResource) => {
                        this._entity$.value.removeRelationship('progress', r.id);
                    });

                    this._entity$.value.addRelationshipsArray(data, 'progress');

                    this._entity$.next(this._entity$.value);
                }
            }));

        this.participantService.subscribe();
        this.poiService.subscribe();
        this.progressService.subscribe();

        this._sessionSubscriptionService.subscribe(EPrivateChannelNames.view, this._entity$.value.id);
    }

    private unsubscribe(): void {
        this._sessionSubscriptionService.unsubscribe(EPrivateChannelNames.view, this._entity$.value.id);
        this._subscriptions.forEach((s: Subscription) => s.unsubscribe());

        this.participantService.unsubscribe();
        this.poiService.unsubscribe();
        this.progressService.unsubscribe();
    }

    private initProgressService(): void {
        this._progressService = new SessionProgressService(this._injector, this, this._sessionSubscriptionService);
    }

    private initSoapService(): void {
        this._soapService = new SessionSoapService(this._injector, this);
    }

    private initParticipantService(): void {
        this._participantService = new SessionParticipantService(this._injector, this, this._sessionSubscriptionService);
    }

    private initPoiService(): void {
        this._poiService = new SessionPoiService(this._injector, this, this._sessionSubscriptionService);
    }
}
