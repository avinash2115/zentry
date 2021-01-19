import { ChangeDetectionStrategy, ChangeDetectorRef, Component } from '@angular/core';
import { ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { SessionService } from '../../session.service';
import { NgSelectComponent } from '@ng-select/ng-select';
import { DataError } from '../../../../shared/classes/data-error';
import { LoaderService } from '../../../../shared/services/loader.service';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { ParticipantComponent } from './participant.component';

@Component({
    selector: 'app-session-widget-participant-custom',
    templateUrl: './participant.custom.component.html',
    styleUrls: ['./participant.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantCustomComponent extends ParticipantComponent {
    public formValue: ParticipantJsonapiResource[] = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected _sessionService: SessionService
    ) {
        super(cdr, loaderService, authenticationService, _sessionService);

        cdr.detach();
    }

    selectControlPick(entities: ParticipantJsonapiResource[], ngSelectComponent?: NgSelectComponent): void {
        if (ngSelectComponent) {
            ngSelectComponent.close();
        }

        this.formValue = entities;

        this.detectChanges();
    }

    add(): void {
        this.loaderService.show();

        this.sessionService
            .participantService
            .addMultiple(this.formValue)
            .subscribe(() => {
                this.isForm = false;
                this.formValue = null;
                this.loaderService.hide();
            }, (error: DataError) => {
                this.loaderService.hide();

                this.formValue = null;
                this.fallback(error);
            });

        this.detectChanges();
    }

    cancel(): void {
        this.isForm = false;
        this.formValue = null;
        this.detectChanges();
    }

    attach(entity: ParticipantJsonapiResource): void {
        if (this.sessionService.isStarted) {
            this.sessionService
                .participantService
                .attach(entity)
                .subscribe(() => {
                    this.detectChanges();
                }, (error: DataError) => {
                    this.fallback(error);
                });
        }
    }
}
