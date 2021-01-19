import { ChangeDetectionStrategy, ChangeDetectorRef, Component } from '@angular/core';
import { AuthenticationService } from '../../authentication/authentication.service';
import { DomSanitizer } from '@angular/platform-browser';
import { EchoService } from '../../../shared/services/echo.service';
import { Router } from '@angular/router';
import { MediaService } from '../../../shared/services/media/media.service';
import { LoaderService } from '../../../shared/services/loader.service';
import { SessionService } from '../session.service';
import { LayoutService } from '../../../shared/services/layout.service';
import { SessionSubscriptionService } from '../session.subscription.service';
import { HeaderService } from '../../../shared/services/header.service';
import { UploadService } from '../../../shared/services/media/upload.service';
import { SessionUploadService } from '../session.upload.service';
import { UserService } from '../../user/user.service';
import { ParticipantService } from '../../user/participant/participant.service';
import { SessionParticipantService } from '../session.participant.service';
import { SessionPoiService } from '../session.poi.service';
import { WidgetComponent } from './widget.component';
import { SessionProgressService } from '../session.progress.service';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';
import { filter, switchMap, take, takeUntil, tap } from 'rxjs/operators';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import * as moment from 'moment';
import { SessionSoapService } from '../session.soap.service';
import { NotificationService } from '../../../shared/services/notification.service';
import { ServiceJsonapiResource, ServiceJsonapiService } from '../../../resources/service/service.jsonapi.service';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { WhitespaceValidator } from '../../../shared/validators/whitespace.validator';
import { SchoolJsonapiResource } from '../../../resources/user/team/school/school.jsonapi.service';
import { ParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';
import { Subject } from 'rxjs/internal/Subject';

@Component({
    selector: 'app-session-widget',
    templateUrl: './widget.custom.component.html',
    styleUrls: ['./widget.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        MediaService,
        UploadService,
        UserService,
        ParticipantService,
        SessionService,
        SessionSubscriptionService,
        SessionParticipantService,
        SessionPoiService,
        SessionProgressService,
        SessionSoapService,
        SessionUploadService,
        NotificationService
    ]
})
export class WidgetCustomComponent extends WidgetComponent {
    public formQuickStart: FormGroup;

    private _services: Array<ServiceJsonapiResource> = [];

    constructor(
        protected fb: FormBuilder,
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected _router: Router,
        protected _domSanitizer: DomSanitizer,
        protected _headerService: HeaderService,
        protected _layoutService: LayoutService,
        protected _echoService: EchoService,
        protected _mediaService: MediaService,
        protected _uploadService: UploadService,
        protected _userService: UserService,
        protected _participantService: ParticipantService,
        protected _sessionService: SessionService,
        protected _sessionSubscriptionService: SessionSubscriptionService,
        protected _sessionParticipantService: SessionParticipantService,
        protected _sessionPoiService: SessionPoiService,
        protected _sessionUploadService: SessionUploadService,
        protected _notificationService: NotificationService
    ) {
        super(
            cdr,
            loaderService,
            authenticationService,
            _router,
            _domSanitizer,
            _headerService,
            _layoutService,
            _echoService,
            _mediaService,
            _uploadService,
            _userService,
            _participantService,
            _sessionService,
            _sessionSubscriptionService,
            _sessionParticipantService,
            _sessionPoiService,
            _sessionUploadService
        );
    }

    get services(): Array<ServiceJsonapiResource> {
        return this._services;
    }

    ngOnInit() {
        super.ngOnInit();

        this._sessionService
            .serviceService
            .list({}, {[this._sessionService.serviceService.serviceJsonapiService.type]: [`name`]})
            .subscribe((data: Array<ServiceJsonapiResource>) => {
                this._services = data;

                this.formQuickStart = this.fb.group({
                    service_id: [null, [Validators.required]],
                });

                this.formQuickStart
                    .valueChanges
                    .pipe(takeUntil(this._destroy$))
                    .subscribe(() => this.detectChanges());

                this.detectChanges();
            });
    }

    start(): void {
        const service_id: string | null = this.formQuickStart.getRawValue()['service_id'];
        let service: ServiceJsonapiResource | null = null;

        if (service_id !== null) {
            service = this.services.find((s: ServiceJsonapiResource) => s.id === service_id) || null
        }

        this._mediaService
            .combinedRecorderChunk
            .pipe(
                takeUntil(this._destroy$),
                tap((chunk: Blob) => this._sessionUploadService.register(this.session, [chunk]))
            )
            .subscribe();

        this._mediaService
            .initialize()
            .pipe(
                takeUntil(this._destroy$),
                switchMap((stream: MediaStream) => {
                    this.loaderService.show();

                    this.stream = stream;

                    return this._sessionService.adhoc(`${moment().format('MMMM DD YYYY h:mm A')}`, service);
                }),
                switchMap(() => {
                    this.loaderService.hide();
                    this._mediaService.start();

                    return this._sessionService.deviceConnectingQR;
                })
            )
            .subscribe((qrCode: Blob) => {
                this.formQuickStart.get('service_id').patchValue(null);
                this.sessionQRCode = this._domSanitizer.bypassSecurityTrustUrl(URL.createObjectURL(qrCode));
                this.detectChanges();
            }, (error: DataError | Error) => {
                this._mediaService.stop().subscribe();
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    startDirect(entity: SessionJsonapiResource): void {
        this._mediaService
            .combinedRecorderChunk
            .pipe(
                takeUntil(this._destroy$),
                tap((chunk: Blob) => this._sessionUploadService.register(this.session, [chunk]))
            )
            .subscribe();

        this._mediaService
            .initialize()
            .pipe(
                takeUntil(this._destroy$),
                switchMap((stream: MediaStream) => {
                    this.loaderService.show();

                    this.stream = stream;

                    return this._sessionService.start(entity);
                }),
                switchMap(() => {
                    this.loaderService.hide();
                    this._mediaService.start();

                    return this._sessionService.deviceConnectingQR;
                })
            )
            .subscribe((qrCode: Blob) => {
                this.sessionQRCode = this._domSanitizer.bypassSecurityTrustUrl(URL.createObjectURL(qrCode));
                this.detectChanges();
            }, (error: DataError | Error) => {
                this._mediaService.stop().subscribe();
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    stop(): void {
        this.loaderService.show();

        SwalService.warning({
            title: `Are you sure you want to end this session?`,
            text: `Once session ends you will be prompted to enter your documentation.`,
            confirmButtonText: `End`
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                this._sessionService
                    .end()
                    .pipe(take(1))
                    .subscribe(() => {
                        this.loaderService.hide();
                    }, (error: Error) => {
                        this.loaderService.hide();
                        this.fallback(error);
                    });
            } else {
                this.loaderService.hide();
            }
        });
    }

    finish(): void {
        this._mediaService
            .active
            .pipe(
                filter((value: boolean) => !value),
                take(1)
            )
            .subscribe(() => {
                setTimeout(() => {
                    super.finish();
                    this._notificationService.success('Session was completed', 3000);
                }, 1000);
            });
    }
}
