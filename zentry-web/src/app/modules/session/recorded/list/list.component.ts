import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, ViewChild } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../../shared/services/layout.service';
import { RecordedService } from '../recorded.service';
import { EPrivateChannelNames, RecordedSubscriptionService } from '../recorded.subscription.service';
import { EStatus, SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { BaseList } from '../../../assistant/list/abstractions/base.abstract';
import { Resource } from '../../../../../vendor/vp-ngx-jsonapi';
import { ListComponent as AssistantListComponent } from '../../../assistant/list/components/list/list.component';
import { takeUntil } from 'rxjs/operators';

@Component({
    selector: 'app-session-recorded-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [RecordedService, RecordedSubscriptionService]
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    @Input() embedded: boolean = false;
    @Input() filter: object = {};
    @Input() limit: number = 18;
    @Input() vertical: boolean = false;

    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    public data: Array<SessionJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected recordedService: RecordedService,
        protected recordedSubscriptionService: RecordedSubscriptionService,
    ) {
        super(cdr);
    }

    get service(): BaseList {
        return this.recordedService.sessionService.sessionJsonapiService;
    }

    get filterIncludes(): Array<string> {
        return ['*'];
    }

    get filterRemote(): object {
        return {
            statuses: {
                collection: [EStatus.wrapped]
            }
        };
    }

    get filterBy(): object {
        return {
            status: [EStatus.wrapped]
        };
    }

    ngOnInit(): void {
        if (!this.embedded) {
            this.layoutService.changeTitle('Sessions');
        }

        this.recordedSubscriptionService
            .wrapped
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => this.assistantListComponent.reloadDataWithCurrentPage());

        this.recordedSubscriptionService.subscribe(EPrivateChannelNames.list);

        this.detectChanges();
    }

    onResponse(data: Array<Resource>): void {
        this.data = data as Array<SessionJsonapiResource>;
        this.detectChanges();
    }

    onFetching(value: boolean): void {}

    trackByFn(index: number, item: Resource): string {
        return item.id;
    }
}
