import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { UserService } from '../../user.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LayoutService } from '../../../../shared/services/layout.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { filter, switchMap, take, takeUntil } from 'rxjs/operators';
import { WhitespaceValidator } from '../../../../shared/validators/whitespace.validator';
import { DataError } from '../../../../shared/classes/data-error';
import { TeamJsonapiResource } from '../../../../resources/user/team/team.jsonapi.service';
import { SchoolJsonapiResource } from '../../../../resources/user/team/school/school.jsonapi.service';
import { SwalService } from '../../../../shared/services/swal.service';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { TeamService } from '../team.service';

@Component({
    selector: 'app-user-team-view',
    templateUrl: './view.component.html',
    styleUrls: ['./view.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, TeamService]
})
export class ViewComponent extends BaseDetachedComponent implements OnInit {
    public entity: TeamJsonapiResource;

    public form: FormGroup;

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

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('Districts/Schools');

        this.userService
            .teamService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: TeamJsonapiResource | null) => entity instanceof TeamJsonapiResource)
            )
            .subscribe((entity: TeamJsonapiResource) => {
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
            .subscribe(() => {
            }, (error: DataError) => this.fallback(error));
    }


    build(): void {
        if (!this.entity.hasSources) {
            this.form = this.fb.group({
                name: [this.entity.name, [Validators.required, Validators.maxLength(255), WhitespaceValidator]]
            });

            this.form
                .valueChanges
                .pipe(takeUntil(this._destroy$))
                .subscribe(() => this.detectChanges());
        }
    }

    submit(): void {
        if (this.form.valid) {
            this.loaderService.show();

            const {
                name
            } = this.form.getRawValue();

            this.userService
                .teamService
                .entity
                .pipe(
                    take(1),
                    switchMap((r: TeamJsonapiResource) => {
                        r.name = name;

                        return this.userService.teamService.save();
                    })
                )
                .subscribe((r: TeamJsonapiResource) => {
                    this.loaderService.hide();

                    SwalService.toastSuccess({title: `${r.name} has been saved!`});

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
        this.userService.teamService.refresh();
    }

    makeStudentsFilter(school?: SchoolJsonapiResource): object {
        return {
            teams: {collection: [this.entity.id]},
            schools: {
                collection: school instanceof SchoolJsonapiResource ? [school.id] : this.entity.schools.map((r: SchoolJsonapiResource) => r.id),
                has: school instanceof SchoolJsonapiResource
            }
        };
    }
}
