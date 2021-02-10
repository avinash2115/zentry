import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionService } from '../../../session.service';
import { takeUntil } from 'rxjs/operators';
import { ParticipantJsonapiResource } from '../../../../../resources/user/participant/participant.jsonapi.service';
import { DataError } from '../../../../../shared/classes/data-error';

@Component({
    selector: 'app-session-widget-participant-attached',
    templateUrl: './attached.component.html',
    styleUrls: ['./attached.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class AttachedComponent extends BaseDetachedComponent implements OnInit {
    public participant: ParticipantJsonapiResource | null;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected _sessionService: SessionService,
    ) {
        super(cdr);
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    ngOnInit(): void {
        this.sessionService
            .participantService
            .attached
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                if (data.length) {
                    this.participant = data[0];
                } else {
                    this.participant = null;
                }

                this.detectChanges();
            });
    }

    remove(): void {
        this.sessionService
            .participantService
            .detach(this.participant)
            .subscribe(() => {
                this.detectChanges();
            }, (error: DataError) => {
                this.fallback(error);
            });
    }
}
