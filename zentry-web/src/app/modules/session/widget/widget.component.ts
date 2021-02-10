import { ChangeDetectionStrategy, ChangeDetectorRef, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { AuthenticationService } from '../../authentication/authentication.service';
import { switchMap, take, takeUntil } from 'rxjs/operators';
import * as moment from 'moment';
import { Moment } from 'moment';
import { DomSanitizer, SafeUrl } from '@angular/platform-browser';
import { DataError } from '../../../shared/classes/data-error';
import { EchoService } from '../../../shared/services/echo.service';
import { Router } from '@angular/router';
import { MediaService } from '../../../shared/services/media/media.service';
import { SwalService } from '../../../shared/services/swal.service';
import { LoaderService } from '../../../shared/services/loader.service';
import { SessionService } from '../session.service';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';
import { LayoutService } from '../../../shared/services/layout.service';
import { SessionSubscriptionService } from '../session.subscription.service';
import { HeaderService } from '../../../shared/services/header.service';
import { UploadService } from '../../../shared/services/media/upload.service';
import { TokenJsonapiResource as LoginTokenJsonapiResource } from '../../../resources/login/token/token.jsonapi.service';
import { IHighlight } from '../../../shared/components/media/audio/waveform/waveform.component';
import { SessionUploadService } from '../session.upload.service';
import { BaseAuthorizedComponent } from '../../../shared/classes/abstracts/component/base-authorized-component';
import { UserService } from '../../user/user.service';
import { ParticipantService } from '../../user/participant/participant.service';
import { SessionParticipantService } from '../session.participant.service';
import { SessionPoiService } from '../session.poi.service';

@Component({
    selector: 'app-session-widget',
    templateUrl: './widget.component.html',
    styleUrls: ['./widget.component.scss'],
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
        SessionUploadService,
    ]
})
export class WidgetComponent extends BaseAuthorizedComponent implements OnInit {
    @ViewChild('name', {static: false}) public name: ElementRef;

    public isPreparing: boolean = false;

    public session: SessionJsonapiResource | null;
    public sessionQRCode: SafeUrl | null;

    public stream: MediaStream | null = null;

    private _trackpadActivatedAt: Moment | null;

    private _isFooterVisible: boolean = true;

    private _editMode: boolean = false;

    constructor(
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
        protected _userSerivce: UserService,
        protected _participantService: ParticipantService,
        protected _sessionService: SessionService,
        protected _sessionSubscriptionService: SessionSubscriptionService,
        protected _sessionParticipantService: SessionParticipantService,
        protected _sessionPoiService: SessionPoiService,
        protected _sessionUploadService: SessionUploadService,
    ) {
        super(cdr, loaderService, authenticationService);

        cdr.detach();
    }

    get service(): SessionService {
        return this._sessionService;
    }

    get mediaService(): MediaService {
        return this._mediaService;
    }

    get waveformHighlight(): IHighlight | null {
        return this.isTrackpadActive ? {startedAtUnix: this._trackpadActivatedAt.unix()} : null;
    }

    get isTrackpadActive(): boolean {
        return !!this._trackpadActivatedAt;
    }

    get canFinish(): boolean {
        return this.service.isStarted && !this.isTrackpadActive;
    }

    get isFooterVisible(): boolean {
        return this._isFooterVisible;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        super.initialize(() => {
            this.loadingCompleted();
        });

        this._layoutService.changeTitle(`${this.applicationName} Widget`);

        this._sessionUploadService
            .restore()
            .subscribe(
                (data: Array<SessionJsonapiResource>) => console.info(`Restored ${data.length} sessions.`),
                (error: Error) => console.error(error)
            );

        this._sessionService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((session: SessionJsonapiResource | null) => {
                if (this.session instanceof SessionJsonapiResource && !this.session.isFinished && session instanceof SessionJsonapiResource && session.isFinished) {
                    this._mediaService
                        .stop()
                        .pipe(take(1))
                        .subscribe(
                            () => {
                                this.loaderService.hide();

                                setTimeout(() => {
                                    this._sessionUploadService.listUpdate(session, false, true);
                                }, 5000);
                            },
                            (error: Error) => {
                                this.loaderService.hide();
                                this.fallback(error);
                            }
                        );
                }

                this.session = session;
                this.detectChanges();
            });
    }

    trackpadActivated(value: Moment | null): void {
        this._trackpadActivatedAt = value;

        if (this._trackpadActivatedAt !== null) {
            if (!this.mediaService.isAudioActive) {
                this.mediaService.toggleAudio();
            }
        }

        this.detectChanges();
    }

    logout(): void {
        this.authenticationService
            .logout()
            .pipe(takeUntil(this._destroy$))
            .subscribe(({redirectTo}: { redirectTo: string }) => {
                this._echoService.disconnect();
                this._router.navigate([redirectTo]).then(() => {
                });
            }, (error: DataError) => {
                this.fallback(error);
            })
    }

    external(): void {
        this.loaderService.show();

        this.authenticationService
            .generateLoginToken()
            .pipe(takeUntil(this._destroy$))
            .subscribe((token: LoginTokenJsonapiResource) => {
                window.open(`/auth/login/token/${token.id}`);

                this.loaderService.hide();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    togglePreparing(value: boolean): void {
        this.isPreparing = value;
        this.detectChanges();
    }

    start(): void {
        this._mediaService
            .combinedRecorderChunk
            .pipe(takeUntil(this._destroy$))
            .subscribe((chunk: Blob) => this._sessionUploadService.register(this.session, [chunk]));

        this._mediaService
            .initialize()
            .pipe(
                takeUntil(this._destroy$),
                switchMap((stream: MediaStream) => {
                    this.loaderService.show();

                    this.stream = stream;

                    return this._sessionService.adhoc(`${moment().format('MMMM DD YYYY h:mm A')}`)
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

    startDirect(entity: SessionJsonapiResource): void {
        this._mediaService
            .combinedRecorderChunk
            .pipe(takeUntil(this._destroy$))
            .subscribe((chunk: Blob) => this._sessionUploadService.register(this.session, [chunk]));

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
            title: `Do you wish to end the session?`,
            confirmButtonText: `End`,
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
        this._sessionService.finish();
    }

    toggleFooter(value: boolean): void {
        this._isFooterVisible = value;
        this.detectChanges();
    }

    onEditMode(): void {
        this._editMode = true;

        this.detectChanges();

        setTimeout(() => {
            (this.name.nativeElement as HTMLInputElement).setSelectionRange(0, 0);
            (this.name.nativeElement as HTMLInputElement).focus();

            this.detectChanges();
        });
    }

    offEditMode(): void {
        this._editMode = false;
        this.detectChanges();
    }

    isEditMode(): boolean {
        return this._editMode;
    }

    save(event?: FocusEvent): void {
        if (event && event.relatedTarget instanceof EventTarget && (event.relatedTarget as HTMLElement).id === 'cancel') {
            return
        }

        setTimeout(() => {
            this.service
                .save()
                .subscribe(() => {
                    this.offEditMode();
                }, (error: DataError) => {
                    this.fallback(error);
                });
        }, 300);
    }

    cancel(): void {
        this.session.attributeRestore('name');
        this.offEditMode();
    }
}
