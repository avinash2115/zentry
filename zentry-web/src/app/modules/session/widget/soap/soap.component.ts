import { ChangeDetectionStrategy, ChangeDetectorRef, Component, EventEmitter, OnInit, Output, ViewChild } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionService } from '../../session.service';
import { take, takeUntil } from 'rxjs/operators';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { ProgressJsonapiResource } from '../../../../resources/session/progress/progress.jsonapi.service';
import { FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { GoalJsonapiResource } from '../../../../resources/user/participant/goal/goal.jsonapi.service';
import { TrackerJsonapiResource } from '../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import { PerfectScrollbarDirective } from 'ngx-perfect-scrollbar';
import { ERate, IRate, Rates, SoapJsonapiResource } from '../../../../resources/session/soap/soap.jsonapi.service';
import { DataError } from '../../../../shared/classes/data-error';
import { LoaderService } from '../../../../shared/services/loader.service';
import { SwalService } from '../../../../shared/services/swal.service';

@Component({
    selector: 'app-session-widget-soap',
    templateUrl: './soap.component.html',
    styleUrls: ['./soap.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class SoapComponent extends BaseDetachedComponent implements OnInit {
    @ViewChild('formContainer', {static: false}) public formContainer: PerfectScrollbarDirective;
    @Output() completed: EventEmitter<void> = new EventEmitter<void>();

    public readonly rates: Array<IRate> = Rates;

    public form: FormGroup;

    public session: SessionJsonapiResource | null;
    public participants: Array<ParticipantJsonapiResource> = [];
    public progress: Array<ProgressJsonapiResource> = [];

    private _participantCurrent: ParticipantJsonapiResource;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected loaderService: LoaderService,
        private _sessionService: SessionService
    ) {
        super(cdr);
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    get participantCurrent(): ParticipantJsonapiResource {
        return this._participantCurrent;
    }

    get participantCurrentIndex(): number {
        return this.participants.findIndex((r: ParticipantJsonapiResource) => r.id === this.participantCurrent.id);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        combineLatest([
            this.sessionService.entity,
            this.sessionService.participantService.selected,
            this.sessionService.progressService.list
        ])
            .pipe(take(1))
            .subscribe(([session, participants, progress]: [SessionJsonapiResource | null, Array<ParticipantJsonapiResource>, Array<ProgressJsonapiResource>]) => {
                this.session = session;
                this.participants = participants;

                if (participants.length === 0) {
                    this.completed.emit();
                } else {
                    this.progress = progress;
                    this.participantActivate(this.participants[0]);
                    this.build();
                }
            });
    }

    participantActivate(entity: ParticipantJsonapiResource): void {
        this._participantCurrent = entity;
        this.detectChanges()
    }

    hasProgress(goal: GoalJsonapiResource): boolean {
        return this.progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id).length > 0;
    }

    amount(goal: GoalJsonapiResource, tracker: TrackerJsonapiResource): number {
        return this.progress.filter((r: ProgressJsonapiResource) => r.goal.id === goal.id && r.tracker.id === tracker.id).length;
    }

    present(): void {
        this.form.get(`participants.${this.participantCurrentIndex}.present`).patchValue(true);
    }

    absent(): void {
        this.form.get(`participants.${this.participantCurrentIndex}.present`).patchValue(false);
    }

    complete(): void {
        if (this.form.valid) {
            SwalService.warning({
                title: `You are going to complete SOAP notes`,
                text: `Are you sure?`,
                confirmButtonText: `Complete`
            }).then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    const {participants} = this.form.getRawValue();
                    const bag: Array<SoapJsonapiResource> = [];

                    participants.forEach((
                        p: {
                            resource: ParticipantJsonapiResource,
                            present: boolean,
                            activity: string,
                            note: string,
                            plan: string
                            goals: Array<{
                                resource: GoalJsonapiResource,
                                rate: ERate,
                            }>
                        }
                    ) => {
                        if (p.present && p.goals.length) {
                            p.goals.forEach((g: {
                                resource: GoalJsonapiResource,
                                rate: ERate,
                            }) => {
                                const single: SoapJsonapiResource = this.sessionService.soapService.make();
                                single.present = true;
                                single.rate = g.rate;
                                single.activity = p.activity;
                                single.note = p.note;
                                single.plan = p.plan;
                                single.addRelationship(p.resource, 'participant');
                                single.addRelationship(g.resource, 'goal');

                                bag.push(single);
                            });
                        } else {
                            const single: SoapJsonapiResource = this.sessionService.soapService.make();
                            single.present = false;
                            single.note = p.note;
                            single.addRelationship(p.resource, 'participant');

                            bag.push(single);
                        }
                    });

                    this.sessionService
                        .soapService
                        .bulk(bag)
                        .subscribe(() => {
                            this.loaderService.hide();
                            this.completed.emit();
                        }, ((error: DataError) => {
                            this.loaderService.hide();
                            this.fallback(error);
                            this.detectChanges();
                        }));
                } else {
                    this.loaderService.hide();
                }
            });
        }
    }

    copyActivityToGroup(): void {
        const currentActivity = this.form.get('participants.' + this.participantCurrentIndex).value.activity
        this.participants.forEach((_, participantIndex) => {
            this.form.get('participants.' + participantIndex + '.activity').setValue(currentActivity)
        })
    }

    skip(): void {
        SwalService.warning({
            title: `Cannot complete note`,
            text: `Progress, activity, note, and plan cannot be blank.`,
            confirmButtonText: `Go Back`,
            cancelButtonText: `Add note later`,
        }).then((answer: { value: boolean }) => {
            if (!answer.value) {
                this.completed.emit();
            }
        });
    }

    private build(): void {

        this.form = this.fb.group({
            participants: new FormArray(
                this.participants.map((r: ParticipantJsonapiResource) => {
                    let defaultNote: string = ''

                    r.goals.forEach((goal: GoalJsonapiResource) => {
                        let allAnswers: number = 0
                        let positiveAnswers: number = 0

                        goal.trackers.forEach((tracker: TrackerJsonapiResource) => {
                            const amount: number = this.amount(goal, tracker)
                            allAnswers += amount
                            if (tracker.isTrackerTypePositive) {
                                positiveAnswers += amount
                            }
                        })

                        const successPercents: number = !!allAnswers ? (100 / allAnswers) * positiveAnswers : 0;

                        defaultNote += `[Goal ${goal.name}: ${successPercents.toFixed(2)}%]\n`
                    })
                    return this.fb.group({
                        resource: [r],
                        present: [true, [Validators.required]],
                        activity: ['', []],
                        note: [defaultNote, []],
                        plan: ['', []],
                        goals: new FormArray(
                            r.goalsSortedActual.map((g: GoalJsonapiResource) => {
                                return this.fb.group({
                                    resource: [g],
                                    rate: [null, []],
                                });
                            })
                        )
                    });
                })
            )
        });

        this.form
            .valueChanges
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => this.detectChanges());

        this.loadingCompleted();
    }
}
