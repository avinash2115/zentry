import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observable, Observer, of, Subscription } from 'rxjs';
import { filter, map, switchMap, takeUntil } from 'rxjs/operators';
import {
    EType,
    PoiJsonapiResource,
    PoiJsonapiResource as SessionPoiJsonapiResource
} from '../../../resources/session/poi/poi.jsonapi.service';
import { RecordedService } from './recorded.service';
import { EPOIAction, IPOI, RecordedSubscriptionService } from './recorded.subscription.service';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { DataError } from '../../../shared/classes/data-error';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../../../resources/session/poi/participant/participant.jsonapi.service';
import { ParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';
import { HttpClient } from '@angular/common/http';
import { fromPromise } from 'rxjs/internal-compatibility';
import { UserService } from '../../user/user.service';
import { RecordedPoiTranscriptService } from './recorded.poi.transcript.service';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';

@Injectable()
export class RecordedPoiService implements OnDestroy {
    public readonly types: typeof EType = EType;

    private _pois$: BehaviorSubject<Array<SessionPoiJsonapiResource>> = new BehaviorSubject<Array<SessionPoiJsonapiResource>>([]);

    private _subscriptions: Array<Subscription> = [];

    private _http: HttpClient = this._injector.get(HttpClient);
    private _userService: UserService = this._injector.get(UserService);

    private _transcriptService: RecordedPoiTranscriptService | null;

    private _loaded: boolean = false;

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _recordedService: RecordedService,
        private _recordedSubscriptionService: RecordedSubscriptionService,
    ) {
        this.reboot().subscribe();
    }

    ngOnDestroy(): void {
        this._pois$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get transcriptService(): RecordedPoiTranscriptService {
        if (!(this._transcriptService instanceof RecordedPoiTranscriptService)) {
            this.initTranscriptService();
        }

        return this._transcriptService;
    }

    get pois(): Observable<Array<PoiJsonapiResource>> {
        return this._pois$
            .asObservable()
            .pipe(
                filter(() => this._loaded),
                map((data: Array<SessionPoiJsonapiResource>) => {
                    return data.sort((a: SessionPoiJsonapiResource, b: SessionPoiJsonapiResource) => a.startedAtDate.unix() - b.startedAtDate.unix())
                })
            );
    }

    edit(entity: SessionPoiJsonapiResource): Observable<SessionPoiJsonapiResource> {
        return new Observable<SessionPoiJsonapiResource>((observer: Observer<SessionPoiJsonapiResource>) => {
            entity
                .save()
                .then((response: IDataObject) => {
                    const resource: SessionPoiJsonapiResource = this._recordedService.sessionService.sessionPoiJsonapiService.new();

                    Converter.build(response, resource);

                    const currentValue: Array<SessionPoiJsonapiResource> = this._pois$.value;
                    const currentIndex: number = currentValue.findIndex((r: SessionPoiJsonapiResource) => r.id === entity.id);

                    if (currentIndex !== -1) {
                        currentValue[currentIndex] = resource;
                    }

                    this._pois$.next(currentValue);

                    observer.next(resource);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    remove(entity: SessionPoiJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity
                .customCall({
                    method: 'DELETE'
                })
                .then(() => {
                    const currentValue: Array<SessionPoiJsonapiResource> = this._pois$.value;
                    const currentIndex: number = currentValue.findIndex((r: SessionPoiJsonapiResource) => r.id === entity.id);

                    if (currentIndex !== -1) {
                        currentValue.splice(currentIndex, 1);
                    }

                    this._pois$.next(currentValue);

                    observer.next(true);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    participantCreate(entity: SessionPoiJsonapiResource, poiParticipant: SessionPoiParticipantJsonapiResource): Observable<PoiJsonapiResource> {
        const currentValue: Array<PoiJsonapiResource> = this._pois$.value;
        const resourceIndex: number = currentValue.findIndex((r: PoiJsonapiResource) => r.id === entity.id);

        if (resourceIndex === -1) {
            throw new Error('Provided POI cannot be found');
        }

        const resource: SessionPoiJsonapiResource = currentValue[resourceIndex];

        let observable: Observable<SessionPoiParticipantJsonapiResource>;

        if (poiParticipant.raw.is_new) {
            observable = this._userService
                .participantService
                .make()
                .pipe(
                    switchMap((r: ParticipantJsonapiResource) => {
                        r.email = poiParticipant.raw.email;
                        r.firstName = poiParticipant.raw.firstName;
                        r.lastName = poiParticipant.raw.lastName;

                        return this._userService.participantService.save();
                    }),
                    switchMap((r: ParticipantJsonapiResource) => {
                        poiParticipant.raw.id = r.id;
                        poiParticipant.raw.is_new = false;

                        return of(poiParticipant);
                    })
                );
        } else {
            observable = of(poiParticipant);
        }

        return observable
            .pipe(
                switchMap((r: SessionPoiParticipantJsonapiResource) => {
                    return this._http
                        .post(
                            `${resource.path}/relationships/participants`,
                            {
                                data: [
                                    r.toObject().data
                                ]
                            },
                        )
                }),
                switchMap(() => fromPromise(resource.reloadResource({include: ['*']}))),
                switchMap(() => {
                    currentValue[resourceIndex] = resource;

                    this._pois$.next(currentValue);

                    return of(resource);
                }),
            )
    }

    participantRemove(entity: PoiJsonapiResource, poiParticipant: SessionPoiParticipantJsonapiResource): Observable<PoiJsonapiResource> {
        const currentValue: Array<PoiJsonapiResource> = this._pois$.value;
        const resourceIndex: number = currentValue.findIndex((r: PoiJsonapiResource) => r.id === entity.id);

        if (resourceIndex === -1) {
            throw new Error('Provided POI cannot be found');
        }

        const resource: SessionPoiJsonapiResource = currentValue[resourceIndex];

        return this._http
            .post(
                `${resource.path}/relationships/participants`,
                {
                    data: [
                        poiParticipant.toObject().data
                    ]
                }, {
                    headers: {
                        'X-HTTP-METHOD-OVERRIDE': 'DELETE'
                    }
                }
            )
            .pipe(
                switchMap(() => {
                    resource.removeRelationship('participants', poiParticipant.id)

                    currentValue[resourceIndex] = resource;

                    this._pois$.next(currentValue);

                    return of(resource);
                }),
            )
    }

    reboot(): Observable<Array<SessionPoiJsonapiResource>> {
        return this._recordedService
            .entityLoaded
            .pipe(
                map((session: SessionJsonapiResource) => {
                    this._loaded = true;
                    this._pois$.next(session.pois);

                    return this._pois$.value;
                })
            );
    }

    subscribe(): void {
        this._subscriptions
            .push(
                this._recordedSubscriptionService
                    .poi
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((event: IPOI) => {
                        const currentValue: Array<PoiJsonapiResource> = this._pois$.value;
                        const currentIndex: number = currentValue.findIndex((r: SessionPoiJsonapiResource) => r.id === event.resource.id);

                        if (event.action === EPOIAction.changed) {
                            if (currentIndex !== -1) {
                                currentValue[currentIndex] = event.resource;
                            }
                        }

                        if (event.action === EPOIAction.removed) {
                            if (currentIndex !== -1) {
                                currentValue.splice(currentIndex, 1);
                            }
                        }

                        this._pois$.next(currentValue);
                    })
            );
    }

    unsubscribe(): void {
        this._subscriptions.forEach((s: Subscription) => s.unsubscribe());
    }

    private initTranscriptService(): void {
        this._transcriptService = new RecordedPoiTranscriptService(this._injector, this._recordedService, this);
    }
}
