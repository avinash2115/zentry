import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../../../resources/user/participant/participant.jsonapi.service';
import { RecordedService } from '../../../../recorded.service';
import { LoaderService } from '../../../../../../../shared/services/loader.service';
import { BaseDetachedComponent } from '../../../../../../../shared/classes/abstracts/component/base-detached-component';
import { IParticipantProgressCalculation, SessionJsonapiResource } from '../../../../../../../resources/session/session.jsonapi.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { takeUntil, tap } from 'rxjs/operators';
import { GoalJsonapiResource } from '../../../../../../../resources/user/participant/goal/goal.jsonapi.service';
import { ProgressJsonapiResource } from '../../../../../../../resources/session/progress/progress.jsonapi.service';
import { TrackerJsonapiResource } from '../../../../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import { ERate, IRate, Rates, SoapJsonapiResource } from '../../../../../../../resources/session/soap/soap.jsonapi.service';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SwalService } from '../../../../../../../shared/services/swal.service';
import { DataError } from '../../../../../../../shared/classes/data-error';

@Component({
    selector: 'app-session-recorded-participant-view-soap',
    templateUrl: './soap.component.html',
    styleUrls: ['./soap.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class SoapComponent extends BaseDetachedComponent implements OnInit {
    public recorded: SessionJsonapiResource | null = null;
    public entity: UserParticipantJsonapiResource | null = null;

    public readonly rates: Array<IRate> = Rates;

    public form: FormGroup;

    private _goals: Array<GoalJsonapiResource> = [];
    private _goalCurrent: GoalJsonapiResource | null;
    private _progress: Array<ProgressJsonapiResource> = [];
    private _soaps: Array<SoapJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
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
        return this._goals.findIndex((r: GoalJsonapiResource) => r.id === this.goalCurrent.id);
    }

    get goalCurrentIndexHuman(): number {
        return this.goalCurrentIndex + 1;
    }

    ngOnInit(): void {
        this.loadingTrigger();
        combineLatest([
            this.recordedService.entity,
            this.recordedService.participantService.entity
        ]).pipe(
            tap(() => this.loadingTrigger()),
            takeUntil(this._destroy$)
        )
            .subscribe(([recorded, participant]: [SessionJsonapiResource, UserParticipantJsonapiResource]) => {
                this.recorded = recorded;
                this.entity = participant;
                this._progress = this.recorded.participantsProgress(this.entity);
                this._soaps = this.recorded.participantsSoaps(this.entity);
                this._goals = [];

                this.entity.goalsSortedActual.filter((goal: GoalJsonapiResource) => !this.recorded.excludedGoals.includes(goal.id)).forEach((p: GoalJsonapiResource) => {
                    if (this._goals.findIndex((g: GoalJsonapiResource) => g.id === p.id) === -1) {
                        this._goals.push(p);
                    }
                });

                this.goalNavigate();
                this.build();
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

    present(): void {
        this.form.get(`present`).patchValue(true);
    }

    absent(): void {
        this.form.get(`present`).patchValue(false);
    }

    submit(): void {
        if (this.form.valid) {
            SwalService.warning({
                title: `You are going to complete SOAP notes for ${this.entity.fullname || this.entity.email}`,
                text: `Are you sure?`,
                confirmButtonText: `Complete`
            }).then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    const {
                        present,
                        note,
                        activity,
                        plan,
                        goals
                    } = this.form.getRawValue();

                    const bag: Array<SoapJsonapiResource> = [];

                    if (present && goals.length) {
                        goals.forEach((g: {
                            resource: GoalJsonapiResource,
                            rate: ERate
                        }) => {
                            const single: SoapJsonapiResource = this._soaps.find((r: SoapJsonapiResource) => r.goal.id === g.resource.id) || this.recordedService.soapService.make();

                            single.present = true;
                            single.rate = g.rate;
                            single.activity = activity;
                            single.note = note;
                            single.plan = plan;
                            single.addRelationship(this.entity, 'participant');
                            single.addRelationship(g.resource, 'goal');

                            bag.push(single);
                        });
                    } else {
                        const single: SoapJsonapiResource = this._soaps[0] || this.recordedService.soapService.make();

                        single.present = false;
                        single.note = note;
                        single.addRelationship(this.entity, 'participant');

                        bag.push(single);
                    }

                    this.recordedService
                        .soapService
                        .bulk(bag)
                        .subscribe(() => {
                            this.loaderService.hide();
                            SwalService.toastSuccess({title: 'SOAP note has been saved!'});
                        }, ((error: DataError) => {
                            this.loaderService.hide();
                            this.fallback(error);
                            this.detectChanges();
                        }));
                } else {
                    this.loaderService.hide();
                }
            });
        } else {
            this.form.updateValueAndValidity();
            this.form.markAllAsTouched();

            this.detectChanges();
        }
    }

    // copyActivityToGroup(): void {
    // }

    cancel(): void {
        this.form.reset();
        this.detectChanges();
    }

    private build(): void {
        let defaultNote: string = '';

        this.recorded.progress.reduce((result: Array<{ goal: GoalJsonapiResource, trackers: Array<TrackerJsonapiResource> }>, r: ProgressJsonapiResource) => {
            const existingIndex: number = result.findIndex((rr: { goal: GoalJsonapiResource, trackers: Array<TrackerJsonapiResource> }) => rr.goal.id === r.goal.id);

            if (existingIndex === -1) {
                result.push({
                    goal: r.goal,
                    trackers: [r.tracker]
                });
            } else {
                result[existingIndex].trackers.push(r.tracker);
            }

            return result;
        }, []).forEach((r: { goal: GoalJsonapiResource, trackers: Array<TrackerJsonapiResource> }) => {
            const successPercents: number = (100 / r.trackers.length) * r.trackers.filter((t: TrackerJsonapiResource) => t.isTrackerTypePositive).length;

            if (!!defaultNote) {
                defaultNote += '\r\n';
            }

            defaultNote += `[Goal ${r.goal.name}: ${successPercents.toFixed(2)}%]`
        });

        if (!!defaultNote) {
            defaultNote += '\r\n';
        }

        this.form = this.fb.group({
            present: [this._soaps[0] instanceof SoapJsonapiResource ? this._soaps[0].present : true, [Validators.required]],
            note: [this._soaps[0] instanceof SoapJsonapiResource && !!this._soaps[0].note ? this._soaps[0].note : defaultNote, []],
            activity: [this._soaps[0] instanceof SoapJsonapiResource ? this._soaps[0].activity : '', []],
            plan: [this._soaps[0] instanceof SoapJsonapiResource ? this._soaps[0].plan : '', []],
            goals: new FormArray(
                this.goals.map((g: GoalJsonapiResource) => {
                    const exists: SoapJsonapiResource | undefined = this._soaps.find((r: SoapJsonapiResource) => r.goal.id === g.id);
                    return this.fb.group({
                        resource: [g],
                        rate: [exists instanceof SoapJsonapiResource ? exists.rate : null, []]
                    });
                })
            )
        });

        if (this.recorded.isLocked) {
            this.form.disable()
        }

        this.form
            .valueChanges
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => this.detectChanges());

        this.loadingCompleted();
    }
}
