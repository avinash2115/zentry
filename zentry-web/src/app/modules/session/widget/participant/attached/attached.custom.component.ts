import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { SessionService } from '../../../session.service';
import { AttachedComponent } from './attached.component';
import { GoalJsonapiResource } from '../../../../../resources/user/participant/goal/goal.jsonapi.service';
import { takeUntil } from 'rxjs/operators';
import { ParticipantJsonapiResource } from '../../../../../resources/user/participant/participant.jsonapi.service';
import { TrackerJsonapiResource } from '../../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import { DataError } from '../../../../../shared/classes/data-error';
import * as moment from 'moment';
import { ProgressJsonapiResource } from '../../../../../resources/session/progress/progress.jsonapi.service';

@Component({
    selector: 'app-session-widget-participant-attached-custom',
    templateUrl: './attached.custom.component.html',
    styleUrls: ['./attached.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class AttachedCustomComponent extends AttachedComponent implements OnInit {
    private _goals: Array<GoalJsonapiResource> = [];
    private _progress: Array<ProgressJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected _sessionService: SessionService
    ) {
        super(cdr, _sessionService);
    }

    get goals(): Array<GoalJsonapiResource> {
        return this._goals;
    }

    ngOnInit(): void {
        this.sessionService
            .participantService
            .attached
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                if (data.length) {
                    this.participant = data[0];
                    this._goals = this.participant.goalsSortedActual;
                } else {
                    this.participant = null;
                    this._goals = null;
                }

                this.detectChanges();
            });

        this.sessionService
            .progressService
            .list
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ProgressJsonapiResource>) => {
                this._progress = data;
                this.detectChanges();
            });
    }

    goalsHaveProgress(): boolean {
        return this.goals.some((goal: GoalJsonapiResource) => this.hasProgress(goal))
    }

    hasProgress(goal: GoalJsonapiResource): boolean {
        return this._progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id).length > 0;
    }

    amount(goal: GoalJsonapiResource, tracker: TrackerJsonapiResource): number {
        return this._progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id && r.tracker.id === tracker.id).length;
    }

    track(goal: GoalJsonapiResource, entity: TrackerJsonapiResource): void {
        this.sessionService
            .progressService
            .add(
                moment().toISOString(),
                this.participant,
                goal,
                entity
            )
            .subscribe(() => {
                // todo mark
            }, (error: DataError) => this.fallback(error));
    }

    undo(): void {
        this.sessionService
            .progressService
            .undo()
            .subscribe(() => {
                // todo mark
            }, (error: DataError) => this.fallback(error));
    }
}
