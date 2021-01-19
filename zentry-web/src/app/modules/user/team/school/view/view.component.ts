import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LayoutService } from '../../../../../shared/services/layout.service';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthenticationService } from '../../../../authentication/authentication.service';
import { filter, switchMap, take, takeUntil } from 'rxjs/operators';
import { WhitespaceValidator } from '../../../../../shared/validators/whitespace.validator';
import { DataError } from '../../../../../shared/classes/data-error';
import { TeamJsonapiResource } from '../../../../../resources/user/team/team.jsonapi.service';
import { SchoolJsonapiResource } from '../../../../../resources/user/team/school/school.jsonapi.service';
import { SwalService } from '../../../../../shared/services/swal.service';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { SchoolService } from '../../school.service';
import { TeamService } from '../../team.service';
import { IState, STATES } from '../../../../../shared/consts/states';

@Component({
    selector: 'app-user-team-school-view',
    templateUrl: './view.component.html',
    styleUrls: ['./view.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, TeamService, SchoolService]
})
export class ViewComponent extends BaseDetachedComponent implements OnInit {
    public entity: SchoolJsonapiResource;

    public form: FormGroup;
    public states: Array<IState> = STATES;

    public studentsFilter: object = {};

    private _team: TeamJsonapiResource;
    private _teams: Array<TeamJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
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

        this.layoutService.changeTitle('Districts/Schools');
        this.userService
            .teamService
            .schoolService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: SchoolJsonapiResource | null) => entity instanceof SchoolJsonapiResource)
            )
            .subscribe((entity: SchoolJsonapiResource) => {
                this.entity = entity;

                this.layoutService.changeTitle(`Districts/Schools | ${this.entity.name}`);

                if (this.activatedRoute.snapshot.queryParams.edit) {
                    this.router.navigate([], {relativeTo: this.activatedRoute, queryParams: {}});
                    this.build();
                }

                this.loadingCompleted();
            });

        this.userService
            .teamService
            .get(this.activatedRoute.snapshot.params.districtId)
            .pipe(
                switchMap((team: TeamJsonapiResource) => {
                    this._team = team;

                    this.studentsFilter = {
                        teams: {collection: [this.team.id]},
                        schools: {collection: [this.activatedRoute.snapshot.params.schoolId]}
                    };

                    return this.userService.teamService.schoolService.get(this.activatedRoute.snapshot.params.schoolId, ['*'], {beforepath: `${this.team.path}/relationships`});
                })
            )
            .subscribe(() => {
            }, (error: DataError) => this.fallback(error));
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
                name,
                team_id,
                address,
                city,
                state,
                zip
            } = this.form.getRawValue();

            this.userService
                .teamService
                .schoolService
                .entity
                .pipe(
                    take(1),
                    switchMap((r: SchoolJsonapiResource) => {
                        r.name = name;
                        r.streetAddress = address;
                        r.city = city;
                        r.state = state;
                        r.zip = zip;

                        if (team_id !== this.team.id) {
                            r.addRelationship(this.teams.find((r: TeamJsonapiResource) => r.id === team_id), 'target_team');
                        }

                        return this.userService.teamService.schoolService.save();
                    })
                )
                .subscribe((r: SchoolJsonapiResource) => {
                    this.loaderService.hide();

                    if (team_id !== this.team.id) {
                        SwalService.toastSuccess({title: `${r.name} has been saved and moved!`});
                        this.router.navigate(['/user/districts/', team_id, 'schools', r.id]);
                    } else {
                        SwalService.toastSuccess({title: `${r.name} has been saved!`});
                    }

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
        this.detectChanges();
    }

    private fetchTeams(): void {
        this.loaderService.show();

        this.userService
            .teamService
            .list()
            .subscribe((data: Array<TeamJsonapiResource>) => {
                this._teams = data;

                this.form = this.fb.group({
                    name: [this.entity.name, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    team_id: [this.team.id, [Validators.required]],
                    address: [this.entity.streetAddress, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    city: [this.entity.city, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    state: [this.entity.state, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    zip: [this.entity.zip, [Validators.required, Validators.maxLength(255), WhitespaceValidator]]
                });

                this.form
                    .valueChanges
                    .pipe(takeUntil(this._destroy$))
                    .subscribe(() => this.detectChanges());

                this.loaderService.hide();

                this.detectChanges();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }
}
