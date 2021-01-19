import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../../../resources/user/participant/participant.jsonapi.service';
import { RecordedService } from '../../../../recorded.service';
import { LoaderService } from '../../../../../../../shared/services/loader.service';
import { BaseDetachedComponent } from '../../../../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionJsonapiResource } from '../../../../../../../resources/session/session.jsonapi.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { takeUntil } from 'rxjs/operators';
import { GoalJsonapiResource } from '../../../../../../../resources/user/participant/goal/goal.jsonapi.service';
import { ProgressJsonapiResource } from '../../../../../../../resources/session/progress/progress.jsonapi.service';
import { TrackerJsonapiResource } from '../../../../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import { SwalService } from '../../../../../../../shared/services/swal.service';
import { DataError } from '../../../../../../../shared/classes/data-error';

@Component({
    selector: 'app-session-recorded-participant-view-goal',
    templateUrl: './goal.component.html',
    styleUrls: ['./goal.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class GoalComponent extends BaseDetachedComponent implements OnInit {
    public recorded: SessionJsonapiResource | null = null;
    public entity: UserParticipantJsonapiResource | null = null;
    private _goals: Array<GoalJsonapiResource> = [];
    private _goalCurrent: GoalJsonapiResource | null;
    private _progress: Array<ProgressJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected _recordedService: RecordedService
    ) {
        super(cdr);

        cdr.detach();
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    get goals(): Array<GoalJsonapiResource> {
        return this._goals;
    }

    get goalsNavigatable(): boolean {
        return this.goals.length > 1;
    }

    get goalCurrent(): GoalJsonapiResource {
        return this._goalCurrent;
    }

    get goalCurrentIndex(): number {
        return this._goals.findIndex((r: GoalJsonapiResource) => r.id === this.goalCurrent.id) + 1;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        combineLatest([
            this.recordedService.entity,
            this.recordedService.participantService.entity
        ]).pipe(takeUntil(this._destroy$))
            .subscribe(([recorded, participant]: [SessionJsonapiResource, UserParticipantJsonapiResource]) => {
                this.recorded = recorded;
                this.entity = participant;
                this._progress = this.recorded.participantsProgress(this.entity);

                this._progress.forEach((p: ProgressJsonapiResource) => {
                    if (!this.recorded.excludedGoals.includes(p.goal.id) && this._goals.findIndex((g: GoalJsonapiResource) => g.id === p.goal.id) === -1) {
                        this._goals.push(p.goal);
                    }
                });

                this.goalNavigate();
                this.loadingCompleted();
            });
    }

    goalNavigate(backwards: boolean = false): void {
        if (!(this.goalCurrent instanceof GoalJsonapiResource)) {
            this._goalCurrent = this.goals[0] || null;
        } else {
            const index: number = this.goals.findIndex((r: GoalJsonapiResource) => r.id === this.goalCurrent.id);

            if (backwards) {
                if (index === 0) {
                    this._goalCurrent = this.goals[this.goals.length - 1];
                } else {
                    this._goalCurrent = this.goals[index - 1];
                }
            } else {
                if (index === this.goals.length - 1) {
                    this._goalCurrent = this.goals[0];
                } else {
                    this._goalCurrent = this.goals[index + 1];
                }
            }
        }

        this.detectChanges();
    }

    removeGoal(goal: GoalJsonapiResource): void {
        SwalService.warning({
            title: 'Are you sure?',
            text: `You are going to remove goal ${goal.name} from this session!`
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                this.loaderService.show();
                this.recordedService.excludeGoal(goal)
                    .subscribe(() => {
                        this.loaderService.hide();
                        SwalService.toastSuccess({title: 'Goal was been removed from session!'});
                    }, ((error: DataError) => {
                        this.loaderService.hide();
                        this.fallback(error);
                    }));
            }
        })
    }

    hasProgress(goal: GoalJsonapiResource): boolean {
        return this._progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id).length > 0;
    }

    amount(goal: GoalJsonapiResource, tracker: TrackerJsonapiResource): number {
        return this._progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id && r.tracker.id === tracker.id).length;
    }

    track(entity: TrackerJsonapiResource): void {
        // this.sessionService
        //     .progressService
        //     .add(
        //         moment().toISOString(),
        //         this.participant,
        //         this.goalCurrent,
        //         entity
        //     )
        //     .subscribe(() => {
        //         //todo mark
        //     }, (error: DataError) => this.fallback(error));
    }

    undo(): void {
        // this.sessionService
        //     .progressService
        //     .undo(this.goalCurrent)
        //     .subscribe(() => {
        //         //todo mark
        //     }, (error: DataError) => this.fallback(error));
    }
}
