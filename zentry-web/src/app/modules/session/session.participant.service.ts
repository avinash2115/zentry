import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observable, Observer, of, forkJoin, Subscription } from 'rxjs';
import { ParticipantJsonapiResource } from '../../resources/user/participant/participant.jsonapi.service';
import { UserService } from '../user/user.service';
import { switchMap, takeUntil } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { SessionService } from './session.service';
import { SessionJsonapiResource } from '../../resources/session/session.jsonapi.service';
import {
    EParticipantAction,
    EPrivateChannelNames,
    IParticipant,
    SessionSubscriptionService
} from './session.subscription.service';
import * as moment from 'moment';

@Injectable()
export class SessionParticipantService implements OnDestroy {
    private _selected$: BehaviorSubject<Array<ParticipantJsonapiResource>> = new BehaviorSubject<Array<ParticipantJsonapiResource>>([]);
    private _attached$: BehaviorSubject<Array<ParticipantJsonapiResource>> = new BehaviorSubject<Array<ParticipantJsonapiResource>>([]);
    private _available$: BehaviorSubject<Array<ParticipantJsonapiResource>> = new BehaviorSubject<Array<ParticipantJsonapiResource>>([]);

    private _subscriptions: Array<Subscription> = [];

    private _http: HttpClient = this._injector.get(HttpClient);
    private _userService: UserService = this._injector.get(UserService);

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _sessionService: SessionService,
        private _sessionSubscriptionService: SessionSubscriptionService,
    ) {
        this._sessionService
            .entityLoaded
            .subscribe((resource: SessionJsonapiResource) => this._selected$.next(resource.participants))
    }

    ngOnDestroy(): void {
        this._selected$.complete();
        this._attached$.complete();
        this._available$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get selected(): Observable<Array<ParticipantJsonapiResource>> {
        return this._selected$.asObservable();
    }

    get attached(): Observable<Array<ParticipantJsonapiResource>> {
        return this._attached$.asObservable();
    }

    get available(): Observable<Array<ParticipantJsonapiResource>> {
        return this._available$.asObservable();
    }

    fetchAvailable(): Observable<Array<ParticipantJsonapiResource>> {
        return this._userService
            .participantService
            .list()
            .pipe(
                switchMap((response: Array<ParticipantJsonapiResource>) => {
                    this._available$.next(response.filter((resource: ParticipantJsonapiResource) => {
                        return this._selected$.value.findIndex((r: ParticipantJsonapiResource) => r.id === resource.id) === -1;
                    }));

                    return of(this._available$.value);
                }),
            );
    }

    start(session: SessionJsonapiResource): Observable<Array<ParticipantJsonapiResource>> {
        if (this._selected$.value.length) {
            return this._http
                .post(
                    `${session.path}/relationships/participants`,
                    {
                        data: this._selected$.value.map((r: ParticipantJsonapiResource) => r.toObject().data)
                    }
                )
                .pipe(
                    switchMap(() => {
                        this._selected$.next(this._selected$.value);

                        return of(this._selected$.value);
                    }),
                );
        }

        return of(this._selected$.value);
    }

    pickup(session: SessionJsonapiResource): Observable<Array<ParticipantJsonapiResource>> {
        this._selected$.next(session.participants)

        return of(this._selected$.value);
    }

    addMultiple(entities: ParticipantJsonapiResource[]): Observable<ParticipantJsonapiResource[]> {
        const observables: Observable<ParticipantJsonapiResource[]> = forkJoin(
            entities.map(entity => {
                if (entity.is_new) {
                    return this._userService
                        .participantService
                        .make()
                        .pipe(
                            switchMap((resource: ParticipantJsonapiResource) => {
                                resource.email = entity.email;
                                resource.firstName = entity.firstName;
                                resource.lastName = entity.lastName;

                                return this._userService.participantService.save();
                            }),
                        );
                } else {
                    return of(entity);
                }
            })
        )

        return observables.pipe(
            switchMap((participants: ParticipantJsonapiResource[]) => {
                if (this._sessionService.isStarted) {
                    return this._sessionService
                        .entityLoaded
                        .pipe(
                            switchMap((session: SessionJsonapiResource) => {
                                return this._http
                                    .post(
                                        `${session.path}/relationships/participants`,
                                        {
                                            data: participants.map(participant => participant.toObject().data)
                                        }
                                    )
                            }),
                            switchMap(() => of(participants)),
                        );
                }

                return of(participants)
            }),
            switchMap((participants: ParticipantJsonapiResource[]) => {
                const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;

                participants.map(participant => {
                    if (currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === participant.id) === -1) {
                        currentValue.push(participant);

                        this._selected$.next(currentValue);
                    }
                })

                return this.fetchAvailable().pipe(switchMap(() => of(participants)))
            })
        )
    }

    add(entity: ParticipantJsonapiResource): Observable<ParticipantJsonapiResource> {
        let observable: Observable<ParticipantJsonapiResource>;

        if (entity.is_new) {
            observable = this._userService
                .participantService
                .make()
                .pipe(
                    switchMap((resource: ParticipantJsonapiResource) => {
                        resource.email = entity.email;
                        resource.firstName = entity.firstName;
                        resource.lastName = entity.lastName;

                        return this._userService.participantService.save();
                    }),
                );
        } else {
            observable = of(entity);
        }

        return observable
            .pipe(
                switchMap((participant: ParticipantJsonapiResource) => {
                    if (this._sessionService.isStarted) {
                        return this._sessionService
                            .entityLoaded
                            .pipe(
                                switchMap((session: SessionJsonapiResource) => {
                                    return this._http
                                        .post(
                                            `${session.path}/relationships/participants`,
                                            {
                                                data: [
                                                    participant.toObject().data
                                                ]
                                            }
                                        )
                                }),
                                switchMap(() => of(participant)),
                            );
                    }

                    return of(participant);
                }),
                switchMap((participant: ParticipantJsonapiResource) => {
                    const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;

                    if (currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === participant.id) === -1) {
                        currentValue.push(participant);

                        this._selected$.next(currentValue);
                    }

                    return this.fetchAvailable().pipe(switchMap(() => of(participant)))
                })
            )
    }

    remove(entity: ParticipantJsonapiResource): Observable<boolean> {
        return of(true)
            .pipe(
                switchMap(() => {
                    if (this._sessionService.isStarted) {
                        return this._sessionService
                            .entityLoaded
                            .pipe(
                                switchMap((session: SessionJsonapiResource) => {
                                    return this._http
                                        .post(
                                            `${session.path}/relationships/participants`,
                                            {
                                                data: [
                                                    entity.toObject().data
                                                ]
                                            }, {
                                                headers: {
                                                    'X-HTTP-METHOD-OVERRIDE': 'DELETE'
                                                }
                                            }
                                        )
                                })
                            );
                    }

                    return of(true);
                }),
                switchMap(() => {
                    const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;
                    const currentIndex: number = currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === entity.id);

                    if (currentIndex !== -1) {
                        currentValue.splice(currentIndex, 1);
                        this._selected$.next(currentValue);
                    }

                    return this._sessionService
                        .entityLoaded
                        .pipe(
                            switchMap((session: SessionJsonapiResource) => {
                                if (moment(session.name).isValid() || session.name.localeCompare(currentValue.map((p: ParticipantJsonapiResource) => p.fullname || p.email).join(', '))) {
                                    if (this._selected$.value.length > 0) {
                                        session.name = this._selected$.value.map((p: ParticipantJsonapiResource) => p.fullname || p.email).join(', ');
                                    } else {
                                        session.name = `${session.startedAtDate.format('MMMM DD YYYY h:mm A')}`
                                    }
                                }

                                return this._sessionService.save(false);
                            }),
                            switchMap(() => this.fetchAvailable()),
                            switchMap(() => of(true))
                        );
                })
            );
    }

    attach(entity: ParticipantJsonapiResource, internal: boolean = false): Observable<ParticipantJsonapiResource> {
        return new Observable<ParticipantJsonapiResource>((observer: Observer<ParticipantJsonapiResource>) => {
            if (this._selected$.value.length && this._selected$.value.findIndex((r: ParticipantJsonapiResource) => r.id === entity.id) !== -1) {
                const currentValue: Array<ParticipantJsonapiResource> = this._attached$.value;

                if (currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === entity.id) === -1) {
                    // currentValue.push(entity);
                    // this._attached$.next(currentValue);

                    this._attached$.next([entity]);

                    if (!internal) {
                        this._sessionSubscriptionService.whisper(EPrivateChannelNames.view, this._sessionService.identity, EParticipantAction.selected, {
                            eventName: EParticipantAction.selected,
                            actionDate: moment().utc().format('YYYY-MM-DDTHH:mm:ssZ'),
                            participantId: entity.id
                        });
                    }
                }

                observer.next(entity);
                observer.complete();
            } else {
                observer.error(new Error('Cannot select'));
            }
        });
    }

    detach(entity: ParticipantJsonapiResource, internal: boolean = false): Observable<ParticipantJsonapiResource> {
        return new Observable<ParticipantJsonapiResource>((observer: Observer<ParticipantJsonapiResource>) => {
            const currentValue: Array<ParticipantJsonapiResource> = this._attached$.value;
            const currentIndex: number = currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === entity.id);

            if (currentIndex !== -1) {
                currentValue.splice(currentIndex, 1);

                this._attached$.next(currentValue);

                if (!internal) {
                    this._sessionSubscriptionService.whisper(EPrivateChannelNames.view, this._sessionService.identity, EParticipantAction.deselected, {
                        eventName: EParticipantAction.deselected,
                        actionDate: moment().utc().format('YYYY-MM-DDTHH:mm:ssZ'),
                        participantId: entity.id
                    });
                }
            }

            observer.next(entity);
            observer.complete();
        });
    }

    reboot(): Observable<Array<ParticipantJsonapiResource>> {
        this._selected$.next([]);
        this._attached$.next([]);

        return this.fetchAvailable();
    }

    subscribe(): void {
        this._subscriptions
            .push(
                this._sessionSubscriptionService
                    .participant
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((event: IParticipant) => {
                        if (event.action === EParticipantAction.added) {
                            const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;

                            if (currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === event.resource.id) === -1) {
                                currentValue.push(event.resource);
                                this._selected$.next(currentValue);
                            }
                        }

                        if (event.action === EParticipantAction.removed) {
                            const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;
                            const currentIndex: number = currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === event.resource.id);

                            if (currentIndex !== -1) {
                                currentValue.splice(currentIndex, 1);
                                this._selected$.next(currentValue);
                            }
                        }

                        if (event.action === EParticipantAction.selected) {
                            const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;
                            const currentIndex: number = currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === event.whisper.participantId);

                            if (currentIndex !== -1) {
                                this.attach(currentValue[currentIndex], true).subscribe();
                            }
                        }

                        if (event.action === EParticipantAction.deselected) {
                            const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;
                            const currentIndex: number = currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === event.whisper.participantId);

                            if (currentIndex !== -1) {
                                this.detach(currentValue[currentIndex], true).subscribe();
                            }
                        }
                    })
            );
    }

    unsubscribe(): void {
        this._subscriptions.forEach((s: Subscription) => s.unsubscribe());
    }
}
