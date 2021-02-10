import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../resources/user/participant/participant.jsonapi.service';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../../../../../resources/session/poi/participant/participant.jsonapi.service';
import { RecordedService } from '../../recorded.service';
import { map, switchMap, takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../../shared/classes/data-error';
import { NgSelectComponent } from '@ng-select/ng-select';
import { throttleable } from '../../../../../shared/decorators/throttleable.decorator';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../../shared/consts/form/patterns';
import { Converter } from '../../../../../../vendor/vp-ngx-jsonapi/services/converter';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { SwalService } from '../../../../../shared/services/swal.service';
import { Observable } from 'rxjs';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';

interface IParticipant {
    id: string,
    resource: UserParticipantJsonapiResource;
    originalResource: UserParticipantJsonapiResource | SessionPoiParticipantJsonapiResource;
}

@Component({
    selector: 'app-session-recorded-participant',
    templateUrl: './participant.component.html',
    styleUrls: ['./participant.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantComponent extends BaseDetachedComponent implements OnInit {
    @Input() recorded: SessionJsonapiResource;

    public entity: UserParticipantJsonapiResource | null = null;

    public data: Array<IParticipant> = [];
    public available: Array<UserParticipantJsonapiResource> = [];
    public participantCreate: UserParticipantJsonapiResource | null;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected _recordedService: RecordedService
    ) {
        super(cdr);

        cdr.detach();
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    ngOnInit(): void {
        this.fetchAvailable()
            .subscribe((data: Array<UserParticipantJsonapiResource>) => {
                this.available = data;
            }, (error: DataError) => this.fallback(error));

        this.recordedService
            .participantService
            .selected
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<UserParticipantJsonapiResource>) => {
                this.data = data.map((r: UserParticipantJsonapiResource) => {
                    return {
                        id: r.id,
                        resource: r,
                        originalResource: r
                    };
                });

                if (this.data.length) {
                    this.select(this.data[0].resource);
                }

                this.detectChanges();
            });

        this.recordedService
            .participantService
            .entity
            .subscribe((entity: UserParticipantJsonapiResource) => {
                this.entity = entity;
                this.detectChanges();
            });

        this.detectChanges();
    }

    @throttleable(150)
    ngSelectMouseover(): void {
        this.detectChanges();
    }

    selectControlSearch(term: string, item: UserParticipantJsonapiResource): boolean {
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

    form(): void {
        this.participantCreate = Converter.getService('users_participants').new() as UserParticipantJsonapiResource;
        this.participantCreate.firstName = '';
        this.participantCreate.lastName = '';
        this.participantCreate.email = '';
        this.detectChanges();
    }

    formCancel(): void {
        this.participantCreate = null;
        this.detectChanges();
    }

    create(): void {
        this.loaderService.show();

        this.recordedService
            .participantService
            .add(this.participantCreate)
            .pipe(switchMap(() => this.fetchAvailable()))
            .subscribe(() => {
                this.loaderService.hide();

                SwalService.toastSuccess({
                    title: `${this.participantCreate.fullname || this.participantCreate.email} has been added`
                });

                this.formCancel();
            }, (error: DataError) => {
                this.loaderService.hide();

                this.fallback(error);
            });
    }

    remove(entity: UserParticipantJsonapiResource): void {
        SwalService.warning({
            title: 'Are you sure?',
            text: `You are going to remove ${entity.fullname || entity.email} from the session!`
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                this.loaderService.show();
                this.recordedService
                    .participantService
                    .remove(entity)
                    .pipe(switchMap(() => this.fetchAvailable()))
                    .subscribe(() => {
                        this.loaderService.hide();

                        SwalService.toastSuccess({
                            title: `${entity.fullname || entity.email} has been removed`
                        });
                    }, (error: DataError) => {
                        this.loaderService.hide();
                        this.fallback(error);
                    });
            }
        })
    }

    select(entity: UserParticipantJsonapiResource): void {
        this.recordedService
            .participantService
            .direct(entity)
            .subscribe();
    }

    isSelected(entity: UserParticipantJsonapiResource): boolean {
        return this.entity instanceof UserParticipantJsonapiResource && this.entity.id === entity.id;
    }

    private fetchAvailable(): Observable<Array<UserParticipantJsonapiResource>> {
        return this.recordedService
            .participantService
            .fetchAvailable()
            .pipe(
                map((data: Array<UserParticipantJsonapiResource>) => {
                    this.available = data;

                    return this.available;
                })
            );
    }
}
