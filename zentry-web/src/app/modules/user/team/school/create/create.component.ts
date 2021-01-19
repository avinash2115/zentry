import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutService } from '../../../../../shared/services/layout.service';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { UserService } from '../../../user.service';
import { TeamJsonapiResource } from '../../../../../resources/user/team/team.jsonapi.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { WhitespaceValidator } from '../../../../../shared/validators/whitespace.validator';
import { switchMap, takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../../shared/classes/data-error';
import { SwalService } from '../../../../../shared/services/swal.service';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { SchoolJsonapiResource } from '../../../../../resources/user/team/school/school.jsonapi.service';
import { IState, STATES } from '../../../../../shared/consts/states';
import { TeamService } from '../../team.service';
import { SchoolService } from '../../school.service';
import { of } from 'rxjs/internal/observable/of';

enum ESteps {
    general,
    students
}

@Component({
    selector: 'app-user-team-school-create',
    templateUrl: './create.component.html',
    styleUrls: ['./create.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, TeamService, SchoolService]
})
export class CreateComponent extends BaseDetachedComponent implements OnInit {
    public form: FormGroup;

    public steps: typeof ESteps = ESteps;
    public states: Array<IState> = STATES;
    private _stepActive: ESteps = ESteps.general;

    private _team: TeamJsonapiResource | null;
    private _teams: Array<TeamJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected userService: UserService
    ) {
        super(cdr);
    }

    get team(): TeamJsonapiResource {
        return this._team;
    }

    get teams(): Array<TeamJsonapiResource> {
        return this._teams;
    }

    ngOnInit(): void {
        this.loadingTrigger();
        this.layoutService.changeTitle('Districts/Schools | Create');

        this.fetchTeams();
    }

    stepIsActive(value: ESteps): boolean {
        return this._stepActive === value;
    }

    stepIsValid(value: ESteps): boolean {
        switch (value) {
            case ESteps.general:
                return ['name', 'team_id', 'address', 'city', 'state', 'zip'].filter((key: string) => {
                    return !this.form.get(key).dirty || !this.form.get(key).valid;
                }).length === 0;
            case ESteps.students:
                return this.stepIsValid(ESteps.general);
            default:
                return false;
        }
    }

    stepActivate(value: ESteps): void {
        switch (value) {
            case ESteps.students:
                if (this.stepIsValid(ESteps.general)) {
                    this._stepActive = value;
                } else {
                    ['name', 'team_id', 'address', 'city', 'state', 'zip'].forEach((key: string) => {
                        this.form.get(key).markAsTouched();
                        this.form.get(key).updateValueAndValidity();
                    });
                }
                break;
            default:
                this._stepActive = value;
                break;
        }

        this.detectChanges();
    }

    submit(): void {
        if (this.form.valid) {
            this.loaderService.show();

            const {
                name,
                team_id,
                address,
                city,
                state,
                zip
            } = this.form.getRawValue();

            const team: TeamJsonapiResource = this.teams.find((t: TeamJsonapiResource) => t.id === team_id);
// 
            this.userService
                .teamService
                .schoolService
                .make()
                .pipe(
                    switchMap((r: SchoolJsonapiResource) => {
                        r.name = name;
                        r.streetAddress = address;
                        r.city = city;
                        r.state = state;
                        r.zip = zip;

                        r.path = `${team.path}/relationships/${this.userService.teamService.schoolService.schoolJsonapiService.path}`;

                        return this.userService.teamService.schoolService.save();
                    })
                )
                .subscribe((r: SchoolJsonapiResource) => {
                    this.loaderService.hide();

                    SwalService.toastSuccess({title: `${r.name} has been created!`});

                    this.router.navigate(['/user/districts/']);
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.fallback(error);
                });
        } else {
            this.form.markAllAsTouched();
            this.detectChanges();
        }
    }

    private fetchTeams(): void {
        this.userService
            .teamService
            .list()
            .pipe(
                switchMap((data: Array<TeamJsonapiResource>) => {
                    this._teams = data;

                    if (!this.activatedRoute.snapshot.params.hasOwnProperty('districtId')) {
                        return of(null);
                    }

                    return this.userService.teamService.get(this.activatedRoute.snapshot.params.districtId);
                })
            )
            .subscribe((entity: TeamJsonapiResource | null) => {
                this._team = entity;

                this.form = this.fb.group({
                    name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    team_id: [this.team ? this.team.id : null, [Validators.required]],
                    address: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    city: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    state: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    zip: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]]
                });

                this.form
                    .valueChanges
                    .pipe(takeUntil(this._destroy$))
                    .subscribe(() => this.detectChanges());

                this.loadingCompleted();
            });
    }
}
