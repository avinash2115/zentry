import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { LayoutService } from '../../../../shared/services/layout.service';
import { RecordedService } from '../recorded.service';
import { RecordedSubscriptionService } from '../recorded.subscription.service';
import { ListComponent } from './list.component';
import {
    ParticipantJsonapiResource,
} from '../../../../resources/user/participant/participant.jsonapi.service';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { ListComponent as AssistantListComponent } from '../../../assistant/list/components/list/list.component';
import { IPage } from '../../../../../vendor/vp-ngx-jsonapi/interfaces/page'

@Component({
    selector: 'app-session-recorded-list-custom',
    templateUrl: './list.custom.component.html',
    styleUrls: ['./list.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [RecordedService, RecordedSubscriptionService]
})
export class ListCustomComponent extends ListComponent implements OnInit {
    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    public participantInfo: ParticipantJsonapiResource | null = null;
    public participantRecorded: SessionJsonapiResource | null = null;

    private _totalRecords: number = 0;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected recordedService: RecordedService,
        protected recordedSubscriptionService: RecordedSubscriptionService
    ) {
        super(cdr, layoutService, recordedService, recordedSubscriptionService);
    }

    ngOnInit(): void {
        super.ngOnInit();

        if (!this.embedded) {
            this.layoutService.changeTitle('Documentation');
        }
    }

    get count(): number {
        return this._totalRecords;
    }

    onMeta({ pagination }: {pagination: IPage}): void {
        this._totalRecords = pagination.total_records;
    }

    showParticipantInfo(participant: ParticipantJsonapiResource, recorded: SessionJsonapiResource): void {
        this.participantInfo = participant
        this.participantRecorded = recorded
    }

    clearParticipantInfo(): void {
        this.participantInfo = null
        this.participantRecorded = null
    }
}
