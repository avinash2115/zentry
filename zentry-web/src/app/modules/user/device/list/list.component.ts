import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { UserService } from '../../user.service';
import { DeviceJsonapiResource, EDeviceModels } from '../../../../resources/user/device/device.jsonapi.service';
import { takeUntil } from 'rxjs/operators';
import { DomSanitizer, SafeUrl } from '@angular/platform-browser';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { combineLatest } from 'rxjs';
import { EPrivateChannelNames, UserSubscriptionService } from '../../user.subscription.service';
import { animate, style, transition, trigger } from '@angular/animations';
import { ConnectingPayloadJsonapiResource as DeviceConnectingPayloadJsonapiResource } from '../../../../resources/user/device/connecting-payload/connecting-payload.jsonapi.service';
import { SwalService } from '../../../../shared/services/swal.service';
import { DataError } from '../../../../shared/classes/data-error';
import { IAcknowledgeResponse } from '../../../../shared/interfaces/acknowledge-response.interface';
import { LoaderService } from '../../../../shared/services/loader.service';
import { LayoutService } from '../../../../shared/services/layout.service';

@Component({
    selector: 'app-user-device-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        UserService
    ],
    animations: [
        trigger('List', [
            transition(':enter', [])
        ]),
        trigger('EnterLeave', [
            transition(':enter', [
                style({transform: 'scale(0.5)', opacity: 0}),  // initial
                animate('1s cubic-bezier(.8, -0.6, 0.2, 1.5)',
                    style({transform: 'scale(1)', opacity: 1}))  // final
            ]),
            transition(':leave', [
                style({transform: 'scale(1)', opacity: 1, height: '*'}),
                animate('1s cubic-bezier(.8, -0.6, 0.2, 1.5)',
                    style({
                        transform: 'scale(0.5)', opacity: 0,
                        height: '0px', margin: '0px'
                    }))
            ])
        ])
    ]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    public qrCode: SafeUrl;
    public data: Array<DeviceJsonapiResource> = [];
    public connectingDevice: DeviceConnectingPayloadJsonapiResource | null;

    constructor(
        private cdr: ChangeDetectorRef,
        private layoutService: LayoutService,
        private domSanitizer: DomSanitizer,
        private loaderService: LoaderService,
        private userService: UserService,
        private userSubscriptionService: UserSubscriptionService,
    ) {
        super(cdr);
    }

    get isConnectingDevice(): boolean {
        return this.connectingDevice instanceof DeviceConnectingPayloadJsonapiResource;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('My Devices');

        this.subscribe();

        combineLatest([
            this.userService.deviceConnectingQR,
            this.userService.devices
        ]).pipe(
            takeUntil(this._destroy$)
        ).subscribe(([qrCode, devices]: [Blob, Array<DeviceJsonapiResource>]) => {
            setTimeout(() => {
                this.data = devices;
                this.qrCode = this.domSanitizer.bypassSecurityTrustUrl(URL.createObjectURL(qrCode));

                this.sort();

                this.loadingCompleted();
            }, 1500);
        });
    }

    devicePicture(model: EDeviceModels): SafeUrl {
        switch (model) {
            case EDeviceModels.iphone_5:
            case EDeviceModels.iphone_5s:
            case EDeviceModels.iphone_se:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/iphone-5.svg')
            case EDeviceModels.iphone_6:
            case EDeviceModels.iphone_6s:
            case EDeviceModels.iphone_7:
            case EDeviceModels.iphone_8:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/iphone-678.svg');
            case EDeviceModels.iphone_6_plus:
            case EDeviceModels.iphone_6s_plus:
            case EDeviceModels.iphone_7_plus:
            case EDeviceModels.iphone_8_plus:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/iphone-plus.svg')
            case EDeviceModels.iphone_10:
            case EDeviceModels.iphone_10s:
            case EDeviceModels.iphone_11_pro:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/iphone-x.svg');
            case EDeviceModels.iphone_10_max:
            case EDeviceModels.iphone_10s_max:
            case EDeviceModels.iphone_11_pro_max:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/iphone-max.svg')
            case EDeviceModels.iphone_10r:
            case EDeviceModels.iphone_11:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/iphone-11.svg')
            default:
                return this.domSanitizer.bypassSecurityTrustUrl('/assets/img/icons/devices/device.svg')
        }
    }

    remove(entity: DeviceJsonapiResource): void {
        SwalService
            .remove({
                title: `Your ${entity.model} is going to be removed!`,
                text: `You will not be able to use this device anymore, are you sure?`,
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    entity
                        .customCall({
                            method: 'DELETE',
                        })
                        .then(({acknowledge}: IAcknowledgeResponse) => {
                            this.loaderService.hide();

                            if (acknowledge) {
                                const index: number = this.data.findIndex((d: DeviceJsonapiResource) => d.id === entity.id);

                                if (index !== -1) {
                                    this.data.splice(index, 1);
                                    this.detectChanges();
                                }

                                SwalService.toastSuccess({title: `Your ${entity.model} has been removed!`,})
                            } else {
                                SwalService
                                    .error({
                                        title: `Your ${entity.model} was not removed!`,
                                        text: `Please try to remove it again.`
                                    });
                            }
                        }, (error: DataError) => {
                            this.loaderService.hide();
                            SwalService
                                .error({
                                    title: `Your ${entity.model} was not removed!`,
                                    text: error.message
                                });
                        });
                }
            });
    }

    private subscribe(): void {
        this.userSubscriptionService
            .deviceConnecting
            .pipe(takeUntil(this._destroy$))
            .subscribe((entity: DeviceConnectingPayloadJsonapiResource) => {
                this.connectingDevice = entity;
                this.detectChanges();
            });

        this.userSubscriptionService
            .deviceConnectingRefresh
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                SwalService
                    .error({
                        title: 'QR code expired!',
                        text: 'We\'ve refreshed QR code for you, please rescan it again!'
                    })
                    .then(() => {
                        this.connectingCallback();
                    });
            });

        this.userSubscriptionService
            .deviceConnectingFailed
            .pipe(takeUntil(this._destroy$))
            .subscribe((connectingDevice: DeviceConnectingPayloadJsonapiResource) => {
                SwalService
                    .error({
                        title: `Your ${connectingDevice.model} was not added :(`,
                        text: 'We\'ve refreshed QR code for you, please rescan it again!'
                    })
                    .then(() => {
                        this.connectingCallback();
                    });
            });

        this.userSubscriptionService
            .deviceCreated
            .pipe(takeUntil(this._destroy$))
            .subscribe((device: DeviceJsonapiResource) => {
                const index: number = this.data.findIndex((d: DeviceJsonapiResource) => d.id === device.id);

                if (index === -1) {
                    this.data.unshift(device);
                }

                this.connectingCallback();
            });

        this.userSubscriptionService
            .deviceExists
            .pipe(takeUntil(this._destroy$))
            .subscribe((device: DeviceJsonapiResource) => {
                const index: number = this.data.findIndex((d: DeviceJsonapiResource) => d.id === device.id);

                if (index === -1) {
                    this.data.unshift(device);
                } else {
                    SwalService
                        .success({
                            title: `Your ${device.model} already exists!`,
                            text: `You can find your device in the list below.`
                        })
                        .then(() => {
                            this.connectingCallback();
                        });
                }

                this.connectingCallback();
            });


        this.userSubscriptionService
            .deviceRemoved
            .pipe(takeUntil(this._destroy$))
            .subscribe((device: DeviceJsonapiResource) => {
                const index: number = this.data.findIndex((d: DeviceJsonapiResource) => d.id === device.id);

                if (index !== -1) {
                    this.data.splice(index, 1);
                }

                this.detectChanges();
            });

        this.userSubscriptionService.subscribe(EPrivateChannelNames.devices);
    }

    private connectingCallback(): void {
        this.userService
            .deviceConnectingQR
            .subscribe((qrCode: Blob) => {
                this.connectingDevice = null;
                this.qrCode = this.domSanitizer.bypassSecurityTrustUrl(URL.createObjectURL(qrCode));
                setTimeout(() => {
                    this.detectChanges();
                }, 1000)
            });
    }

    private sort(): void {
        this.data = this.data.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());
    }
}
