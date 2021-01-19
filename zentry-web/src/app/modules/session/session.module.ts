import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { WidgetComponent } from './widget/widget.component';
import { SessionRoutingModule } from './session-routing.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';
import { SessionService } from './session.service';
import { ListComponent as RecordedListComponent } from './recorded/list/list.component';
import { SessionSubscriptionService } from './session.subscription.service';
import { ViewComponent as RecordedViewComponent } from './recorded/view/view.component';
import { RecordedService } from './recorded/recorded.service';
import { RecordedSubscriptionService } from './recorded/recorded.subscription.service';
import { TagInputModule } from 'ngx-chips';
import { AgmCoreModule } from '@agm/core';
import { NgSelectModule } from '@ng-select/ng-select';
import { UploadComponent as WidgetUploadComponent } from './widget/upload/upload.component';
import { SessionUploadService as SessionUploadService } from './session.upload.service';
import { ParticipantComponent as WidgetParticipantComponent } from './widget/participant/participant.component';
import { TrackpadComponent as WidgetTrackpadComponent } from './widget/trackpad/trackpad.component';
import { SessionParticipantService } from './session.participant.service';
import { SessionPoiService } from './session.poi.service';
import { ClipComponent as WidgetClipComponent } from './widget/clip/clip.component';
import { AttachedComponent as WidgetParticipantAttachedComponent } from './widget/participant/attached/attached.component';
import { ParticipantComponent as RecordedSharedParticipantComponent } from './recorded/shared/participant/participant.component';
import { ClipComponent as RecordedViewClipComponent } from './recorded/view/clip/clip.component';
import { TranscriptComponent as RecordedViewTranscriptComponent } from './recorded/view/transcript/transcript.component';
import { RecordedParticipantService } from './recorded/recorded.participant.service';
import { RecordedPoiService } from './recorded/recorded.poi.service';
import { RecordedPoiTranscriptService } from './recorded/recorded.poi.transcript.service';
import { WidgetCustomComponent } from './widget/widget.custom.component';
import { TrackpadCustomComponent as WidgetTrackpadCustomComponent } from './widget/trackpad/trackpad.custom.component';
import { AttachedCustomComponent as WidgetAttachedCustomComponent } from './widget/participant/attached/attached.custom.component';
import { ParticipantCustomComponent as WidgetParticipantCustomComponent } from './widget/participant/participant.custom.component';
import { ListCustomComponent, ListCustomComponent as RecordedListCustomComponent } from './recorded/list/list.custom.component';
import { CalendarComponent } from './calendar/calendar.component';
import { CalendarComponent as WidgetCalendarComponent } from './widget/calendar/calendar.component';
import { CalendarModule } from 'angular-calendar';
import { SessionProgressService } from './session.progress.service';
import { NgCircleProgressModule } from 'ng-circle-progress';
import { SessionSoapService } from './session.soap.service';
import { SoapComponent as WidgetSoapComponent } from './widget/soap/soap.component';
import { ViewCustomComponent as RecordedViewCustomComponent } from './recorded/view/view.custom.component';
import { ParticipantComponent as RecordedParticipantComponent } from './recorded/view/participant/participant.component';
import { ViewComponent as RecordedParticipantViewComponent } from './recorded/view/participant/view/view.component';
import { ClipCustomComponent as RecordedViewClipCustomComponent } from './recorded/view/clip/clip.custom.component';
import { ParticipantCustomComponent as RecordedSharedParticipantCustomComponent } from './recorded/shared/participant/participant.custom.component';
import { FoldoverComponent as RecordedSharedParticipantFoldoverComponent } from './recorded/shared/participant/foldover/foldover.component';
import { SoapComponent as RecordedSharedParticipantSoapComponent } from './recorded/shared/participant/foldover/soap/soap.component';
import { GoalComponent as RecordedSharedParticipantGoalComponent } from './recorded/shared/participant/foldover/goal/goal.component';
import { GoalComponent as RecordedParticipantViewGoalComponent } from './recorded/view/participant/view/goal/goal.component';
import { SoapComponent as RecordedParticipantViewSoapComponent } from './recorded/view/participant/view/soap/soap.component';
import { NoteComponent as RecordedViewNoteComponent } from './recorded/view/note/note.component';
import { AssistantModule } from '../assistant/assistant.module';
import { NotificationListComponent } from '../../components/notification/notification.component';

@NgModule({
    declarations: [
        WidgetComponent,
        WidgetUploadComponent,
        WidgetParticipantComponent,
        WidgetParticipantAttachedComponent,
        WidgetTrackpadComponent,
        WidgetClipComponent,
        RecordedListComponent,
        RecordedListCustomComponent,
        RecordedViewComponent,
        RecordedViewNoteComponent,
        RecordedViewCustomComponent,
        RecordedViewClipComponent,
        RecordedViewClipCustomComponent,
        RecordedParticipantComponent,
        RecordedParticipantViewComponent,
        RecordedParticipantViewGoalComponent,
        RecordedParticipantViewSoapComponent,
        RecordedSharedParticipantComponent,
        RecordedSharedParticipantCustomComponent,
        RecordedSharedParticipantFoldoverComponent,
        RecordedSharedParticipantGoalComponent,
        RecordedSharedParticipantSoapComponent,
        RecordedViewTranscriptComponent,

        NotificationListComponent,
        // customs

        CalendarComponent,
        WidgetCustomComponent,
        WidgetTrackpadCustomComponent,
        WidgetAttachedCustomComponent,
        WidgetParticipantCustomComponent,
        WidgetCalendarComponent,
        WidgetSoapComponent,
    ],
    imports: [
        CommonModule,
        SharedModule,
        FormsModule,
        NgSelectModule,
        ReactiveFormsModule,
        AgmCoreModule,
        SessionRoutingModule,
        CalendarModule,
        AssistantModule,
        TagInputModule,
        NgCircleProgressModule,
    ],
    exports: [
        RecordedListComponent,
        ListCustomComponent,
        CalendarComponent
    ],
    providers: [
        SessionService,
        SessionSubscriptionService,
        SessionParticipantService,
        SessionPoiService,
        SessionProgressService,
        SessionSoapService,
        SessionUploadService,
        RecordedService,
        RecordedSubscriptionService,
        RecordedParticipantService,
        RecordedPoiService,
        RecordedPoiTranscriptService
    ]
})
export class SessionModule {
}
