import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { Router } from '@angular/router';
import { LayoutService } from '../../../../shared/services/layout.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { UserService } from '../../user.service';
import { ParticipantService } from '../participant.service';
import { EGenders, ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { TeamJsonapiResource } from '../../../../resources/user/team/team.jsonapi.service';
import { SchoolJsonapiResource } from '../../../../resources/user/team/school/school.jsonapi.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../shared/consts/form/patterns';
import { WhitespaceValidator } from '../../../../shared/validators/whitespace.validator';
import { switchMap, takeUntil } from 'rxjs/operators';
import { EFrequencies, TherapyJsonapiResource } from '../../../../resources/user/participant/therapy/therapy.jsonapi.service';
import { DataError } from '../../../../shared/classes/data-error';
import { SwalService } from '../../../../shared/services/swal.service';
import * as moment from 'moment';

enum ESteps {
    general,
    therapy
}

@Component({
    selector: 'app-user-participant-create',
    templateUrl: './create.component.html',
    styleUrls: ['./create.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, ParticipantService]
})
export class CreateComponent extends BaseDetachedComponent implements OnInit {
    public form: FormGroup;

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

    public steps: typeof ESteps = ESteps;
    private _stepActive: ESteps = ESteps.general;
    private _teams: Array<TeamJsonapiResource> = [];
    private _schools: Array<SchoolJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected router: Router,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected userService: UserService,
        protected participantService: ParticipantService
    ) {
        super(cdr);
    }

    get teams(): Array<TeamJsonapiResource> {
        return this._teams;
    }

    get schools(): Array<SchoolJsonapiResource> {
        return this._schools;
    }

    ngOnInit(): void {
        this.loadingTrigger();
        this.layoutService.changeTitle('Students | Create');

        this.fetchTeams();
    }

    stepIsActive(value: ESteps): boolean {
        return this._stepActive === value;
    }

    stepIsValid(value: ESteps): boolean {
        switch (value) {
            case ESteps.general:
                return ['first_name', 'last_name', 'dob', 'gender'].filter((key: string) => {
                    return !this.form.get(key).dirty || !this.form.get(key).valid;
                }).length === 0;
            case ESteps.therapy:
                return ['diagnosis', 'frequency'].filter((key: string) => {
                    return !this.form.get(key).dirty || !this.form.get(key).valid;
                }).length === 0;
            default:
                return false;
        }
    }

    stepActivate(value: ESteps): void {
        switch (value) {
            case ESteps.therapy:
                if (this.stepIsValid(ESteps.general)) {
                    this._stepActive = value;
                } else {
                    ['first_name', 'last_name', 'dob', 'gender'].forEach((key: string) => {
                        this.form.get(key).markAsTouched();
                        this.form.get(key).updateValueAndValidity();
                    });
                }
                // break;
            default:
                this._stepActive = value;
                // break;
        }

        this.detectChanges();
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
                .make()
                .pipe(
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

                        const therapy: TherapyJsonapiResource = this.userService
                            .participantService
                            .participantTherapyJsonapiService.new();

                        therapy.diagnosis = diagnosis;
                        therapy.frequency = frequency;
                        therapy.sessionsAmountPlanned = sessions_amount_planned;
                        therapy.treatmentAmountPlanned = treatment_amount_planned;
                        therapy.notes = notes;
                        therapy.privateNotes = private_notes;

                        r.addRelationship(therapy, 'therapy');

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
                            }
                        }

                        return this.userService.participantService.save();
                    })
                )
                .subscribe((r: ParticipantJsonapiResource) => {
                    this.loaderService.hide();
                    SwalService.toastSuccess({title: `${r.fullname} has been created!`});

                    this.router.navigate(['/user/students', r.id]);
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

    private fetchTeams(): void {
        this.userService
            .teamService
            .list()
            .subscribe((data: Array<TeamJsonapiResource>) => {
                this._teams = data;

                this.form = this.fb.group({
                    first_name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    last_name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    email: [null, [Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                    dob: [moment('2003-01-01').format('YYYY-MM-DD'), [Validators.required]],
                    phone_code: [1, [Validators.maxLength(4)]],
                    phone_number: [null, [Validators.maxLength(20)]],
                    parent_phone_number: [null, [Validators.maxLength(20)]],
                    parent_email: [null, [Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                    gender: [null, [Validators.required]],
                    team_id: [null, []],
                    school_id: [null, []],
                    diagnosis: [null, [Validators.required]],
                    frequency: [EFrequencies.weekly, [Validators.required]],
                    sessions_amount_planned: [0, []],
                    treatment_amount_planned: [0, []],
                    notes: [null, []],
                    private_notes: [null, []]
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

                this.loadingCompleted();
            });
    }
}
