import { ChangeDetectionStrategy, Component, Input, Output, EventEmitter } from '@angular/core';
import {
    ParticipantJsonapiResource,
} from '../../../../../../resources/user/participant/participant.jsonapi.service';
import { SessionJsonapiResource } from '../../../../../../resources/session/session.jsonapi.service';

@Component({
    selector: 'app-session-recorded-shared-participant-foldover',
    templateUrl: './foldover.component.html',
    styleUrls: ['./foldover.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class FoldoverComponent {
    @Input() participant: ParticipantJsonapiResource | null;
    @Input() recorded: SessionJsonapiResource;

    @Output() close: EventEmitter<null> = new EventEmitter<null>();

    constructor() {}

    closeFoldover(): void {
        this.close.next(null)
    }
}
