import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    Input,
    Output,
    EventEmitter,
    OnChanges,
    OnInit, SimpleChanges,
    ViewChild
} from '@angular/core';
import {
    ParticipantJsonapiResource,
    ParticipantJsonapiResource as UserParticipantJsonapiResource
} from '../../../../../resources/user/participant/participant.jsonapi.service';
import { PerfectScrollbarConfigInterface, PerfectScrollbarDirective } from 'ngx-perfect-scrollbar';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../../../../../resources/session/poi/participant/participant.jsonapi.service';
import { RecordedService } from '../../recorded.service';
import { map, switchMap, takeUntil } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { DataError } from '../../../../../shared/classes/data-error';
import { NgSelectComponent } from '@ng-select/ng-select';
import { throttleable } from '../../../../../shared/decorators/throttleable.decorator';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../../shared/consts/form/patterns';
import { Converter } from '../../../../../../vendor/vp-ngx-jsonapi/services/converter';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SwalService } from '../../../../../shared/services/swal.service';
import { Observable } from 'rxjs';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../../../resources/session/poi/poi.jsonapi.service';
import { of } from 'rxjs/internal/observable/of';

interface IParticipant {
    id: string,
    resource: UserParticipantJsonapiResource;
    originalResource: UserParticipantJsonapiResource | SessionPoiParticipantJsonapiResource;
}

@Component({
    selector: 'app-session-recorded-shared-participant',
    templateUrl: './participant.component.html',
    styleUrls: ['./participant.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantComponent extends BaseDetachedComponent implements OnInit, OnChanges {
    @Input() recorded: SessionJsonapiResource;
    @Input() recordedPoi: SessionPoiJsonapiResource;
    @Input() directly: boolean = false;
    @Input() inject: boolean = false;
    @Input() readonly: boolean = false;

    @Output() onParticipantClick: EventEmitter<UserParticipantJsonapiResource> = new EventEmitter<UserParticipantJsonapiResource>();

    @ViewChild('selectedList', {static: false}) public selectedList: PerfectScrollbarDirective;

    public data: Array<IParticipant> = [];
    public available: Array<UserParticipantJsonapiResource> = [];

    public readonly scrollbarConfig: PerfectScrollbarConfigInterface = {
        suppressScrollY: true,
    }

    public participantCreate: UserParticipantJsonapiResource | null;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected modalService: NgbModal,
        protected _recordedService: RecordedService
    ) {
        super(cdr);

        cdr.detach();
    }

    get editable(): boolean {
        return !this.readonly;
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    ngOnInit(): void {
        if (this.inject) {
            this.recordedService.direct(this.recorded);
        }

        this.fetchAvailable()
            .subscribe((data: Array<UserParticipantJsonapiResource>) => {
                this.available = data;
            }, (error: DataError) => this.fallback(error));

        switch (true) {
            case this.recorded instanceof SessionJsonapiResource && !(this.recordedPoi instanceof SessionPoiJsonapiResource):
                let observableRecorded: Observable<Array<UserParticipantJsonapiResource>>;

                if (this.directly) {
                    observableRecorded = of(this.recorded.participants);
                } else {
                    observableRecorded = this.recordedService.participantService.selected;
                }

                observableRecorded
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((data: Array<UserParticipantJsonapiResource>) => {
                        this.data = data.map((r: UserParticipantJsonapiResource) => {
                            return {
                                id: r.id,
                                resource: r,
                                originalResource: r,
                            };
                        });

                        this.detectChanges();

                        if (this.selectedList) {
                            this.selectedList.ps().update();
                            this.detectChanges();
                        }
                    });
                break;
            case this.recorded instanceof SessionJsonapiResource && this.recordedPoi instanceof SessionPoiJsonapiResource:
                let observablePoi: Observable<Array<SessionPoiJsonapiResource>>;

                if (this.directly) {
                    observablePoi = of(this.recorded.pois);
                } else {
                    observablePoi = this.recordedService.poiService.pois;
                }

                observablePoi
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((data: Array<SessionPoiJsonapiResource>) => {
                        const resource: SessionPoiJsonapiResource = data.find((r: SessionPoiJsonapiResource) => r.id === this.recordedPoi.id);

                        if (!(resource instanceof SessionPoiJsonapiResource)) {
                            throw new Error('Provided POI cannot be found');
                        }

                        this.data = resource
                            .participants
                            .map((r: SessionPoiParticipantJsonapiResource) => {
                                return {
                                    id: r.id,
                                    resource: r.raw,
                                    originalResource: r,
                                };
                            });

                        this.detectChanges();

                        if (this.selectedList) {
                            this.selectedList.ps().update();
                            this.detectChanges();
                        }
                    });
                break;
        }

        this.detectChanges();
    }

    ngOnChanges(changes: SimpleChanges): void {
        if (this.inject) {
            if (!changes['recorded'].firstChange) {
                this.recordedService.direct(changes['recorded'].currentValue);
            }
        }
    }

    @throttleable(150)
    ngSelectMouseover(): void {
        this.detectChanges();
    }

    isScrollAvailable(): boolean {
        return this.selectedList && this.selectedList.ps().scrollbarXActive;
    }

    isScrollLeftAvailable(): boolean {
        return this.selectedList && this.isScrollAvailable && this.selectedList.ps().reach.x !== 'start';
    }

    isScrollRightAvailable(): boolean {
        return this.selectedList && this.isScrollAvailable && this.selectedList.ps().reach.x !== 'end';
    }

    scrollLeft(): void {
        if (this.isScrollLeftAvailable()) {
            this.selectedList.scrollToX(Number(this.selectedList.ps().lastScrollLeft) - 48, 500);
            this.detectChanges();
        }
    }

    scrollRight(): void {
        if (this.isScrollRightAvailable()) {
            this.selectedList.scrollToX(Number(this.selectedList.ps().lastScrollLeft) + 48, 500);
            this.detectChanges();
        }
    }

    selectControlSearch(term: string, item: ParticipantJsonapiResource): boolean {
        term = term.toLocaleLowerCase();

        return item.fullname.toLocaleLowerCase().indexOf(term) > -1 || item.email.toLocaleLowerCase().indexOf(term) > -1;
    }

    selectControlPick(entity: UserParticipantJsonapiResource, ngSelectComponent?: NgSelectComponent): void {
        if (ngSelectComponent) {
            ngSelectComponent.close();
        }

        this.participantCreate = entity;

        this.detectChanges();
    }

    selectControlCreate(subject: string): Promise<UserParticipantJsonapiResource> {
        return new Promise((resolve, reject) => {
            const regex = new RegExp(EMAIL_VALIDATOR_PATTERN);
            if (!regex.test(subject) && !subject.split(' ')[0]) {
                reject();
            } else {
                const resource: UserParticipantJsonapiResource = Converter.getService('users_participants').new() as UserParticipantJsonapiResource;

                if ((new RegExp(EMAIL_VALIDATOR_PATTERN)).test(subject)) {
                    resource.email = subject;
                    resource.firstName = '';
                    resource.lastName = '';
                } else {
                    const fullName: Array<string> = subject.split(' ');
                    resource.firstName = fullName[0];
                    resource.lastName = '';

                    if (fullName[1] !== undefined) {
                        resource.lastName = fullName[1];
                    }
                }

                resolve(resource);
            }
        });
    }

    create(modal: any): void {
        this.loaderService.show();

        this.fetchAvailable()
            .subscribe(() => {
                this.loaderService.hide();

                this.modalService.open(modal, {
                    size: 'lg'
                }).result.then((result: number) => {
                    switch (result) {
                        case 10:
                            this.loaderService.show();

                            switch (true) {
                                case this.recorded instanceof SessionJsonapiResource && !(this.recordedPoi instanceof SessionPoiJsonapiResource):
                                    this.recordedService
                                        .participantService
                                        .add(this.participantCreate)
                                        .pipe(switchMap(() => this.fetchAvailable()))
                                        .subscribe(() => {
                                            this.loaderService.hide();

                                            SwalService.toastSuccess({
                                                title: `${this.participantCreate.fullname || this.participantCreate.email} has been added`
                                            });

                                            this.participantCreate = null;
                                        }, (error: DataError) => {
                                            this.loaderService.hide();

                                            this.fallback(error);
                                        });
                                    break;
                                case this.recorded instanceof SessionJsonapiResource && this.recordedPoi instanceof SessionPoiJsonapiResource:
                                    const resource: SessionPoiParticipantJsonapiResource = this.recordedService.sessionService.sessionPoiParticipantJsonapiService.new();
                                    resource.startedAt = this.recordedPoi.startedAt;
                                    resource.endedAt = this.recordedPoi.endedAt;
                                    resource.addRelationship(this.participantCreate, 'raw');

                                    this.recordedService
                                        .poiService
                                        .participantCreate(this.recordedPoi, resource)
                                        .pipe(switchMap(() => this.fetchAvailable()))
                                        .subscribe(() => {
                                            this.loaderService.hide();

                                            SwalService.toastSuccess({
                                                title: `${this.participantCreate.fullname || this.participantCreate.email} has been added`
                                            });

                                            this.participantCreate = null;
                                        }, (error: DataError) => {
                                            this.loaderService.hide();

                                            this.fallback(error);
                                        })
                                    break;
                            }

                            break;

                        default:
                            this.participantCreate = null;
                            break;
                    }

                }, () => {
                    this.participantCreate = null;
                    this.loaderService.hide();
                });
            });
    }

    remove(entity: IParticipant): void {
        switch (true) {
            case this.recorded instanceof SessionJsonapiResource && !(this.recordedPoi instanceof SessionPoiJsonapiResource):
                SwalService.warning({
                    title: 'Are you sure?',
                    text: `You are going to remove ${entity.resource.fullname || entity.resource.email} from the session!`
                }).then((answer: { value: boolean }) => {
                    if (answer.value) {
                        this.loaderService.show();

                        this.recordedService
                            .participantService
                            .remove(entity.originalResource as UserParticipantJsonapiResource)
                            .pipe(switchMap(() => this.fetchAvailable()))
                            .subscribe(() => {
                                this.loaderService.hide();

                                SwalService.toastSuccess({
                                    title: `${entity.resource.fullname || entity.resource.email} has been removed`
                                });
                            }, (error: DataError) => {
                                this.loaderService.hide();
                                this.fallback(error);
                            });
                    }
                });
                break;
            case this.recordedPoi instanceof SessionPoiJsonapiResource:
                SwalService.warning({
                    title: 'Are you sure?',
                    text: `You are going to remove ${entity.resource.fullname || entity.resource.email} from the clip!`
                }).then((answer: { value: boolean }) => {
                    if (answer.value) {
                        this.loaderService.show();

                        this.recordedService
                            .poiService
                            .participantRemove(this.recordedPoi, entity.originalResource as SessionPoiParticipantJsonapiResource)
                            .pipe(switchMap(() => this.fetchAvailable()))
                            .subscribe(() => {
                                this.loaderService.hide();

                                SwalService.toastSuccess({
                                    title: `${entity.resource.fullname || entity.resource.email} has been removed`
                                });
                            }, (error: DataError) => {
                                this.loaderService.hide();
                                this.fallback(error);
                            });
                    }
                });
                break;
        }
    }

    protected fetchAvailable(): Observable<Array<UserParticipantJsonapiResource>> {
        if (this.directly) {
            return of([]);
        }

        return this.recordedService
            .participantService
            .fetchAvailable(this.recordedPoi instanceof SessionPoiJsonapiResource, this.recordedPoi instanceof SessionPoiJsonapiResource)
            .pipe(
                map((data: Array<UserParticipantJsonapiResource>) => {
                    if (this.recordedPoi instanceof SessionPoiJsonapiResource) {
                        data = data.filter((resource: UserParticipantJsonapiResource) => {
                            return this.data.findIndex((r: IParticipant) => r.resource.id === resource.id) === -1;
                        });
                    }

                    this.available = data;

                    return this.available;
                })
            );
    }
}
