import { Injectable, OnDestroy } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { SocketIoPrivateChannel } from 'laravel-echo/dist/channel';
import { EchoService } from '../../../shared/services/echo.service';
import { filter, take } from 'rxjs/operators';
import { AuthenticationService } from '../../authentication/authentication.service';
import { BehaviorSubject, Observable, Subject } from 'rxjs';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { SessionJsonapiResource, SessionJsonapiService } from '../../../resources/session/session.jsonapi.service';
import {
    StreamJsonapiResource as SessionStreamJsonapiResource,
    StreamJsonapiService as SessionStreamJsonapiService
} from '../../../resources/session/stream/stream.jsonapi.service';
import {
    PoiJsonapiResource as SessionPoiJsonapiResource,
    PoiJsonapiService as SessionPoiJsonapiService
} from '../../../resources/session/poi/poi.jsonapi.service';

export enum EPrivateChannelNames {
    list = 'users-{userIdentity}.sessions',
    view = 'users-{userIdentity}.sessions-{sessionIdentity}',
}

export enum EPOIAction {
    changed = 'changed',
    removed = 'removed'
}

export interface IPOI {
    action: EPOIAction
    resource: SessionPoiJsonapiResource
}

@Injectable()
export class RecordedSubscriptionService implements OnDestroy {
    private privateChannels: Array<SocketIoPrivateChannel> = [];

    private wrapped$: Subject<SessionJsonapiResource> = new Subject<SessionJsonapiResource>();
    private created$: Subject<SessionJsonapiResource> = new Subject<SessionJsonapiResource>();
    private removed$: Subject<SessionJsonapiResource> = new Subject<SessionJsonapiResource>();

    private streamConvertProgress$: BehaviorSubject<SessionStreamJsonapiResource> = new BehaviorSubject<SessionStreamJsonapiResource>(null);
    private poi$: BehaviorSubject<IPOI | null> = new BehaviorSubject<IPOI>(null);

    constructor(
        private http: HttpClient,
        private echoService: EchoService,
        private authenticationService: AuthenticationService,
        private sessionJsonapiService: SessionJsonapiService,
        private sessionStreamJsonapiService: SessionStreamJsonapiService,
        private sessionPoiJsonapiService: SessionPoiJsonapiService
    ) {
    }

    get wrapped(): Observable<SessionJsonapiResource> {
        return this.wrapped$.asObservable();
    }

    get created(): Observable<SessionJsonapiResource> {
        return this.created$.asObservable();
    }

    get removed(): Observable<SessionJsonapiResource> {
        return this.removed$.asObservable();
    }

    get streamConvertProgress(): Observable<SessionStreamJsonapiResource> {
        return this.streamConvertProgress$.asObservable().pipe(filter((v: SessionStreamJsonapiResource | null) => v instanceof SessionStreamJsonapiResource));
    }

    get poi(): Observable<IPOI> {
        return this.poi$.asObservable().pipe(filter((v: IPOI | null) => !!v));
    }

    ngOnDestroy(): void {
        this.privateChannels.forEach((r: SocketIoPrivateChannel) => {
            this.echoService.leave(r.name);
        });

        this.wrapped$.complete();
        this.created$.complete();
        this.removed$.complete();
        this.streamConvertProgress$.complete();
        this.poi$.complete();
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

        this.wrapped$.complete();
        this.created$.complete();
        this.removed$.complete();

        this.wrapped$ = new BehaviorSubject<SessionJsonapiResource>(null);
        this.created$ = new Subject<SessionJsonapiResource>();
        this.removed$ = new Subject<SessionJsonapiResource>();
    }

    private preparePrivateChannel(channel: EPrivateChannelNames, id?: string): string {
        let result: string = String(channel);

        if (!!id) {
            result = result.replace(/{sessionIdentity}/, id);
        }

        return result.replace(/{userIdentity}/, this.authenticationService.identity);
    }

    private listenPrivateChannel(channelName: EPrivateChannelNames, channel: SocketIoPrivateChannel): void {
        switch (channelName) {
            case EPrivateChannelNames.list:

                // wrapped

                channel.listen('wrapped', (event: { dto: IDataObject }) => {
                    const converted: SessionJsonapiResource = this.sessionJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.wrapped$.next(converted);
                });

                // base

                channel.listen('created', (event: { dto: IDataObject }) => {
                    const converted: SessionJsonapiResource = this.sessionJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.created$.next(converted);
                });

                channel.listen('removed', (event: { dto: IDataObject }) => {
                    const converted: SessionJsonapiResource = this.sessionJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.removed$.next(converted);
                });

                this.appendPrivateChannel(channel);
                break;
            case EPrivateChannelNames.view:

                // Streams

                channel.listen('stream_convert_progress', (event: { dto: IDataObject }) => {
                    const converted: SessionStreamJsonapiResource = this.sessionStreamJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.streamConvertProgress$.next(converted);
                });

                // POIs

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
