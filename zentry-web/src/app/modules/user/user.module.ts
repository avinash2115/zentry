import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../shared/shared.module';
import { ListComponent as DeviceListComponent } from './device/list/list.component';
import { UserRoutingModule } from './user-routing.module';
import { UserService } from './user.service';
import { UserSubscriptionService } from './user.subscription.service';
import { ProfileComponent } from './profile/profile.component';
import { ReactiveFormsModule } from '@angular/forms';
import { ListComponent as StorageListComponent } from './storage/list/list.component';
import { ListComponent as ParticipantListComponent } from './participant/list/list.component';
import { ListCustomComponent as ParticipantListCustomComponent } from './participant/list/list.custom.component';
import { ViewComponent as ParticipantViewComponent } from './participant/view/view.component';
import { ParticipantService } from './participant/participant.service';
import { ListComponent as TeamListComponent } from './team/list/list.component';
import { TeamService } from './team/team.service';
import { CreateComponent as ParticipantCreateComponent } from './participant/create/create.component';
import { NgSelectModule } from '@ng-select/ng-select';
import { ViewCustomComponent as ParticipantViewCustomComponent } from './participant/view/view.custom.component';
import { SessionModule } from '../session/session.module';
import { CreateComponent as TeamSchoolCreateComponent } from './team/school/create/create.component';
import { ViewComponent as TeamSchoolViewComponent } from './team/school/view/view.component';
import { SchoolService } from './team/school.service';
import { CreateComponent as TeamCreateComponent } from './team/create/create.component';
import { ViewComponent as TeamViewComponent } from './team/view/view.component';
import { ProfileCustomComponent } from './profile/profile.custom.component';
import { AssistantModule } from '../assistant/assistant.module';
import { AgePipe } from '../..//shared/pipes/age.pipe';

@NgModule({
    declarations: [
        ProfileComponent,
        ProfileCustomComponent,
        DeviceListComponent,
        StorageListComponent,
        ParticipantCreateComponent,
        ParticipantViewComponent,
        ParticipantViewCustomComponent,
        ParticipantListComponent,
        ParticipantListCustomComponent,
        TeamListComponent,
        TeamCreateComponent,
        TeamViewComponent,
        TeamSchoolCreateComponent,
        TeamSchoolViewComponent,
        AgePipe
    ],
    imports: [
        CommonModule,
        SharedModule,
        UserRoutingModule,
        ReactiveFormsModule,
        NgSelectModule,
        SessionModule,
        AssistantModule
    ],
    providers: [
        UserService,
        UserSubscriptionService,
        ParticipantService,
        TeamService,
        SchoolService
    ]
})
export class UserModule {
}
