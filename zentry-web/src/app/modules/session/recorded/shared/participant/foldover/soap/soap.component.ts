import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input } from '@angular/core';
import { BaseDetachedComponent } from '../../../../../../../shared/classes/abstracts/component/base-detached-component';
import { GoalJsonapiResource } from '../../../../../../../resources/user/participant/goal/goal.jsonapi.service';
import { ProgressJsonapiResource } from '../../../../../../../resources/session/progress/progress.jsonapi.service';
import { TrackerJsonapiResource } from '../../../../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import { RatesDictionary, SoapJsonapiResource } from '../../../../../../../resources/session/soap/soap.jsonapi.service';

@Component({
    selector: 'app-session-recorded-participant-list-soap',
    templateUrl: './soap.component.html',
    styleUrls: ['./soap.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class SoapComponent extends BaseDetachedComponent {
    @Input() goals: Array<GoalJsonapiResource> = [];
    @Input() progress: Array<ProgressJsonapiResource> = [];
    @Input() soaps: Array<SoapJsonapiResource> = [];

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

    get currentSoapRate(): string {
        return RatesDictionary[this.soapCurrent.rate]
    }

    get soapCurrent(): SoapJsonapiResource {
        return this.soaps.find((soap: SoapJsonapiResource) => soap.goal.id = this.goalCurrent.id)
    }

    get goalsNavigatable(): boolean {
        return this.goals.length > 1;
    }

    get goalCurrentIndex(): number {
        return this.goals.findIndex((r: GoalJsonapiResource) => r.id === this.goalCurrent.id);
    }

    get goalCurrentIndexHuman(): number {
        return this.goalCurrentIndex + 1;
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
