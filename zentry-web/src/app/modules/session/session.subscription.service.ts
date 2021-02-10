import { Injectable, OnDestroy } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { SocketIoPrivateChannel } from 'laravel-echo/dist/channel';
import { EchoService } from '../../shared/services/echo.service';
import { filter, take } from 'rxjs/operators';
import { AuthenticationService } from '../authentication/authentication.service';
import { BehaviorSubject, Observable } from 'rxjs';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { SessionJsonapiResource, SessionJsonapiService } from '../../resources/session/session.jsonapi.service';
import {
    PoiJsonapiResource as SessionPoiJsonapiResource,
    PoiJsonapiService as SessionPoiJsonapiService
} from '../../resources/session/poi/poi.jsonapi.service';
import {
    ParticipantJsonapiResource as UserParticipantJsonapiResource,
    ParticipantJsonapiService as UserParticipantJsonapiService
} from '../../resources/user/participant/participant.jsonapi.service';
import {
    ProgressJsonapiResource as SessionProgressJsonapiResource,
    ProgressJsonapiService as SessionProgressJsonapiService
} from '../../resources/session/progress/progress.jsonapi.service';
import { ITag } from '../../shared/interfaces/tag/tag.interface';

export enum EPrivateChannelNames {
    view = 'users-{userIdentity}.sessions-{sessionIdentity}',
}

export enum EParticipantAction {
    added = 'added',
    removed = 'removed',
    selected = 'participant_selected',
    deselected = 'participant_deselected',
}

export interface IParticipantWhisper {
    eventName: string,
    actionDate: string,
    participantId: string
}

export interface IParticipant {
    action: EParticipantAction
    resource?: UserParticipantJsonapiResource,
    whisper?: IParticipantWhisper
}

export enum EPOIAction {
    created = 'created',
    changed = 'changed',
    removed = 'removed',
    backtrackStarted = 'backtrack_started',
    backtrackEnded = 'backtrack_ended',
    backtrackExtended = 'backtrack_extended',
    stopwatchStarted = 'stopwatch_started',
    stopwatchEnded = 'stopwatch_ended',
    activeChanged = 'active_poi_changed',
}

export interface IPOIWhisper {
    eventName: string,
    actionDate: string,
    name?: string,
    tags?: Array<ITag>
}

export interface IPOI {
    action: EPOIAction
    resource?: SessionPoiJsonapiResource,
    whisper?: IPOIWhisper
}

export enum EProgressAction {
    created = 'created',
    removed = 'removed'
}

export interface IProgress {
    action: EProgressAction
    resource: SessionProgressJsonapiResource
}

@Injectable()
export class SessionSubscriptionService implements OnDestroy {
    private privateChannels: Array<SocketIoPrivateChannel> = [];

    private changes$: BehaviorSubject<SessionJsonapiResource | null> = new BehaviorSubject<SessionJsonapiResource>(null);
    private ended$: BehaviorSubject<SessionJsonapiResource | null> = new BehaviorSubject<SessionJsonapiResource>(null);
    private wrapped$: BehaviorSubject<SessionJsonapiResource | null> = new BehaviorSubject<SessionJsonapiResource>(null);
    private participant$: BehaviorSubject<IParticipant | null> = new BehaviorSubject<IParticipant>(null);
    private poi$: BehaviorSubject<IPOI | null> = new BehaviorSubject<IPOI>(null);
    private progress$: BehaviorSubject<IProgress | null> = new BehaviorSubject<IProgress>(null);

    constructor(
        private http: HttpClient,
        private echoService: EchoService,
        private authenticationService: AuthenticationService,
        private sessionJsonapiService: SessionJsonapiService,
        private sessionPoiJsonapiService: SessionPoiJsonapiService,
        private sessionProgressJsonapiService: SessionProgressJsonapiService,
        private userParticipantJsonapiService: UserParticipantJsonapiService,
    ) {
    }

    get changes(): Observable<SessionJsonapiResource> {
        return this.changes$.asObservable().pipe(filter((v: SessionJsonapiResource | null) => v instanceof SessionJsonapiResource));
    }

    get ended(): Observable<SessionJsonapiResource> {
        return this.ended$.asObservable().pipe(filter((v: SessionJsonapiResource | null) => v instanceof SessionJsonapiResource));
    }

    get wrapped(): Observable<SessionJsonapiResource> {
        return this.wrapped$.asObservable().pipe(filter((v: SessionJsonapiResource | null) => v instanceof SessionJsonapiResource));
    }

    get participant(): Observable<IParticipant> {
        return this.participant$.asObservable().pipe(filter((v: IParticipant | null) => !!v));
    }

    get poi(): Observable<IPOI> {
        return this.poi$.asObservable().pipe(filter((v: IPOI | null) => !!v));
    }

    get progress(): Observable<IProgress> {
        return this.progress$.asObservable().pipe(filter((v: IProgress | null) => !!v));
    }

    ngOnDestroy(): void {
        this.privateChannels.forEach((r: SocketIoPrivateChannel) => {
            this.echoService.leave(r.name);
        });

        this.changes$.complete();
        this.ended$.complete();
        this.wrapped$.complete();
        this.participant$.complete();
        this.poi$.complete();
        this.progress$.complete();
    }

    subscribe(channelName: EPrivateChannelNames, sessionId?: string): void {
        this.echoService
            .private(this.preparePrivateChannel(channelName, sessionId))
            .pipe(take(1))
            .subscribe((privateChannel: SocketIoPrivateChannel) => {
                this.listenPrivateChannel(channelName, privateChannel);
            });
    }

    unsubscribe(channelName: EPrivateChannelNames, sessionId?: string): void {
        const index: number = this.findPrivateChannelIndex(this.preparePrivateChannel(channelName, sessionId));

        if (index !== -1) {
            this.echoService.leave(this.privateChannels[index].name);
            this.privateChannels.slice(index, 1);
        }

        this.changes$.complete();
        this.ended$.complete();
        this.wrapped$.complete();
        this.participant$.complete();
        this.poi$.complete();
        this.progress$.complete();

        this.changes$ = new BehaviorSubject<SessionJsonapiResource>(null);
        this.ended$ = new BehaviorSubject<SessionJsonapiResource>(null);
        this.wrapped$ = new BehaviorSubject<SessionJsonapiResource>(null);
        this.participant$ = new BehaviorSubject<IParticipant>(null);
        this.poi$ = new BehaviorSubject<IPOI>(null);
        this.progress$ = new BehaviorSubject<IProgress>(null);
    }

    whisper(channelName: EPrivateChannelNames, sessionId: string, action: EPOIAction | EParticipantAction, data: IPOIWhisper | IParticipantWhisper): void {
        const index: number = this.findPrivateChannelIndex(this.preparePrivateChannel(channelName, sessionId));

        if (index !== -1) {
            this.privateChannels[index].whisper(action, data);
        }
    }

    private preparePrivateChannel(channel: EPrivateChannelNames, sessionId?: string): string {
        let result: string = String(channel);

        if (!!sessionId) {
            result = result.replace(/{sessionIdentity}/, sessionId);
        }

        return result.replace(/{userIdentity}/, this.authenticationService.identity);
    }

    private listenPrivateChannel(channelName: EPrivateChannelNames, channel: SocketIoPrivateChannel): void {
        switch (channelName) {
            case EPrivateChannelNames.view:
                // basics

                channel.listen('changed', (event: { dto: IDataObject }) => {
                    const converted: SessionJsonapiResource = this.sessionJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.changes$.next(converted);
                });

                // ending

                channel.listen('ended', (event: { dto: IDataObject }) => {
                    const converted: SessionJsonapiResource = this.sessionJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.ended$.next(converted);
                });

                // wrapping

                channel.listen('wrapped', (event: { dto: IDataObject }) => {
                    const converted: SessionJsonapiResource = this.sessionJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.wrapped$.next(converted);
                });

                // Participants

                channel.listen('participant_added', (event: { dto: IDataObject }) => {
                    const converted: UserParticipantJsonapiResource = this.userParticipantJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.participant$.next({
                        action: EParticipantAction.added,
                        resource: converted
                    });
                });
                channel.listen('participant_removed', (event: { dto: IDataObject }) => {
                    const converted: UserParticipantJsonapiResource = this.userParticipantJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.participant$.next({
                        action: EParticipantAction.removed,
                        resource: converted
                    });
                });
                channel.listenForWhisper('participant_selected', (event: IParticipantWhisper ) => {
                    this.participant$.next({
                        action: EParticipantAction.selected,
                        whisper: event
                    });
                });
                channel.listenForWhisper('participant_deselected', (event: IParticipantWhisper ) => {
                    this.participant$.next({
                        action: EParticipantAction.deselected,
                        whisper: event
                    });
                });

                // POIs

                channel.listen('poi_created', (event: { dto: IDataObject }) => {
                    const converted: SessionPoiJsonapiResource = this.sessionPoiJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.poi$.next({
                        action: EPOIAction.created,
                        resource: converted
                    });
                });
                channel.listen('poi_changed', (event: { dto: IDataObject }) => {
                    const converted: SessionPoiJsonapiResource = this.sessionPoiJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.poi$.next({
                        action: EPOIAction.changed,
                        resource: converted
                    });
                });
                channel.listen('poi_removed', (event: { dto: IDataObject }) => {
                    const converted: SessionPoiJsonapiResource = this.sessionPoiJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.poi$.next({
                        action: EPOIAction.removed,
                        resource: converted
                    });
                });
                channel.listenForWhisper('backtrack_started', (event: IPOIWhisper ) => {
                    this.poi$.next({
                        action: EPOIAction.backtrackStarted,
                        whisper: event
                    });
                });
                channel.listenForWhisper('backtrack_ended', (event: IPOIWhisper ) => {
                    this.poi$.next({
                        action: EPOIAction.backtrackEnded,
                        whisper: event
                    });
                });
                channel.listenForWhisper('backtrack_extended', (event: IPOIWhisper ) => {
                    this.poi$.next({
                        action: EPOIAction.backtrackExtended,
                        whisper: event
                    });
                });
                channel.listenForWhisper('stopwatch_started', (event: IPOIWhisper ) => {
                    this.poi$.next({
                        action: EPOIAction.stopwatchStarted,
                        whisper: event
                    });
                });
                channel.listenForWhisper('stopwatch_ended', (event: IPOIWhisper ) => {
                    this.poi$.next({
                        action: EPOIAction.stopwatchEnded,
                        whisper: event
                    });
                });
                channel.listenForWhisper('active_poi_changed', (event: IPOIWhisper ) => {
                    this.poi$.next({
                        action: EPOIAction.activeChanged,
                        whisper: event
                    });
                });

                // Progress

                channel.listen('progress_created', (event: { dto: IDataObject }) => {
                    const converted: SessionProgressJsonapiResource = this.sessionProgressJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.progress$.next({
                        action: EProgressAction.created,
                        resource: converted
                    });
                });
                channel.listen('progress_removed', (event: { dto: IDataObject }) => {
                    const converted: SessionProgressJsonapiResource = this.sessionProgressJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.progress$.next({
                        action: EProgressAction.removed,
                        resource: converted
                    });
                });

                this.appendPrivateChannel(channel);
                break;
            default:
                break;
        }
    }

    private findPrivateChannelIndex(channelName: string): number {
        return this.privateChannels.findIndex((c: SocketIoPrivateChannel) => String(c.name).includes(channelName));
    }

    private appendPrivateChannel(channel: SocketIoPrivateChannel): void {
        if (this.findPrivateChannelIndex(channel.name) === -1) {
            this.privateChannels.push(channel);
        }
    }
}
