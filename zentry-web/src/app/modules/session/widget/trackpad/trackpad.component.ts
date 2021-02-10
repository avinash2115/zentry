import { ChangeDetectionStrategy, ChangeDetectorRef, Component, EventEmitter, OnInit, Output } from '@angular/core';
import { EType as ESessionPoiType, PoiJsonapiResource } from '../../../../resources/session/poi/poi.jsonapi.service';
import * as moment from 'moment';
import { Moment } from 'moment';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../shared/classes/data-error';
import { BaseAuthorizedComponent } from '../../../../shared/classes/abstracts/component/base-authorized-component';
import { LoaderService } from '../../../../shared/services/loader.service';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { SessionService } from '../../session.service';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { Subscription } from 'rxjs';
import { ParticipantJsonapiResource } from '../../../../resources/session/poi/participant/participant.jsonapi.service';
import { v4 as uuidv4 } from 'uuid';
import { EPOIAction } from '../../session.subscription.service';

@Component({
    selector: 'app-session-widget-trackpad',
    templateUrl: './trackpad.component.html',
    styleUrls: ['./trackpad.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackpadComponent extends BaseAuthorizedComponent implements OnInit {
    @Output() activatedAt: EventEmitter<Moment | null> = new EventEmitter<Moment | null>();

    private type: ESessionPoiType | null = null;
    private visited: ESessionPoiType | null = null;

    public startedAt: Moment | null;

    private session: SessionJsonapiResource | null;

    private participantsAttendance: Array<ParticipantJsonapiResource> = [];
    private participantsSubscription: Subscription | null;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected _sessionService: SessionService,
    ) {
        super(cdr, loaderService, authenticationService);

        cdr.detach();
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    get isRecording(): boolean {
        return !!this.type && this.startedAt !== null;
    }

    get isBacktrackAvailable(): boolean {
        return this.isRecording ? this.startedAt.unix() > this.session.startedAtDate.unix() : true;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        super.initialize(() => {
            this.loadingCompleted();
        });

        this.sessionService
            .poiService
            .live
            .pipe(takeUntil(this._destroy$))
            .subscribe((entity: PoiJsonapiResource | null) => {
                if (entity instanceof PoiJsonapiResource) {
                    switch (entity.poiType) {
                        case ESessionPoiType.backtrack:
                            this.backtrack(entity.startedAt);
                            break;
                        case ESessionPoiType.stopwatch:
                            this.stopwatch(entity.startedAt);
                            break;
                    }
                } else {
                    this.type = null;
                    this.activatedAt.emit(null);

                    this.detectChanges();
                }
            });

        this.sessionService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((entity: SessionJsonapiResource | null) => {
                this.session = entity;
                this.detectChanges();
            });
    }

    isVisited(type: ESessionPoiType): boolean {
        return this.visited === type;
    }

    backtrack(startedAt?: string): void {
        this.participantsWatch();

        this.detectChanges();

        this.visited = ESessionPoiType.backtrack;

        setTimeout(() => {
            this.visited = null;
            this.detectChanges();
        }, 300);

        let pointer: number = moment().unix();

        if (!!startedAt) {
            pointer = moment(startedAt).unix();
        } else {
            if (!this.isRecording) {
                this._sessionService.poiService.whisper(EPOIAction.backtrackStarted, {
                    eventName: EPOIAction.backtrackStarted,
                    actionDate: moment.unix(pointer).utc().format('YYYY-MM-DDTHH:mm:ssZ')
                });
            } else {
                this._sessionService.poiService.whisper(EPOIAction.backtrackStarted, {
                    eventName: EPOIAction.backtrackExtended,
                    actionDate: moment.unix(pointer).utc().format('YYYY-MM-DDTHH:mm:ssZ')
                });
            }
        }

        if (!this.isRecording) {
            this.type = ESessionPoiType.backtrack;
        } else {
            pointer = this.startedAt.unix();
        }

        this.startedAt = moment.unix(pointer - this.authUser.backtrack.backward);

        if (this.startedAt.unix() <= this.session.startedAtDate.unix()) {
            this.startedAt = moment.unix(this.session.startedAtDate.unix());
        }

        this.sessionService.poiService.watch(this.type, this.startedAt.toISOString()).subscribe();

        this.activatedAt.emit(this.startedAt);

        this.detectChanges();
    }

    stopwatch(startedAt?: string): void {
        if (this.isRecording) {
            return;
        }

        this.participantsWatch();

        this.detectChanges();

        this.type = ESessionPoiType.stopwatch;

        if (!!startedAt) {
            this.startedAt = moment(startedAt);
        } else {
            this.startedAt = moment();

            this._sessionService.poiService.whisper(EPOIAction.stopwatchStarted, {
                eventName: EPOIAction.stopwatchStarted,
                actionDate: this.startedAt.utc().format('YYYY-MM-DDTHH:mm:ssZ')
            });
        }

        this.sessionService.poiService.watch(this.type, this.startedAt.toISOString()).subscribe();

        this.activatedAt.emit(this.startedAt);

        this.detectChanges();
    }

    poi(): void {
        this.participantsWatch();

        this.detectChanges();

        this.visited = ESessionPoiType.poi;

        setTimeout(() => {
            this.visited = null;
            this.detectChanges();
        }, 300);

        const pointer: number = Number(moment().format('ss'));

        let startedAt: Moment = moment().set({
            seconds: pointer - this.authUser.poi.backward
        });

        if (startedAt.unix() <= this.session.startedAtDate.unix()) {
            startedAt = this.session.startedAtDate;
        }

        this.activatedAt.emit(startedAt);

        this.detectChanges();

        this.sessionService
            .poiService
            .create(
                ESessionPoiType.poi,
                startedAt.toISOString(),
                moment().set({
                    seconds: pointer + this.authUser.poi.forward
                }).toISOString(),
                this.participantsRelease(this.isRecording),
            )
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                if (!this.isRecording) {
                    this.activatedAt.emit(null);
                }
            }, (error: DataError) => {
                if (!this.isRecording) {
                    this.activatedAt.emit(null);
                }

                this.fallback(error);
            });
    }

    capture(): void {
        if (!this.isRecording) {
            return;
        }

        const typeBuffer: ESessionPoiType = this.type;

        this.type = null;

        this.detectChanges();

        this._sessionService.poiService.whisper(typeBuffer === ESessionPoiType.backtrack ? EPOIAction.backtrackEnded : EPOIAction.stopwatchEnded, {
            eventName: typeBuffer === ESessionPoiType.backtrack ? EPOIAction.backtrackEnded : EPOIAction.stopwatchEnded,
            actionDate: moment().utc().format('YYYY-MM-DDTHH:mm:ssZ')
        });

        this.sessionService
            .poiService
            .create(
                typeBuffer,
                this.startedAt.toISOString(),
                moment().toISOString(),
                this.participantsRelease(),
            )
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                this.activatedAt.emit(null);
            }, (error: DataError) => {
                this.activatedAt.emit(null);
                this.fallback(error);
            });
    }

    private participantsWatch(): void {
        if (this.participantsSubscription instanceof Subscription) {
            return;
        }

        this.participantsSubscription = this.sessionService
            .participantService
            .attached
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<UserParticipantJsonapiResource>) => {
                this.participantsAttendance.forEach((r: ParticipantJsonapiResource) => {
                    if (data.findIndex((p: UserParticipantJsonapiResource) => p.id === r.raw.id) === -1) {
                        r.endedAt = moment().toISOString();
                    }
                });

                data.forEach((r: UserParticipantJsonapiResource) => {
                    const resource: ParticipantJsonapiResource = this.sessionService.sessionPoiParticipantJsonapiService.new();

                    resource.id = uuidv4();
                    resource.startedAt = moment().toISOString();
                    resource.addRelationship(r, 'raw');

                    this.participantsAttendance.push(resource);
                });

                this.detectChanges();
            });
    }

    private participantsRelease(soft: boolean = false): Array<ParticipantJsonapiResource> {
        if (this.participantsSubscription instanceof Subscription) {
            this.participantsSubscription.unsubscribe();
            this.participantsSubscription = null;
        }

        if (soft) {
            return this.participantsAttendance;
        }

        const result: Array<ParticipantJsonapiResource> = this.participantsAttendance.map((r: ParticipantJsonapiResource) => {
            if (!r.endedAt) {
                r.endedAt = moment().toISOString();
            }

            return r;
        });

        this.participantsAttendance = [];

        return result;
    }
}
