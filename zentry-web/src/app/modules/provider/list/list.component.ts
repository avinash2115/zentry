import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, ViewChild } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LoaderService } from '../../../shared/services/loader.service';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { Router } from '@angular/router';
import { LayoutService } from '../../../shared/services/layout.service';
import { ProviderService } from '../providers.service';
import { ProviderJsonapiResource } from '../../../resources/provider/provider.jsonapi.service';
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
    providers: [ProviderService]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    @Input() embedded: boolean = false;
    @Input() filter: object = {};
    @Input() limit: number = 18;

    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    public data: Array<ProviderJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected providerService: ProviderService,
        public crmService: CrmService
    ) {
        super(cdr);
    }

    get service(): BaseList {
        return this.providerService.providerJsonapiService;
    }

    get count(): number {
        return this.data.length;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        if (!this.embedded) {
            this.layoutService.changeTitle('Providers');
        }

        if (this.embedded) {
            this.fetch();
        }
    }

    onResponse(data: Array<Resource>): void {
        this.data = data as Array<ProviderJsonapiResource>;
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

    remove(entity: ProviderJsonapiResource): void {
        SwalService
            .remove({
                title: `Are you sure?`,
                text: `${entity.name} is going to be removed!`
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    this.providerService
                        .remove(entity)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe((result: boolean) => {
                            this.loaderService.hide();

                            if (result) {
                                const index: number = this.data.findIndex((p: ProviderJsonapiResource) => p.id === entity.id);

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
