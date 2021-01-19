import { ChangeDetectionStrategy, ChangeDetectorRef, Component, ElementRef, NgZone, OnInit, ViewChild } from '@angular/core';
import { LayoutService } from '../../../../shared/services/layout.service';
import { ActivatedRoute, Router } from '@angular/router';
import { RecordedService } from '../recorded.service';
import { RecordedSubscriptionService } from '../recorded.subscription.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { FileService } from '../../../../shared/services/file.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { MapsAPILoader } from '@agm/core';
import { RecordedParticipantService } from '../recorded.participant.service';
import { RecordedPoiService } from '../recorded.poi.service';
import { RecordedPoiTranscriptService } from '../recorded.poi.transcript.service';
import { SharedService } from '../../../shared/shared.service';
import { ViewComponent } from './view.component';
import { SchoolJsonapiResource } from '../../../../resources/user/team/school/school.jsonapi.service';
import { ServiceJsonapiResource } from '../../../../resources/service/service.jsonapi.service';
import { TeamJsonapiResource } from '../../../../resources/user/team/team.jsonapi.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { take, takeUntil } from 'rxjs/operators';
import { Observable } from 'rxjs/internal/Observable';
import { DataError } from '../../../../shared/classes/data-error';
import { SwalService } from '../../../../shared/services/swal.service';
import { ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { RecordedNoteService } from '../recorded.note.service';
import { TagInputComponent } from 'ngx-chips';

enum EView {
    notes,
    participant
}

@Component({
    selector: 'app-session-recorded-view-custom',
    templateUrl: './view.custom.component.html',
    styleUrls: ['./view.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        RecordedService,
        RecordedSubscriptionService,
        RecordedNoteService,
        RecordedParticipantService,
        RecordedPoiService,
        RecordedPoiTranscriptService
    ]
})
export class ViewCustomComponent extends ViewComponent implements OnInit {
    @ViewChild('videoPlayer', {static: false}) videoPlayer: ElementRef;
    @ViewChild('shareModal', {static: false}) shareModal: ElementRef;
    @ViewChild('name', {static: false}) public name: ElementRef;
    @ViewChild('tags', {static: false}) public tags: TagInputComponent;
    @ViewChild('signModal', {static: false}) public signModal: ElementRef;

    public readonly views: typeof EView = EView;

    public viewActive: EView = EView.participant;

    private _services: Array<ServiceJsonapiResource> = [];
    private _teams: Array<TeamJsonapiResource> = [];
    private _schools: Array<SchoolJsonapiResource> = [];

    private _signBase64: string | null = null;

    constructor(
        protected _cdr: ChangeDetectorRef,
        protected _router: Router,
        protected _activatedRoute: ActivatedRoute,
        protected _layoutService: LayoutService,
        protected _loaderService: LoaderService,
        protected _fileService: FileService,
        protected _modalService: NgbModal,
        protected _mapsAPILoader: MapsAPILoader,
        protected _ngZone: NgZone,
        protected _sharedService: SharedService,
        protected _recordedService: RecordedService,
        protected _recordedSubscriptionService: RecordedSubscriptionService
    ) {
        super(
            _cdr,
            _router,
            _activatedRoute,
            _layoutService,
            _loaderService,
            _fileService,
            _modalService,
            _mapsAPILoader,
            _ngZone,
            _sharedService,
            _recordedService,
            _recordedSubscriptionService
        );
    }

    get services(): Array<ServiceJsonapiResource> {
        return this._services;
    }

    get serviceId(): string {
        return this.entity.service instanceof ServiceJsonapiResource ? this.entity.service.id : null;
    }

    set serviceId(value: string) {
        const resource: ServiceJsonapiResource | undefined = this.services.find((s: ServiceJsonapiResource) => s.id === value);

        if (resource instanceof ServiceJsonapiResource) {
            this.entity.addRelationship(resource, 'service');
            this.entity.forceDirty();

            this._loaderService.show();

            this._recordedService
                .save()
                .subscribe(() => {
                    this.loaderService.hide();
                    SwalService.toastSuccess({title: 'Service has been updated!'});
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.fallback(error);
                });
        }
    }

    get schools(): Array<SchoolJsonapiResource> {
        return this._schools;
    }

    get schoolId(): string {
        return this.entity.school instanceof SchoolJsonapiResource ? this.entity.school.id : null;
    }

    set schoolId(value: string) {
        const resource: SchoolJsonapiResource | undefined = this.schools.find((s: SchoolJsonapiResource) => s.id === value);

        if (resource instanceof SchoolJsonapiResource) {
            const team: TeamJsonapiResource | undefined = this._teams.find((t: TeamJsonapiResource) => t.schools.findIndex((s: SchoolJsonapiResource) => s.id === resource.id) !== -1);

            this.entity.addRelationship(team, 'team');
            this.entity.addRelationship(resource, 'school');

            this.entity.forceDirty();

            this._loaderService.show();

            this._recordedService
                .save()
                .subscribe(() => {
                    this.loaderService.hide();
                    SwalService.toastSuccess({title: 'School has been updated!'});
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.fallback(error);
                });
        }
    }

    ngOnInit(): void {
        super.ngOnInit();

        combineLatest([
            this.service.sessionService.serviceService.list({}, {[this.service.sessionService.serviceService.serviceJsonapiService.type]: [`name`]}),
            this.service.sessionService.userService.teamService.list()
        ]).pipe(take(1))
            .subscribe(([services, teams]: [Array<ServiceJsonapiResource>, Array<TeamJsonapiResource>]) => {
                this._services = services;
                this._teams = teams;
                this._schools = [];

                teams.forEach((t: TeamJsonapiResource) => {
                    this._schools = this._schools.concat(t.schools);
                });

                this._schools.sort((a: SchoolJsonapiResource, b: SchoolJsonapiResource) => a.name.localeCompare(b.name));
            });

        this.service
            .participantService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((value: ParticipantJsonapiResource | null) => {
                if (value instanceof ParticipantJsonapiResource && !this.isActive(this.views.participant)) {
                    this.activate(this.views.participant)
                }
            });
    }

    activate(value: EView): void {
        if (value === this.views.notes && this.isActive(this.views.participant)) {
            this.service.participantService.release();
        }

        this.viewActive = value;
        this.detectChanges();
    }

    isActive(value: EView): boolean {
        return this.viewActive === value;
    }

    setSign(sign: string): void {
        this._signBase64 = sign
    }

    document(): void {
        this.modalService.open(this.signModal, {
            size: 'lg',
            windowClass: 'recorded--sign-pad-modal'
        }).result.then((result: number) => {
            switch (result) {
                case 10:
                    this.loaderService.show();
                    this.service
                        .documentSession(this._signBase64)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe(() => {
                            this.loaderService.hide();
                            this.detectChanges();
                        }, (error: DataError) => {
                            this.loaderService.hide();
                            console.error(error);
                        })
                    break;
                case 20:
                default:
                    this._signBase64 = null;
                    break;
            }
        }, () => {
            this.loaderService.hide();
            this._signBase64 = null;
        });
    }

    unlock(): void {
        SwalService.warning({
            title: 'Are you sure?',
            text: `You are going to unlock session!`
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                this.loaderService.show();
                this.service
                .documentSession(null)
                .subscribe(() => {
                    this.loaderService.hide();
                    this.detectChanges();
                    this._signBase64 = null;
                }, (error: DataError) => {
                    this.loaderService.hide();
                    console.error(error);
                })
            }
        })
    }
}
