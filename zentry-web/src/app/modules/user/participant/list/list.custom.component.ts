import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, OnDestroy, ViewChild } from '@angular/core';
import { UserService } from '../../user.service';
import { ParticipantService } from '../participant.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { AgePipe } from '../../../../shared/pipes/age.pipe';
import { Router } from '@angular/router';
import { LayoutService } from '../../../../shared/services/layout.service';
import { ListComponent } from './list.component';
import { CrmService } from '../../../../shared/services/crm.service';
import { ListComponent as AssistantListComponent } from '../../../assistant/list/components/list/list.component';
import { EStatus } from '../../../../resources/session/session.jsonapi.service';

@Component({
    selector: 'app-user-participant-list-custom',
    templateUrl: './list.custom.component.html',
    styleUrls: ['./list.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, ParticipantService, AgePipe],
})
export class ListCustomComponent extends ListComponent implements OnInit, OnDestroy {
    @Input() embedded: boolean = false;
    @Input() filter: object = {};

    @ViewChild('assistantListComponent', {static: true}) public assistantListComponent: AssistantListComponent;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected userService: UserService,
        protected participantService: ParticipantService,
        public crmService: CrmService
    ) {
        super(cdr, router, layoutService, loaderService, userService, participantService);
    }

    get count(): number {
        return this.data.length;
    }

    get filterRemote(): object {
        return this.filter;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        if (!this.embedded) {
            this.layoutService.changeTitle('Caseload');
            this.layoutService.showBackButton();
        }

        if (this.embedded) {
            this.fetch();
        }
    }

    ngOnDestroy(): void {
        this.layoutService.hideBackButton();
    }

    fetch(): void {
        this.assistantListComponent.reloadDataWithCurrentPage();
    }
}
