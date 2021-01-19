import { Injectable, Injector, OnDestroy } from '@angular/core';
import { BehaviorSubject, Observable, Observer, of, Subscription } from 'rxjs';
import { Subject } from 'rxjs/internal/Subject';
import { filter, map, switchMap, take, takeUntil } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import firstLoadedCollection from '../../../shared/operators/first-loaded-collection';
import { ICollection } from '../../../../vendor/vp-ngx-jsonapi/interfaces';
import firstLoadedResource from '../../../shared/operators/first-loaded-resource';
import { StreamJsonapiResource as SessionStreamJsonapiResource } from '../../../resources/session/stream/stream.jsonapi.service';
import { DataError } from '../../../shared/classes/data-error';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import {
    UrlJsonapiResource as FileTemporaryUrlJsonapiResource,
    UrlJsonapiService as FileTemporaryUrlJsonapiService
} from '../../../resources/file/temporary/url/url.jsonapi.service';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { EPrivateChannelNames, RecordedSubscriptionService } from './recorded.subscription.service';
import { AuthenticationService } from '../../authentication/authentication.service';
import { SharedService } from '../../shared/shared.service';
import { SharedJsonapiResource } from '../../../resources/shared/shared.jsonapi.service';
import { Resource } from '../../../../vendor/vp-ngx-jsonapi';
import { RecordedParticipantService } from './recorded.participant.service';
import { RecordedPoiService } from './recorded.poi.service';
import { ParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';
import { UserService } from '../../user/user.service';
import { fromPromise } from 'rxjs/internal-compatibility';
import { TranscriptJsonapiService } from '../../../resources/transcript/transcript.jsonapi.service';
import { WordJsonapiService } from '../../../resources/transcript/word/word.jsonapi.service';
import { PhraseJsonapiService } from '../../../resources/transcript/phrase/phrase.jsonapi.service';
import { EStatus, SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';
import { SessionService } from '../session.service';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../resources/session/poi/poi.jsonapi.service';
import { TokenJsonapiResource as SessionStreamTokenJsonapiResource } from '../../../resources/session/stream/token/token.jsonapi.service';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../../../resources/session/poi/participant/participant.jsonapi.service';
import { RecordedSoapService } from './recorded.soap.service';
import { RecordedNoteService } from './recorded.note.service';
import { NoteJsonapiResource } from '../../../resources/session/note/note.jsonapi.service';
import { GoalJsonapiResource } from 'src/app/resources/user/participant/goal/goal.jsonapi.service';

@Injectable()
export class RecordedService implements OnDestroy {
    private _entity$: BehaviorSubject<SessionJsonapiResource | null> = new BehaviorSubject<SessionJsonapiResource | null>(null);

    private _subscriptions: Array<Subscription> = [];

    private _soapService: RecordedSoapService | null;
    private _noteService: RecordedNoteService | null;
    private _participantService: RecordedParticipantService | null;
    private _poiService: RecordedPoiService | null;

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _http: HttpClient,
        private _authenticationService: AuthenticationService,
        private _sharedService: SharedService,
        public userService: UserService,
        public sessionService: SessionService,
        public transcriptJsonapiService: TranscriptJsonapiService,
        public wordJsonapiService: WordJsonapiService,
        public phraseJsonapiResource: PhraseJsonapiService,
        public fileTemporaryUrlJsonapiService: FileTemporaryUrlJsonapiService,
        private _recordedSubscriptionService: RecordedSubscriptionService
    ) {
    }

    get soapService(): RecordedSoapService {
        if (!(this._soapService instanceof RecordedSoapService)) {
            this.initSoapService();
        }

        return this._soapService;
    }

    get noteService(): RecordedNoteService {
        if (!(this._noteService instanceof RecordedNoteService)) {
            this.initNoteService();
        }

        return this._noteService;
    }

    get participantService(): RecordedParticipantService {
        if (!(this._participantService instanceof RecordedParticipantService)) {
            this.initParticipantService();
        }

        return this._participantService;
    }

    get poiService(): RecordedPoiService {
        if (!(this._poiService instanceof RecordedPoiService)) {
            this.initPoiService();
        }

        return this._poiService;
    }

    get entity(): Observable<SessionJsonapiResource | null> {
        return this._entity$.asObservable();
    }

    get isLocked(): boolean {
        return this._entity$.value.isLocked
    }

    get entityLoaded(): Observable<SessionJsonapiResource> {
        return this.entity.pipe(filter((resource: SessionJsonapiResource | null) => resource instanceof SessionJsonapiResource), take(1));
    }

    ngOnDestroy(): void {
        this._entity$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    direct(entity: SessionJsonapiResource): void {
        this._entity$.next(entity);

        this.participantService.reboot().pipe(take(1)).subscribe();
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<SessionJsonapiResource>> {
        filterBy['statuses'] = {
            collection: [EStatus.wrapped]
        };

        return this.sessionService
            .sessionJsonapiService
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

    get(id: string, includes: Array<string> = ['*'], poiId?: string): Observable<SessionJsonapiResource> {
        return this.sessionService
            .sessionJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                switchMap((r: SessionJsonapiResource) => {
                    if (this._entity$.value instanceof SessionJsonapiResource && this._entity$.value.id !== r.id) {
                        this._recordedSubscriptionService.unsubscribe(EPrivateChannelNames.view, this._entity$.value.id);
                    }

                    if (!!poiId) {
                        const poi: SessionPoiJsonapiResource | undefined = r.poiById(poiId);

                        if (poi instanceof SessionPoiJsonapiResource) {
                            r.pois.forEach((p: SessionPoiJsonapiResource) => {
                                r.removeRelationship('pois', p.id);
                            });

                            r.addRelationship(poi, 'pois');
                        }
                    }

                    if (!this._authenticationService.isAuthorized || this._authenticationService.identity !== r.user.id) {
                        r.forceReadonly();
                    }

                    if (!r.readonly) {
                        if (!(this._entity$.value instanceof SessionJsonapiResource) || this._entity$.value.id !== r.id) {
                            this._entity$.next(r);
                            this._recordedSubscriptionService.subscribe(EPrivateChannelNames.view, r.id);
                            this.subscribe();
                        } else {
                            this._entity$.next(r);
                        }
                    } else {
                        this._entity$.next(r);
                    }

                    return of(r);
                })
            );
    }

    share(entity?: SessionPoiJsonapiResource, remove: boolean = false): Observable<string | null> {
        return new Observable<string>((observer: Observer<string>) => {
            let resource: Resource = this._entity$.value;

            if (entity instanceof SessionPoiJsonapiResource) {
                resource = entity;
            }

            resource
                .customCall({
                    method: remove ? 'DELETE' : 'POST',
                    postfixPath: remove ? 'unshare' : 'share'
                })
                .then((response: IDataObject) => {
                    if (!remove) {
                        const resource: SharedJsonapiResource = this._sharedService.sharedJsonapiService.new();

                        Converter.build(response, resource);

                        observer.next(this._sharedService.build(resource));
                    } else {
                        observer.next(null);
                    }

                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
            }).pipe(
                switchMap((url: string) => {
                    this.refresh(true);

                    return of(url);
                })
            );
    }

    documentSession(sign: string | null): Observable<boolean> {
            const resource: SessionJsonapiResource = this._entity$.value;

            resource.sign = sign

            return this.save();
    }

    excludeGoal(goal: GoalJsonapiResource): Observable<boolean> {
            const resource: SessionJsonapiResource = this._entity$.value;

            resource.excludedGoals = [...resource.excludedGoals, goal.id]

            return this.save();
    }

    streamVideoURL(entity: SessionStreamJsonapiResource): Observable<string> {
        return new Observable<string>((observer: Observer<string>) => {
            entity
                .customCall({
                    method: 'GET',
                    postfixPath: 'token'
                })
                .then((response: IDataObject) => {
                    const resource: SessionStreamTokenJsonapiResource = this.sessionService.sessionStreamTokenJsonapiService.new();

                    Converter.build(response, resource);

                    observer.next(`${entity.path}/token/${resource.token}`);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    streamDownloadURL(entity: SessionStreamJsonapiResource): Observable<FileTemporaryUrlJsonapiResource> {
        return new Observable<FileTemporaryUrlJsonapiResource>((observer: Observer<FileTemporaryUrlJsonapiResource>) => {
            entity
                .customCall({
                    method: 'GET',
                    postfixPath: 'temporary_url'
                })
                .then((response: IDataObject) => {
                    const resource: FileTemporaryUrlJsonapiResource = this.fileTemporaryUrlJsonapiService.new();

                    Converter.build(response, resource);

                    observer.next(resource);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    poiVideoURL(entity: SessionPoiJsonapiResource): Observable<string> {
        return new Observable<string>((observer: Observer<string>) => {
            entity
                .customCall({
                    method: 'GET',
                    postfixPath: 'token'
                })
                .then((response: IDataObject) => {
                    const resource: SessionStreamTokenJsonapiResource = this.sessionService.sessionStreamTokenJsonapiService.new();

                    Converter.build(response, resource);

                    observer.next(`${entity.path}/token/${resource.token}`);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
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
                    const resource: SessionJsonapiResource = this.sessionService.sessionJsonapiService.new();

                    Converter.build(response, resource);

                    this._entity$.next(resource);

                    observer.next(true);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    refresh(force: boolean = false): void {
        if (this._entity$.value.dirty || force) {
            this
                .get(this._entity$.value.id)
                .pipe(
                    switchMap(() => this.poiService.reboot()),
                    switchMap(() => this.noteService.reboot())
                )
                .subscribe();
        }
    }

    private subscribe(): void {
        this.subscriptionsFlush();

        this._subscriptions.push(this._recordedSubscriptionService
            .streamConvertProgress
            .pipe(takeUntil(this._destroy$))
            .subscribe((entity: SessionStreamJsonapiResource) => {
                this._entity$.value.addRelationship(entity, 'streams');
                this._entity$.next(this._entity$.value);
            }));

        this._subscriptions.push(this.participantService
            .selected
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                this._entity$.value.participants.forEach((r: ParticipantJsonapiResource) => {
                    this._entity$.value.removeRelationship('participants', r.id);
                });

                this._entity$.value.addRelationshipsArray(data, 'participants');

                this._entity$.next(this._entity$.value);
            }));

        this._subscriptions.push(this.poiService
            .pois
            .pipe(
                takeUntil(this._destroy$),
                switchMap((data: Array<SessionPoiJsonapiResource>) => {
                    this._entity$.value.pois.forEach((r: SessionPoiJsonapiResource) => {
                        this._entity$.value.removeRelationship('pois', r.id);
                    });

                    this._entity$.value.addRelationshipsArray(data, 'pois');

                    const isMissedParticipant: boolean = this._entity$.value.pois.some((r: SessionPoiJsonapiResource) => {
                        return r.participants.some((p: SessionPoiParticipantJsonapiResource) => {
                            return this._entity$.value.participants.findIndex((rp: ParticipantJsonapiResource) => rp.id === p.raw.id) === -1;
                        });
                    });

                    if (isMissedParticipant) {
                        return fromPromise(this._entity$.value.reloadResource({include: ['*']}));
                    }

                    return of(this._entity$.value);
                }),
                switchMap(() => {
                    this._entity$.next(this._entity$.value);

                    return this.participantService.reboot(true);
                })
            ).subscribe());

        this._subscriptions.push(this.noteService
            .list
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<NoteJsonapiResource>) => {
                this._entity$.value.notes.forEach((r: NoteJsonapiResource) => {
                    this._entity$.value.removeRelationship('notes', r.id);
                });

                this._entity$.value.addRelationshipsArray(data, 'notes');

                this._entity$.next(this._entity$.value);
            }));

        this.poiService.subscribe();
    }

    private subscriptionsFlush(): void {
        this._subscriptions.forEach((s: Subscription) => {
            s.unsubscribe();
        });

        this.poiService.unsubscribe();
    }

    private initSoapService(): void {
        this._soapService = new RecordedSoapService(this._injector, this);
    }

    private initNoteService(): void {
        this._noteService = new RecordedNoteService(this._injector, this);
    }

    private initParticipantService(): void {
        this._participantService = new RecordedParticipantService(this._injector, this);
    }

    private initPoiService(): void {
        this._poiService = new RecordedPoiService(this._injector, this, this._recordedSubscriptionService);
    }
}
