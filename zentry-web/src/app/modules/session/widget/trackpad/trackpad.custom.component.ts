import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { LoaderService } from '../../../../shared/services/loader.service';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { SessionService } from '../../session.service';
import { TrackpadComponent } from './trackpad.component';
import { take } from 'rxjs/operators';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';

@Component({
    selector: 'app-session-widget-trackpad-custom',
    templateUrl: './trackpad.custom.component.html',
    styleUrls: ['./trackpad.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackpadCustomComponent extends TrackpadComponent {
    private _progress: number = 0;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected _sessionService: SessionService
    ) {
        super(cdr, loaderService, authenticationService, _sessionService);

        cdr.detach();
    }

    get progress(): number {
        return this._progress;
    }

    progressUpdate(milliseconds: number): void {
        const seconds: number = Math.floor(milliseconds / 1000);
        this.sessionService
            .entityLoaded
            .pipe(take(1))
            .subscribe((entity: SessionJsonapiResource) => {
                let totalSeconds: number = 3600;

                if (entity.isScheduled) {
                    totalSeconds = entity.scheduledToDate.diff(entity.scheduledOnDate, 's')
                }

                if (seconds > totalSeconds) {
                    totalSeconds = seconds;
                }

                this._progress = 100 * seconds / totalSeconds;

                this.detectChanges();
            });
    }
}
