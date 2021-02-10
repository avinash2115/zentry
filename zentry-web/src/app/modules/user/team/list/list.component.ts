import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, ViewChild } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { Router } from '@angular/router';
import { LayoutService } from '../../../../shared/services/layout.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { UserService } from '../../user.service';
import { DataError } from '../../../../shared/classes/data-error';
import { TeamService } from '../team.service';
import { TeamJsonapiResource } from '../../../../resources/user/team/team.jsonapi.service';
import { SwalService } from '../../../../shared/services/swal.service';
import { takeUntil } from 'rxjs/operators';
import { CrmService } from '../../../../shared/services/crm.service';
import { BaseList } from '../../../assistant/list/abstractions/base.abstract';
import { ListComponent as AssistantListComponent } from '../../../assistant/list/components/list/list.component';
import { Resource } from '../../../../../vendor/vp-ngx-jsonapi';

@Component({
    selector: 'app-user-team-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, TeamService]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    @Input() limit: number = 18;

    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    public data: Array<TeamJsonapiResource> = [];

    private _active: TeamJsonapiResource | null;

    constructor(
        public cdr: ChangeDetectorRef,
        public router: Router,
        public layoutService: LayoutService,
        public loaderService: LoaderService,
        public userService: UserService,
        public teamService: TeamService,
        public crmService: CrmService
    ) {
        super(cdr);
    }

    get service(): BaseList {
        return this.userService.teamService.teamJsonapiService;
    }

    get teamsCount(): number {
        return this.data.length;
    }

    get schoolsCount(): number {
        return this.data.reduce((result: number, r: TeamJsonapiResource) => {
            result += r.schools.length;
            return result;
        }, 0);
    }

    get participantsCount(): number {
        return this.data.reduce((result: number, r: TeamJsonapiResource) => {
            result += r.participants.length;
            return result;
        }, 0);
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Districts/Schools');
    }

    onResponse(data: Array<Resource>): void {
        this.data = data as Array<TeamJsonapiResource>;
        this.detectChanges();
    }

    onFetching(value: boolean): void {
    }

    trackByFn(index: number, item: Resource): string {
        return item.id;
    }

    fetch(): void {
        this.loadingTrigger();

        this.assistantListComponent.reloadDataWithCurrentPage();
    }

    remove(entity: TeamJsonapiResource): void {
        SwalService
            .remove({
                title: `Are you sure?`,
                text: `${entity.name} is going to be removed!`
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    this.userService
                        .teamService
                        .remove(entity)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe((result: boolean) => {
                            this.loaderService.hide();

                            if (result) {
                                const index: number = this.data.findIndex((p: TeamJsonapiResource) => p.id === entity.id);

                                if (index !== -1) {
                                    this.data.splice(index, 1);
                                    this.detectChanges();
                                }

                                SwalService.toastSuccess({title: `${entity.name} has been removed!`});
                            } else {
                                SwalService
                                    .error({
                                        title: `${entity.name} was not removed!`,
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

    toggle(entity: TeamJsonapiResource): void {
        if (this._active instanceof TeamJsonapiResource && this._active.id === entity.id) {
            this._active = null;
        } else {
            this._active = entity;
        }

        this.detectChanges();
    }

    isToggled(entity: TeamJsonapiResource): boolean {
        return this._active instanceof TeamJsonapiResource && this._active.id === entity.id;
    }
}
