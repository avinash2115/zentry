import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { UserService } from '../../user.service';
import { ParticipantService } from '../participant.service';
import { AbstractControl, FormArray, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { EGenders, ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { LayoutService } from '../../../../shared/services/layout.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { ViewComponent } from './view.component';
import { filter, switchMap, take, takeUntil } from 'rxjs/operators';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../shared/consts/form/patterns';
import { WhitespaceValidator } from '../../../../shared/validators/whitespace.validator';
import { DataError } from '../../../../shared/classes/data-error';
import { TeamJsonapiResource } from '../../../../resources/user/team/team.jsonapi.service';
import { SchoolJsonapiResource } from '../../../../resources/user/team/school/school.jsonapi.service';
import { EFrequencies } from '../../../../resources/user/participant/therapy/therapy.jsonapi.service';
import { SwalService } from '../../../../shared/services/swal.service';
import { RecordedService } from '../../../session/recorded/recorded.service';
import { Subject } from 'rxjs/internal/Subject';
import { GoalJsonapiResource } from '../../../../resources/user/participant/goal/goal.jsonapi.service';
import { IepJsonapiResource } from '../../../../resources/user/participant/iep/iep.jsonapi.service';
import { Observable } from 'rxjs/internal/Observable';
import { Observer } from 'rxjs/internal/types';
import { IDataObject } from '../../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../../../vendor/vp-ngx-jsonapi/services/converter';
import { IAcknowledgeResponse } from '../../../../shared/interfaces/acknowledge-response.interface';
import { ETypes, ITypes, Types, EColors, EIcons, TrackerJsonapiResource } from '../../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import * as moment from 'moment';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';

enum ESteps {
    general,
    ieps,
    goals,
    recordings
}

@Component({
    selector: 'app-user-participant-view-custom',
    templateUrl: './view.custom.component.html',
    styleUrls: ['./view.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, ParticipantService]
})
export class ViewCustomComponent extends ViewComponent implements OnInit {
    public readonly goalTypes: Array<ITypes> = Types;

    public readonly genders: Array<{
        value: EGenders,
        label: string,
    }> = [
        {
            value: EGenders.male,
            label: 'Male'
        },
        {
            value: EGenders.female,
            label: 'Female'
        }
    ];

    public readonly frequencies: Array<{
        value: EFrequencies,
        label: string,
    }> = [
        {
            value: EFrequencies.daily,
            label: 'Daily'
        },
        {
            value: EFrequencies.weekly,
            label: 'Weekly'
        },
        {
            value: EFrequencies.monthly,
            label: 'Monthly'
        }
    ];

    public readonly trackersIcons: Array<{
        value: EIcons,
        label: string,
    }> = [
        {
            value: EIcons.yes,
            label: EColors.positive
        },
        {
            value: EIcons.no,
            label: EColors.negative
        },
        {
            value: EIcons.assist,
            label: EColors.neutral
        }
    ];

    public recordingsFilter: object = {};

    public formGoal: FormGroup;
    public formIep: FormGroup;
    private readonly _formGoalDestroy$: Subject<boolean> = new Subject<boolean>();
    private readonly _formIepDestroy$: Subject<boolean> = new Subject<boolean>();

    public entity: ParticipantJsonapiResource | null = null;
    public steps: typeof ESteps = ESteps;
    private _stepActive: ESteps = ESteps.general;
    private _teams: Array<TeamJsonapiResource> = [];
    private _schools: Array<SchoolJsonapiResource> = [];
    private _sessions: Array<SessionJsonapiResource> = [];
    private _ieps: Array<IepJsonapiResource> = [];

    get teams(): Array<TeamJsonapiResource> {
        return this._teams;
    }

    get schools(): Array<SchoolJsonapiResource> {
        return this._schools;
    }

    get sessions(): Array<SessionJsonapiResource> {
        return this._sessions;
    }

    get trackers(): Array<AbstractControl> {
        return this.formGoal ? (this.formGoal.get('trackers') as FormArray).controls as Array<AbstractControl> : [];
    }

    get ieps(): Array<IepJsonapiResource> {
        return this._ieps
    }

    get activeIepResourceId(): string {
        return this.formIep ? this.formIep.get('resource').value.id : ''
    }

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected userService: UserService,
        protected participantService: ParticipantService,
        private recordedService: RecordedService
    ) {
        super(cdr, fb, router, activatedRoute, layoutService, loaderService, authenticationService, userService, participantService);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('Students');

        this.userService
            .participantService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: ParticipantJsonapiResource | null) => entity instanceof ParticipantJsonapiResource),
                switchMap((entity: ParticipantJsonapiResource) => {
                    this.entity = entity;
                    this._ieps = entity.ieps;

                    this.layoutService.changeTitle(`Students | ${this.entity.fullname}`);

                    this.recordingsFilter = {participants: {collection: [entity.id]}};
                    return this.recordedService.list(this.recordingsFilter);
                })
            )
            .subscribe((data: Array<SessionJsonapiResource>) => {
                this._sessions = data;

                if (this.activatedRoute.snapshot.queryParams.edit) {
                    this.router.navigate([], {relativeTo: this.activatedRoute, queryParams: {}});
                    this.build();
                }

                this.loadingCompleted();
            });

        this.userService
            .participantService
            .get(this.activatedRoute.snapshot.params.studentId)
            .subscribe(() => {
            }, (error: DataError) => this.fallback(error));
    }

    typeSelectNeeded(icon: EIcons): boolean {
        return EIcons.assist === icon
    }

    stepIsActive(value: ESteps): boolean {
        return this._stepActive === value;
    }

    stepActivate(value: ESteps): void {
        this._stepActive = value;

        this.detectChanges();
    }

    build(): void {
        if (!this.entity.hasSources) {
            this.fetchTeams();
        }
    }

    submit(): void {
        if (this.form.valid) {
            this.loaderService.show();

            const {
                first_name,
                last_name,
                email,
                dob,
                phone_code,
                phone_number,
                parent_phone_number,
                parent_email,
                gender,
                team_id,
                school_id,
                diagnosis,
                frequency,
                sessions_amount_planned,
                treatment_amount_planned,
                notes,
                private_notes
            } = this.form.getRawValue();

            this.userService
                .participantService
                .entity
                .pipe(
                    take(1),
                    switchMap((r: ParticipantJsonapiResource) => {
                        r.firstName = first_name;
                        r.lastName = last_name;
                        r.email = email;
                        r.dob = dob;
                        r.phoneCode = phone_code;
                        r.phoneNumber = phone_number;
                        r.gender = gender;
                        r.parentPhoneNumber = parent_phone_number;
                        r.parentEmail = parent_email;

                        r.therapy.diagnosis = diagnosis;
                        r.therapy.frequency = frequency;
                        r.therapy.sessionsAmountPlanned = sessions_amount_planned;
                        r.therapy.treatmentAmountPlanned = treatment_amount_planned;
                        r.therapy.notes = notes;
                        r.therapy.privateNotes = private_notes;

                        if (!!team_id) {
                            const team: TeamJsonapiResource | undefined = this.teams.find((t: TeamJsonapiResource) => t.id === team_id);

                            if (team instanceof TeamJsonapiResource) {
                                r.addRelationship(team, 'team');
                            }

                            if (!!school_id) {
                                const school: SchoolJsonapiResource | undefined = this.schools.find((s: SchoolJsonapiResource) => s.id === school_id);

                                if (school instanceof SchoolJsonapiResource) {
                                    r.addRelationship(school, 'school');
                                }
                            } else {
                                if (r.school instanceof SchoolJsonapiResource) {
                                    r.removeRelationship('school', r.school.id);
                                }
                            }
                        } else {
                            if (r.team instanceof TeamJsonapiResource) {
                                r.removeRelationship('team', r.team.id);
                            }
                        }

                        return this.userService.participantService.save();
                    })
                )
                .subscribe((r: ParticipantJsonapiResource) => {
                    this.loaderService.hide();

                    SwalService.toastSuccess({title: `${r.fullname} has been saved!`});

                    this.cancel();
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.fallback(error);
                });
        } else {
            this.form.markAllAsTouched();
            this.form.updateValueAndValidity();
            this.detectChanges();
        }
    }

    cancel(): void {
        this.form = null;
        this.formGoal = null;
        this.formIep = null;
        this._formGoalDestroy$.next(true);
        this._formIepDestroy$.next(true);

        this.detectChanges();

        this.userService
            .participantService
            .refresh();
    }

    buildGoal(goal: GoalJsonapiResource = null): void {
        let goalResource: GoalJsonapiResource | null = goal;

        if (!(goalResource instanceof GoalJsonapiResource)) {
            goalResource = this.participantService.participantGoalJsonapiService.new();
        }

        if (!goalResource.trackers.length) {
            goalResource.addRelationshipsArray(this.participantService.participantGoalTrackerJsonapiService.default, 'trackers');
        }

        this.formGoal = this.fb.group({
            resource: [goalResource, []],
            name: [goalResource.name, [Validators.required, WhitespaceValidator]],
            iep: [goalResource.iep ? goalResource.iep.id : null, [Validators.required]],
            trackers: new FormArray(goalResource.trackers.map((r: TrackerJsonapiResource) => {
                return this.fb.group({
                    name: [r.name, [Validators.maxLength(255), Validators.required, WhitespaceValidator]],
                    icon: [r.icon, [Validators.required]],
                    tracker_type: [r.trackerType, [Validators.required]],
                });
            }))
        });

        if (goalResource.hasSources) {
            this.formGoal.get('name').disable();
        }

        this.formGoal
            .valueChanges
            .pipe(takeUntil(this._formGoalDestroy$))
            .subscribe(() => this.detectChanges());

        this.detectChanges();
    }

    submitGoal(): void {
        if (this.formGoal.valid) {
            this.loaderService.show();

            const {
                resource,
                name,
                trackers,
                iep: iepId
            } = this.formGoal.getRawValue();

            const goalResource: GoalJsonapiResource = resource;
            const iepResource: IepJsonapiResource = this.entity.ieps.find((iep: IepJsonapiResource) => iep.id === iepId);

            goalResource.name = name;

            if (iepResource) {
                goalResource.addRelationship(iepResource, 'iep');
            }

            (trackers as Array<{ icon: EIcons, name: string }>).forEach((v: { icon: EIcons, tracker_type: ETypes, name: string }, i: number) => {
                const tracker: TrackerJsonapiResource = goalResource.trackers[i];
                tracker.name = v.name;
                tracker.trackerType = v.tracker_type;
                tracker.icon = v.icon;
            });

            (new Observable((observer: Observer<GoalJsonapiResource>) => {
                goalResource.save({
                    preserveRelationships: true,
                    beforepath: goalResource.is_new ? `${this.entity.path}/relationships` : ''
                }, (response: IDataObject) => {
                    const r: GoalJsonapiResource = this.userService.participantService.participantGoalJsonapiService.new();
                    Converter.build(response, r);

                    observer.next(r);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
            })).subscribe((goal: GoalJsonapiResource) => {
                this.loaderService.hide();

                if (this.entity.goals.findIndex((r: GoalJsonapiResource) => r.id === goal.id) === -1) {
                    this.entity.addRelationship(goal, 'goals');
                }

                SwalService.toastSuccess({title: `Goal has been saved!`});

                this.cancel();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });

        } else {
            this.formGoal.markAllAsTouched();
            this.formGoal.updateValueAndValidity();
            this.detectChanges();
        }
    }

    removeGoal(entity: GoalJsonapiResource): void {
        SwalService.warning({
            title: 'Are you sure?',
            text: `Do you really want to remove ${entity.name} goal from ${this.entity.fullname}?`,
            confirmButtonText: `Remove`,
            cancelButtonText: 'Cancel'
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                (new Observable((observer: Observer<boolean>) => {
                    entity.customCall({
                        method: 'DELETE'
                    }, (response: IAcknowledgeResponse) => {
                        observer.next(response.acknowledge);
                        observer.complete();
                    }, (error: DataError) => observer.error(error));
                })).subscribe(() => {
                    this.loaderService.hide();

                    if (this.entity.goals.findIndex((r: GoalJsonapiResource) => r.id === entity.id) !== -1) {
                        this.entity.removeRelationship('goals', entity.id);
                    }

                    SwalService.toastSuccess({title: `Goal has been removed!`});

                    this.detectChanges();
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.fallback(error);
                });
            }
        });
    }

    buildIep(iep: IepJsonapiResource = null): void {
        let iepResource: IepJsonapiResource | null = iep;

        if (!(iepResource instanceof IepJsonapiResource)) {
            iepResource = this.participantService.participantIepJsonapiService.new();
        }

        this.formIep = this.fb.group({
            resource: [iepResource, []],
            date_actual: [iepResource.dateActual, [Validators.required]],
            date_reeval: [iepResource.dateReeval, [Validators.required]],
        });

        this.formIep
            .valueChanges
            .pipe(takeUntil(this._formIepDestroy$))
            .subscribe(() => this.detectChanges());

        this.detectChanges();
    }

    submitIep(): void {
        if (this.formIep.valid) {
            this.loaderService.show();

            const {
                date_actual,
                date_reeval,
                resource
            } = this.formIep.getRawValue();

            const iepResource: IepJsonapiResource = resource;

            iepResource.dateActual = date_actual;
            iepResource.dateReeval = date_reeval;

            (new Observable((observer: Observer<IepJsonapiResource>) => {
                iepResource.save({
                    preserveRelationships: true,
                    beforepath: iepResource.is_new ? `${this.entity.path}/relationships` : ''
                }, (response: IDataObject) => {
                    const r: IepJsonapiResource = this.userService.participantService.participantIepJsonapiService.new();
                    Converter.build(response, r);

                    observer.next(r);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
            })).subscribe((iep: IepJsonapiResource) => {
                this.loaderService.hide();

                if (this.entity.ieps.findIndex((r: IepJsonapiResource) => r.id === iep.id) === -1) {
                    this.entity.addRelationship(iep, 'ieps');
                }

                this._ieps = this.entity.ieps

                SwalService.toastSuccess({title: `IEP Date has been saved!`});

                this.cancel();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });

        } else {
            this.formIep.markAllAsTouched();
            this.formIep.updateValueAndValidity();
            this.detectChanges();
        }
    }

    removeIep(entity: IepJsonapiResource): void {
        SwalService.warning({
            title: 'Are you sure?',
            text: `Do you really want to remove IEP Date from ${this.entity.fullname}?`,
            confirmButtonText: `Remove`,
            cancelButtonText: 'Cancel'
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                (new Observable((observer: Observer<boolean>) => {
                    entity.customCall({
                        method: 'DELETE'
                    }, (response: IAcknowledgeResponse) => {
                        observer.next(response.acknowledge);
                        observer.complete();
                    }, (error: DataError) => observer.error(error));
                })).subscribe(() => {
                    this.loaderService.hide();

                    if (this.entity.ieps.findIndex((r: IepJsonapiResource) => r.id === entity.id) !== -1) {
                        this.entity.removeRelationship('ieps', entity.id);
                    }

                    this._ieps = this.entity.ieps

                    SwalService.toastSuccess({title: `IEP has been removed!`});

                    this.detectChanges();
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.fallback(error);
                });
            }
        });
    }

    appendTracker(): void {
        if (this.formGoal) {
            const resource: GoalJsonapiResource = (this.formGoal.get('resource').value as GoalJsonapiResource);
            const entity: TrackerJsonapiResource = this.participantService.participantGoalTrackerJsonapiService.default[0];

            resource.addRelationship(entity, 'trackers');

            (this.formGoal.get('trackers') as FormArray).insert((this.formGoal.get('trackers') as FormArray).controls.length + 1, this.fb.group({
                icon: [entity.icon, [Validators.required]],
                tracker_type: [entity.trackerType, [Validators.required]],
                name: [entity.name, [Validators.maxLength(255), Validators.required, WhitespaceValidator]]
            }));

            this.detectChanges();
        }
    }

    removeTracker(index: number): void {
        if (this.formGoal && this.trackers.length > 1) {
            const resource: GoalJsonapiResource = (this.formGoal.get('resource').value as GoalJsonapiResource);
            const entity: TrackerJsonapiResource = resource.trackers[index];

            SwalService.warning({
                title: 'Are you sure?',
                text: `Do you really want to remove ${entity.name} tracker?`,
                confirmButtonText: `Remove`,
                cancelButtonText: 'Cancel'
            }).then((answer: { value: boolean }) => {
                if (answer.value) {
                    resource.removeRelationship('trackers', entity.id);

                    (this.formGoal.get('trackers') as FormArray).removeAt(index);

                    this.detectChanges();
                }
            });
        }
    }

    private fetchTeams(): void {
        this.loaderService.show();

        this.detectChanges();

        this.userService
            .teamService
            .list()
            .subscribe((data: Array<TeamJsonapiResource>) => {
                this._teams = data;
                this.form = this.fb.group({
                    first_name: [this.entity.firstName, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    last_name: [this.entity.lastName, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    email: [this.entity.email, [Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                    dob: [moment(this.entity.dob).format('YYYY-MM-DD'), [Validators.required]],
                    phone_code: [1, [Validators.maxLength(4)]],
                    phone_number: [this.entity.phoneNumber, [Validators.maxLength(20)]],
                    parent_phone_number: [this.entity.parentPhoneNumber, [Validators.maxLength(20)]],
                    parent_email: [this.entity.parentEmail, [Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                    gender: [this.entity.gender, [Validators.required]],
                    team_id: [this.entity.team ? this.entity.team.id : null, []],
                    school_id: [this.entity.school ? this.entity.school.id : null, []],
                    diagnosis: [this.entity.therapy.diagnosis, [Validators.required]],
                    frequency: [this.entity.therapy.frequency, [Validators.required]],
                    sessions_amount_planned: [this.entity.therapy.sessionsAmountPlanned, []],
                    treatment_amount_planned: [this.entity.therapy.treatmentAmountPlanned, []],
                    notes: [this.entity.therapy.notes, []],
                    private_notes: [this.entity.therapy.privateNotes, []]
                });

                this.form
                    .valueChanges
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((data: any) => {
                        if (!data['team_id']) {
                            if (!!data['school_id']) {
                                this.form.get('school_id').patchValue(null);
                            }

                            this._schools = [];
                        } else {
                            this._schools = this._teams.find((r: TeamJsonapiResource) => r.id === data['team_id']).schools;
                        }

                        this.detectChanges();
                    });

                this.loaderService.hide();

                this.detectChanges();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }
}
