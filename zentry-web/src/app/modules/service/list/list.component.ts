import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, ViewChild } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LoaderService } from '../../../shared/services/loader.service';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { Router } from '@angular/router';
import { LayoutService } from '../../../shared/services/layout.service';
import { ServiceService } from '../service.service';
import { ServiceJsonapiResource } from '../../../resources/service/service.jsonapi.service';
import { CrmService } from '../../../shared/services/crm.service';
import { BaseList } from '../../assistant/list/abstractions/base.abstract';
import { ListComponent as AssistantListComponent } from '../../assistant/list/components/list/list.component';
import { Resource } from '../../../../vendor/vp-ngx-jsonapi';
import { ParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';

@Component({
    selector: 'app-service-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [ServiceService]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    @Input() embedded: boolean = false;
    @Input() filter: object = {};
    @Input() limit: number = 18;

    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    public data: Array<ServiceJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected serviceService: ServiceService,
        public crmService: CrmService
    ) {
        super(cdr);
    }

    get service(): BaseList {
        return this.serviceService.serviceJsonapiService;
    }

    get count(): number {
        return this.data.length;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        if (!this.embedded) {
            this.layoutService.changeTitle('Services');
        }

        if (this.embedded) {
            this.fetch();
        }
    }

    onResponse(data: Array<Resource>): void {
        this.data = data as Array<ServiceJsonapiResource>;
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

    remove(entity: ServiceJsonapiResource): void {
        SwalService
            .remove({
                title: `Are you sure?`,
                text: `${entity.name} is going to be removed!`
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    this.serviceService
                        .remove(entity)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe((result: boolean) => {
                            this.loaderService.hide();

                            if (result) {
                                const index: number = this.data.findIndex((p: ServiceJsonapiResource) => p.id === entity.id);

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
}
