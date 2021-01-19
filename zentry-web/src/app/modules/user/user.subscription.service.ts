import { Injectable, OnDestroy } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { SocketIoPrivateChannel } from 'laravel-echo/dist/channel';
import { EchoService } from '../../shared/services/echo.service';
import { first } from 'rxjs/operators';
import { AuthenticationService } from '../authentication/authentication.service';
import { Observable, Subject } from 'rxjs';
import { DeviceJsonapiResource } from '../../resources/user/device/device.jsonapi.service';
import { UserService } from './user.service';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { ConnectingPayloadJsonapiResource as DeviceConnectingPayloadJsonapiResource } from '../../resources/user/device/connecting-payload/connecting-payload.jsonapi.service';

export enum EPrivateChannelNames {
    devices = 'users-{userIdentity}.devices'
}

@Injectable()
export class UserSubscriptionService implements OnDestroy {
    private privateChannels: Array<SocketIoPrivateChannel> = [];

    private deviceConnecting$: Subject<DeviceConnectingPayloadJsonapiResource> = new Subject<DeviceConnectingPayloadJsonapiResource>();
    private deviceConnectingRefresh$: Subject<DeviceConnectingPayloadJsonapiResource> = new Subject<DeviceConnectingPayloadJsonapiResource>();
    private deviceConnectingFailed$: Subject<DeviceConnectingPayloadJsonapiResource> = new Subject<DeviceConnectingPayloadJsonapiResource>();
    private deviceCreated$: Subject<DeviceJsonapiResource> = new Subject<DeviceJsonapiResource>();
    private deviceExists$: Subject<DeviceJsonapiResource> = new Subject<DeviceJsonapiResource>();
    private deviceRemoved$: Subject<DeviceJsonapiResource> = new Subject<DeviceJsonapiResource>();

    constructor(
        private http: HttpClient,
        private echoService: EchoService,
        private authenticationService: AuthenticationService,
        private userService: UserService,
    ) {
    }

    get deviceConnecting(): Observable<DeviceConnectingPayloadJsonapiResource> {
        return this.deviceConnecting$.asObservable();
    }

    get deviceConnectingRefresh(): Observable<DeviceConnectingPayloadJsonapiResource> {
        return this.deviceConnectingRefresh$.asObservable();
    }

    get deviceConnectingFailed(): Observable<DeviceConnectingPayloadJsonapiResource> {
        return this.deviceConnectingFailed$.asObservable();
    }

    get deviceCreated(): Observable<DeviceJsonapiResource> {
        return this.deviceCreated$.asObservable();
    }

    get deviceExists(): Observable<DeviceJsonapiResource> {
        return this.deviceExists$.asObservable();
    }

    get deviceRemoved(): Observable<DeviceJsonapiResource> {
        return this.deviceRemoved$.asObservable();
    }

    subscribe(channelName: EPrivateChannelNames): void {
        this.echoService
            .private(this.preparePrivateChannel(channelName))
            .subscribe((privateChannel: SocketIoPrivateChannel) => {
                this.listenPrivateChannel(channelName, privateChannel);
            });
    }

    ngOnDestroy(): void {
        this.privateChannels.forEach((r: SocketIoPrivateChannel) => {
            this.echoService.leave(r.name);
        });

        this.deviceConnecting$.complete();
        this.deviceConnectingRefresh$.complete();
        this.deviceConnectingFailed$.complete();
        this.deviceCreated$.complete();
        this.deviceExists$.complete();
        this.deviceRemoved$.complete();
    }

    private preparePrivateChannel(channel: EPrivateChannelNames): string {
        return channel.replace(/{userIdentity}/, this.authenticationService.identity);
    }

    private listenPrivateChannel(channelName: EPrivateChannelNames, channel: SocketIoPrivateChannel): void {
        switch (channelName) {
            case EPrivateChannelNames.devices:
                channel.listen('connecting_started', (event: { dto: IDataObject }) => {
                    const converted: DeviceConnectingPayloadJsonapiResource = this.userService.deviceConnectingPayloadJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.deviceConnecting$.next(converted);
                });

                channel.listen('connecting_failed', (event: { dto: IDataObject }) => {
                    const converted: DeviceConnectingPayloadJsonapiResource = this.userService.deviceConnectingPayloadJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.deviceConnectingFailed$.next(converted);
                });

                channel.listen('connecting_refresh', (event: { dto: IDataObject }) => {
                    const converted: DeviceConnectingPayloadJsonapiResource = this.userService.deviceConnectingPayloadJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.deviceConnectingRefresh$.next(converted);
                });

                channel.listen('created', (event: { dto: IDataObject }) => {
                    const converted: DeviceJsonapiResource = this.userService.deviceJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.deviceCreated$.next(converted);
                });

                channel.listen('exists', (event: { dto: IDataObject }) => {
                    const converted: DeviceJsonapiResource = this.userService.deviceJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.deviceExists$.next(converted);
                });

                channel.listen('removed', (event: { dto: IDataObject }) => {
                    const converted: DeviceJsonapiResource = this.userService.deviceJsonapiService.new();
                    Converter.build(event.dto, converted);
                    this.deviceRemoved$.next(converted);
                });

                this.appendPrivateChannel(channel);
                break;
            default:
                break;
        }
    }

    private appendPrivateChannel(channel: SocketIoPrivateChannel): void {
        if (this.privateChannels.findIndex((c: SocketIoPrivateChannel) => c.name === channel.name) === -1) {
            this.privateChannels.push(channel);
        }
    }
}
