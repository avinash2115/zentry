import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, ViewChild } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { UserService } from '../../user.service';
import { ParticipantService } from '../participant.service';
import { ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../shared/classes/data-error';
import { SwalService } from '../../../../shared/services/swal.service';
import { Router } from '@angular/router';
import { LayoutService } from '../../../../shared/services/layout.service';
import { BaseList } from '../../../assistant/list/abstractions/base.abstract';
import { Resource } from '../../../../../vendor/vp-ngx-jsonapi';
import { ListComponent as AssistantListComponent } from '../../../assistant/list/components/list/list.component';

@Component({
    selector: 'app-user-participant-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, ParticipantService]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    @Input() limit: number = 18;

    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    public data: Array<ParticipantJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected userService: UserService,
        protected participantService: ParticipantService,
    ) {
        super(cdr);
    }

    get service(): BaseList {
        return this.userService.participantService.participantJsonapiService;
    }

    ngOnInit(): void {
        this.layoutService.changeTitle(this.terms('participants'));
    }

    onResponse(data: Array<Resource>): void {
        this.data = data as Array<ParticipantJsonapiResource>;
        this.detectChanges();
    }

    onFetching(value: boolean): void {
    }

    trackByFn(index: number, item: Resource): string {
        return item.id;
    }

    remove(entity: ParticipantJsonapiResource): void {
        SwalService
            .remove({
                title: `Are you sure?`,
                text: `${entity.fullname || entity.email} is going to be removed!`,
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    this.participantService
                        .remove(entity)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe((result: boolean) => {
                            this.loaderService.hide();

                            if (result) {
                                const index: number = this.data.findIndex((p: ParticipantJsonapiResource) => p.id === entity.id);

                                if (index !== -1) {
                                    this.data.splice(index, 1);
                                    this.detectChanges();
                                }

                                SwalService.toastSuccess({title: `${entity.fullname || entity.email} has been removed!`,})
                            } else {
                                SwalService
                                    .error({
                                        title: `${entity.fullname || entity.email} was not removed!`,
                                        text: `Please try to remove it again.`
                                    });
                            }
                        }, (error: DataError) => {
                            this.loaderService.hide();
                            this.fallback(error);
                        });
                }
            });
    }
}
