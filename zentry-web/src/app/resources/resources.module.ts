import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PasswordResetJsonapiService } from './helpers/password/reset-jsonapi.service';
import { UserJsonapiService } from './user/user.jsonapi.service';
import { DeviceJsonapiService as UserDeviceJsonapiService } from './user/device/device.jsonapi.service';
import { PoiJsonapiService as UserPoiJsonapiService } from './user/poi/poi.jsonapi.service';
import { BacktrackJsonapiService as UserBacktrackJsonapiService } from './user/backtrack/backtrack.jsonapi.service';
import { ConnectingPayloadJsonapiService as UserDeviceConnectingPayloadJsonapiService } from './user/device/connecting-payload/connecting-payload.jsonapi.service';
import { SessionJsonapiService } from './session/session.jsonapi.service';
import { PoiJsonapiService as SessionPoiJsonapiService } from './session/poi/poi.jsonapi.service';
import { StreamJsonapiService as SessionStreamJsonapiService } from './session/stream/stream.jsonapi.service';
import { ProfileJsonapiService as UserProfileJsonapiService } from './user/profile/profile.jsonapi.service';
import { UrlJsonapiService as FileTemporaryUrlJsonapiService } from './file/temporary/url/url.jsonapi.service';
import { TokenJsonapiService as LoginTokenJsonapiService } from './login/token/token.jsonapi.service';
import { StorageJsonapiService as UserStorageJsonapiService } from './user/storage/storage.jsonapi.service';
import { DriverJsonapiService as UserStorageDriverJsonapiService } from './user/storage/driver/driver.jsonapi.service';
import { DriverJsonapiService as SSODriverJsonapiService } from './sso/driver/driver.jsonapi.service';
import { SharedJsonapiService } from './shared/shared.jsonapi.service';
import { ParticipantJsonapiService as UserParticipantJsonapiService } from './user/participant/participant.jsonapi.service';
import { ParticipantJsonapiService as SessionPoiParticipantJsonapiService } from './session/poi/participant/participant.jsonapi.service';
import { WordJsonapiService as TranscriptWordJsonapiService } from './transcript/word/word.jsonapi.service';
import { PhraseJsonapiService as TranscriptPhraseJsonapiService } from './transcript/phrase/phrase.jsonapi.service';
import { TranscriptJsonapiService } from './transcript/transcript.jsonapi.service';
import { TokenJsonapiService as SessionStreamTokenJsonapiService } from './session/stream/token/token.jsonapi.service';
import { NoteJsonapiService as SessionNoteJsonapiService } from './session/note/note.jsonapi.service';
import { TeamJsonapiService as UserTeamJsonapiService } from './user/team/team.jsonapi.service';
import { SchoolJsonapiService as UserTeamSchoolJsonapiService } from './user/team/school/school.jsonapi.service';
import { TherapyJsonapiService as UserParticipantTherapyJsonapiService } from './user/participant/therapy/therapy.jsonapi.service';
import { GoalJsonapiService as UserParticipantGoalJsonapiService } from './user/participant/goal/goal.jsonapi.service';
import { IepJsonapiService as UserParticipantIepJsonapiService } from './user/participant/iep/iep.jsonapi.service';
import { TrackerJsonapiService as UserParticipantGoalTrackerJsonapiService } from './user/participant/goal/tracker/tracker.jsonapi.service';
import { SourceJsonapiService as CrmSourceJsonapiService } from './crm/source/source.jsonapi.service';
import { CrmJsonapiService as UserCrmJsonapiService } from './user/crm/crm.jsonapi.service';
import { DriverJsonapiService as UserCrmDriverJsonapiService } from './user/crm/driver/driver.jsonapi.service';
import { ProgressJsonapiService as SessionProgressJsonapiService } from './session/progress/progress.jsonapi.service';
import { SyncLogJsonapiService as CrmSyncLogJsonapiService } from './crm/sync-log/sync-log.jsonapi.service';
import { SoapJsonapiService as SessionSoapJsonapiService } from './session/soap/soap.jsonapi.service';
import { ServiceJsonapiService } from './service/service.jsonapi.service';
import { ProviderJsonapiService} from './provider/provider.jsonapi.service'

@NgModule({
    declarations: [],
    providers: [
        // region Shared
        SharedJsonapiService,
        // endregion

        // region SSO
        SSODriverJsonapiService,
        // endregion

        // region Login
        LoginTokenJsonapiService,
        // endregion

        // region File
        FileTemporaryUrlJsonapiService,
        // endregion

        // region Transcript
        TranscriptJsonapiService,
        TranscriptWordJsonapiService,
        TranscriptPhraseJsonapiService,
        // endregion

        // region User
        UserJsonapiService,
        UserProfileJsonapiService,
        UserDeviceJsonapiService,
        UserDeviceConnectingPayloadJsonapiService,
        UserPoiJsonapiService,
        UserBacktrackJsonapiService,
        UserStorageJsonapiService,
        UserStorageDriverJsonapiService,
        UserParticipantJsonapiService,
        UserParticipantTherapyJsonapiService,
        UserParticipantGoalJsonapiService,
        UserParticipantIepJsonapiService,
        UserParticipantGoalTrackerJsonapiService,
        UserTeamJsonapiService,
        UserTeamSchoolJsonapiService,
        UserCrmJsonapiService,
        UserCrmDriverJsonapiService,
        CrmSourceJsonapiService,
        CrmSyncLogJsonapiService,
        // endregion

        // region Service
        ServiceJsonapiService,
        // endregion

        // region Provider
        ProviderJsonapiService,
        // endregion

        // region Session
        SessionJsonapiService,
        SessionProgressJsonapiService,
        SessionSoapJsonapiService,
        SessionPoiJsonapiService,
        SessionPoiParticipantJsonapiService,
        SessionStreamJsonapiService,
        SessionStreamTokenJsonapiService,
        SessionNoteJsonapiService,
        // endregion

        // region Helpers
        PasswordResetJsonapiService
        // endregion
    ],
    imports: [
        CommonModule
    ]
})
export class ResourcesModule {
}
