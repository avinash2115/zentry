import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { ParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';
import { UserService } from '../../user/user.service';
import { filter, switchMap, take } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { RecordedService } from './recorded.service';
import { AuthenticationService } from '../../authentication/authentication.service';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';

@Injectable()
export class RecordedParticipantService implements OnDestroy {
    private _entity$: BehaviorSubject<ParticipantJsonapiResource | null> = new BehaviorSubject<ParticipantJsonapiResource>(null);

    private _selected$: BehaviorSubject<Array<ParticipantJsonapiResource>> = new BehaviorSubject<Array<ParticipantJsonapiResource>>([]);
    private _available$: BehaviorSubject<Array<ParticipantJsonapiResource>> = new BehaviorSubject<Array<ParticipantJsonapiResource>>([]);

    private _http: HttpClient = this._injector.get(HttpClient);
    private _userService: UserService = this._injector.get(UserService);
    private _authenticationService: AuthenticationService = this._injector.get(AuthenticationService);

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _recordedService: RecordedService
    ) {
        this.reboot().subscribe();
    }

    ngOnDestroy(): void {
        this._entity$.complete();
        this._selected$.complete();
        this._available$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get entity(): Observable<ParticipantJsonapiResource | null> {
        return this._entity$.asObservable();
    }

    get entityLoaded(): Observable<ParticipantJsonapiResource> {
        return this.entity.pipe(filter((resource: ParticipantJsonapiResource | null) => resource instanceof ParticipantJsonapiResource), take(1));
    }

    get selected(): Observable<Array<ParticipantJsonapiResource>> {
        return this._selected$.asObservable();
    }

    get available(): Observable<Array<ParticipantJsonapiResource>> {
        return this._available$.asObservable();
    }

    direct(entity: ParticipantJsonapiResource): Observable<ParticipantJsonapiResource> {
        this._entity$.next(entity);

        return of(this._entity$.value);
    }

    release(): Observable<null> {
        this._entity$.next(null);

        return of(null);
    }

    fetchAvailable(dontFilter: boolean = false, dontEmit: boolean = false): Observable<Array<ParticipantJsonapiResource>> {
        if (!this._authenticationService.isAuthorized) {

            if (!dontEmit) {
                this._available$.next([]);
            }

            return of([]);
        }

        return this._authenticationService
            .entityLoaded
            .pipe(
                switchMap(() => this._userService.participantService.list()),
                switchMap((response: Array<ParticipantJsonapiResource>) => {
                    const result: Array<ParticipantJsonapiResource> = response.filter((resource: ParticipantJsonapiResource) => {
                        if (dontFilter) {
                            return true;
                        }

                        return this._selected$.value.findIndex((r: ParticipantJsonapiResource) => r.id === resource.id) === -1;
                    });

                    if (!dontEmit) {
                        this._available$.next(result);
                    }

                    return of(result);
                })
            );
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
                    })
                );
        } else {
            observable = of(entity);
        }

        return observable
            .pipe(
                switchMap((participant: ParticipantJsonapiResource) => {
                    return this._recordedService
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
                                    );
                            }),
                            switchMap(() => of(participant))
                        );
                }),
                switchMap((participant: ParticipantJsonapiResource) => {
                    const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;

                    if (currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === participant.id) === -1) {
                        currentValue.push(participant);

                        this._selected$.next(currentValue);
                    }

                    return this.fetchAvailable().pipe(switchMap(() => of(participant)));
                })
            );
    }

    remove(entity: ParticipantJsonapiResource): Observable<boolean> {
        return this._recordedService
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
                        );
                }),
                switchMap(() => {
                    const currentValue: Array<ParticipantJsonapiResource> = this._selected$.value;
                    const currentIndex: number = currentValue.findIndex((r: ParticipantJsonapiResource) => r.id === entity.id);

                    if (currentIndex !== -1) {
                        currentValue.splice(currentIndex, 1);
                        this._selected$.next(currentValue);
                    }

                    return this.fetchAvailable().pipe(switchMap(() => of(true)));
                })
            );
    }

    reboot(soft: boolean = false): Observable<Array<ParticipantJsonapiResource>> {
        return this._recordedService
            .entityLoaded
            .pipe(
                switchMap((session: SessionJsonapiResource) => {
                    if (!soft) {
                        this._entity$.next(null);
                    }

                    this._selected$.next(session.participants);

                    return this.fetchAvailable();
                })
            );
    }
}
