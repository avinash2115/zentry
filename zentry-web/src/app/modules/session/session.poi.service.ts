import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observable, Observer, of, Subscription } from 'rxjs';
import { SessionService } from './session.service';
import { EType, PoiJsonapiResource } from '../../resources/session/poi/poi.jsonapi.service';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { DataError } from '../../shared/classes/data-error';
import { map, switchMap, takeUntil } from 'rxjs/operators';
import { SessionJsonapiResource } from '../../resources/session/session.jsonapi.service';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import {
    EPOIAction,
    EPrivateChannelNames,
    IPOI,
    IPOIWhisper,
    SessionSubscriptionService
} from './session.subscription.service';
import { ParticipantJsonapiResource } from '../../resources/session/poi/participant/participant.jsonapi.service';
import { v4 as uuidv4 } from 'uuid';
import * as moment from 'moment';

@Injectable()
export class SessionPoiService implements OnDestroy {
    public readonly types: typeof EType = EType;

    private _poi$: BehaviorSubject<PoiJsonapiResource | null> = new BehaviorSubject<PoiJsonapiResource>(null);
    private _live$: BehaviorSubject<PoiJsonapiResource | null> = new BehaviorSubject<PoiJsonapiResource>(null);
    private _pois$: BehaviorSubject<Array<PoiJsonapiResource>> = new BehaviorSubject<Array<PoiJsonapiResource>>([]);

    private _subscriptions: Array<Subscription> = [];

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _sessionService: SessionService,
        private _sessionSubscriptionService: SessionSubscriptionService,
    ) {
        this._sessionService
            .entityLoaded
            .subscribe((resource: SessionJsonapiResource) => this._pois$.next(resource.pois))
    }

    ngOnDestroy(): void {
        this._poi$.complete();
        this._live$.complete();
        this._pois$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get poi(): Observable<PoiJsonapiResource | null> {
        return this._poi$.asObservable();
    }

    get live(): Observable<PoiJsonapiResource | null> {
        return this._live$.asObservable();
    }

    get pois(): Observable<Array<PoiJsonapiResource>> {
        return this._pois$
            .asObservable()
            .pipe(
                map((data: Array<PoiJsonapiResource>) => {
                    return data.sort((a: PoiJsonapiResource, b: PoiJsonapiResource) => b.startedAtDate.unix() - a.startedAtDate.unix())
                })
            );
    }

    whisper(action: EPOIAction, event: IPOIWhisper): void {
        this._sessionSubscriptionService.whisper(
            EPrivateChannelNames.view,
            this._sessionService.identity,
            action,
            event
        );
    }

    watch(type: EType, startedAt: string): Observable<PoiJsonapiResource> {
        return new Observable<PoiJsonapiResource>((observer: Observer<PoiJsonapiResource>) => {
            const resource: PoiJsonapiResource = this._sessionService.sessionPoiJsonapiService.new();
            resource.poiType = type;
            resource.startedAt = startedAt;

            this._poi$.next(resource);

            observer.next(resource);
            observer.complete();
        });
    }

    create(type: EType, startedAt: string, endedAt: string, participants: Array<ParticipantJsonapiResource> = []): Observable<PoiJsonapiResource> {
        const resource: PoiJsonapiResource = this._poi$.value instanceof PoiJsonapiResource && type !== EType.poi ? this._poi$.value : this._sessionService.sessionPoiJsonapiService.new();

        resource.poiType = type;
        resource.startedAt = startedAt;
        resource.endedAt = endedAt;
        resource.addRelationshipsArray(participants, 'participants');

        return this._sessionService
            .entityLoaded
            .pipe(
                switchMap((session: SessionJsonapiResource) => {
                    return new Observable<PoiJsonapiResource>((observer: Observer<PoiJsonapiResource>) => {
                        resource.customCall({
                            method: 'POST',
                            params: {
                                beforepath: `${session.path}/relationships`,
                                preserveRelationships: true,
                            }
                        }).then((result: IDataObject) => {
                            if (type !== EType.poi) {
                                this._poi$.next(null);
                            }

                            const resultResource: PoiJsonapiResource = this.hydratePoi(result);

                            const currentValue: Array<PoiJsonapiResource> = this._pois$.value;

                            if (currentValue.findIndex((r: PoiJsonapiResource) => r.id === resultResource.id) === -1) {
                                currentValue.push(resultResource);
                                this._pois$.next(currentValue);
                            }

                            observer.next(resultResource);
                            observer.complete();
                        }, (error: DataError) => {
                            observer.error(error);
                        })
                    })
                })
            );
    }

    edit(entity?: PoiJsonapiResource): Observable<PoiJsonapiResource> {
        if (!(entity instanceof PoiJsonapiResource)) {
            this._sessionSubscriptionService.whisper(EPrivateChannelNames.view, this._sessionService.identity, EPOIAction.activeChanged, {
                eventName: EPOIAction.activeChanged,
                actionDate: moment().utc().format('YYYY-MM-DDTHH:mm:ssZ'),
                name: this._poi$.value.name,
                tags: this._poi$.value.tags,
            })

            return of(this._poi$.value);
        }

        return new Observable<PoiJsonapiResource>((observer: Observer<PoiJsonapiResource>) => {
            entity.customCall({
                method: 'POST',
                params: {
                    preserveRelationships: true,
                }
            }).then((result: IDataObject) => {
                const resultResource: PoiJsonapiResource = this.hydratePoi(result);

                const currentValue: Array<PoiJsonapiResource> = this._pois$.value;
                const currentIndex: number = currentValue.findIndex((r: PoiJsonapiResource) => r.id === resultResource.id);

                if (currentIndex === -1) {
                    currentValue.push(resultResource);
                } else {
                    currentValue[currentIndex] = resultResource;
                }

                this._pois$.next(currentValue);

                observer.next(resultResource);
                observer.complete();
            }, (error: DataError) => {
                observer.error(error);
            })
        });
    }


    remove(entity: PoiJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity.customCall({
                method: 'DELETE',
            }).then(() => {
                const currentValue: Array<PoiJsonapiResource> = this._pois$.value;
                const currentIndex: number = currentValue.findIndex((r: PoiJsonapiResource) => r.id === entity.id);

                if (currentIndex !== -1) {
                    currentValue.splice(currentIndex, 1);
                    this._pois$.next(currentValue);
                }

                observer.next(true);
                observer.complete();
            }, (error: DataError) => {
                observer.error(error);
            })
        });
    }

    reboot(): Observable<Array<PoiJsonapiResource>> {
        this._pois$.next([]);

        return of([]);
    }

    subscribe(): void {
        this._subscriptions
            .push(
                this._sessionSubscriptionService
                    .poi
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((event: IPOI) => {
                        let currentValue: Array<PoiJsonapiResource> = this._pois$.value;
                        let currentIndex: number;

                        switch (event.action) {
                            case EPOIAction.created:
                                currentIndex = currentValue.findIndex((r: PoiJsonapiResource) => r.id === event.resource.id);

                                if (currentIndex === -1) {
                                    currentValue.push(event.resource);
                                }

                                this._pois$.next(currentValue);
                                break;
                            case EPOIAction.changed:
                                currentIndex = currentValue.findIndex((r: PoiJsonapiResource) => r.id === event.resource.id);

                                if (currentIndex === -1) {
                                    currentValue.push(event.resource);
                                } else {
                                    currentValue[currentIndex] = event.resource;
                                }

                                this._pois$.next(currentValue);
                                break;
                            case EPOIAction.removed:
                                currentIndex = currentValue.findIndex((r: PoiJsonapiResource) => r.id === event.resource.id);

                                if (currentIndex !== -1) {
                                    currentValue.splice(currentIndex, 1);
                                }

                                this._pois$.next(currentValue);
                                break;
                            case EPOIAction.backtrackStarted:
                            case EPOIAction.stopwatchStarted:
                            case EPOIAction.backtrackExtended:
                                const resource: PoiJsonapiResource = this._sessionService.sessionPoiJsonapiService.new();
                                resource.id = uuidv4();
                                resource.poiType = event.action === EPOIAction.backtrackStarted || event.action === EPOIAction.backtrackExtended ? EType.backtrack : EType.stopwatch;
                                resource.startedAt = event.whisper.actionDate;

                                this._live$.next(resource);
                                break;
                            case EPOIAction.backtrackEnded:
                            case EPOIAction.stopwatchEnded:
                                this._poi$.next(null);
                                this._live$.next(null);
                                break;
                            case EPOIAction.activeChanged:
                                if (this._poi$.value instanceof PoiJsonapiResource) {
                                    this._poi$.value.name = event.whisper.name;
                                    this._poi$.value.tags = event.whisper.tags;
                                    this._poi$.next(this._poi$.value);
                                }
                                break;
                        }
                    })
            );
    }

    unsubscribe(): void {
        this._subscriptions.forEach((s: Subscription) => s.unsubscribe());
    }

    private hydratePoi(data: IDataObject): PoiJsonapiResource {
        const resource: PoiJsonapiResource = this._sessionService.sessionPoiJsonapiService.new();
        Converter.build(data, resource);
        return resource;
    }
}
