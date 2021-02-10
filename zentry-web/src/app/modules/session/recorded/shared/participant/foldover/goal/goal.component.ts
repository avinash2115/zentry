import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input } from '@angular/core';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../../../resources/user/participant/participant.jsonapi.service';
import { BaseDetachedComponent } from '../../../../../../../shared/classes/abstracts/component/base-detached-component';
import { GoalJsonapiResource } from '../../../../../../../resources/user/participant/goal/goal.jsonapi.service';
import { ProgressJsonapiResource } from '../../../../../../../resources/session/progress/progress.jsonapi.service';
import { TrackerJsonapiResource } from '../../../../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';

@Component({
    selector: 'app-session-recorded-participant-list-goal',
    templateUrl: './goal.component.html',
    styleUrls: ['./goal.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class GoalComponent extends BaseDetachedComponent {
    @Input() progress: Array<ProgressJsonapiResource> = [];

    private _goalCurrent: GoalJsonapiResource | null;

    constructor(
        protected cdr: ChangeDetectorRef,
    ) {
        super(cdr);
    }

    get goalCurrent(): GoalJsonapiResource {
        if (!this._goalCurrent && !!this.goals.length) {
            return this.goals[0]
        }
        return this._goalCurrent
    }

    get goals(): Array<GoalJsonapiResource> {
        return this.progress.reduce((acc: Array<GoalJsonapiResource>, p: ProgressJsonapiResource) => {
            if (acc.findIndex((g: GoalJsonapiResource) => g.id === p.goal.id) === -1) {
                acc.push(p.goal);
            }
            return acc;
        }, []);
    }

    get goalsNavigatable(): boolean {
        return this.goals.length > 1;
    }

    get goalCurrentIndex(): number {
        return this.goals.findIndex((r: GoalJsonapiResource) => r.id === this.goalCurrent.id) + 1;
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

    hasProgress(goal: GoalJsonapiResource): boolean {
        return this.progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id).length > 0;
    }

    amount(goal: GoalJsonapiResource, tracker: TrackerJsonapiResource): number {
        return this.progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id && r.tracker.id === tracker.id).length;
    }
}
