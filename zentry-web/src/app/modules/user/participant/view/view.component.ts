import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { UserService } from '../../user.service';
import { ParticipantService } from '../participant.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { filter, switchMap, takeUntil } from 'rxjs/operators';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../shared/consts/form/patterns';
import { DataError } from '../../../../shared/classes/data-error';
import { LayoutService } from '../../../../shared/services/layout.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { ActivatedRoute, Router } from '@angular/router';
import { UserJsonapiResource } from '../../../../resources/user/user.jsonapi.service';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { SwalService } from '../../../../shared/services/swal.service';
import { WhitespaceValidator } from '../../../../shared/validators/whitespace.validator';

@Component({
    selector: 'app-user-participant-view',
    templateUrl: './view.component.html',
    styleUrls: ['./view.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [UserService, ParticipantService]
})
export class ViewComponent extends BaseDetachedComponent implements OnInit {
    public entity: ParticipantJsonapiResource;
    public form: FormGroup;

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
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle(this.terms('participants'));

        this.userService
            .participantService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: ParticipantJsonapiResource | null) => entity instanceof ParticipantJsonapiResource)
            )
            .subscribe((entity: ParticipantJsonapiResource) => {
                this.entity = entity;

                this.layoutService.changeTitle(`${this.terms('participants')} | ${this.entity.is_new ? 'Create' : this.entity.fullname || this.entity.email}`);

                this.form = this.fb.group({
                    email: [this.entity.email, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                    first_name: [this.entity.firstName, [Validators.required, WhitespaceValidator]],
                    last_name: [this.entity.lastName, [Validators.required, WhitespaceValidator]],
                });

                if (!this.entity.is_new) {
                    this.form.disable();
                }

                this.loadingCompleted();
            });

        this.authenticationService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                switchMap((user: UserJsonapiResource) => this.userService.get(user.id)),
                switchMap(() => {
                    switch (this.activatedRoute.snapshot.params.participantId) {
                        case 'create':
                            return this.userService.participantService.make();
                        default:
                            return this.userService.participantService.get(this.activatedRoute.snapshot.params.participantId);
                    }
                })
            )
            .subscribe(() => {
            }, (error: DataError) => {
                this.fallback(error);
            });
    }

    edit(): void {
        this.form.enable();
        this.detectChanges();
    }

    cancel(): void {
        this.form.disable();

        this.participantService.refresh();

        this.detectChanges();
    }

    save(): void {
        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        this.loaderService.show();

        const {email, first_name, last_name} = this.form.getRawValue();

        if (this.entity.email !== email) {
            this.entity.email = email;
            this.entity.forceDirty();
        }

        if (this.entity.firstName !== first_name) {
            this.entity.firstName = first_name;
            this.entity.forceDirty();
        }

        if (this.entity.lastName !== last_name) {
            this.entity.lastName = last_name;
            this.entity.forceDirty();
        }

        const isNew: boolean = this.entity.is_new;

        this.userService
            .participantService
            .save()
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                this.loaderService.hide();
                this.cancel();

                if (isNew) {
                    this.router.navigate(['/user/participants']).then(() => {
                        SwalService.toastSuccess({title: `${this.entity.fullname || this.entity.email} has been created!`});
                    });
                }
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    remove(): void {
        SwalService
            .remove({
                title: `Are you sure?`,
                text: `${this.entity.fullname || this.entity.email} is going to be removed!`,
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    this.participantService
                        .remove(this.entity)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe((result: boolean) => {
                            this.loaderService.hide();

                            if (result) {
                                this.router.navigate(['/user/participants']).then(() => {
                                    SwalService.toastSuccess({title: `Removed`});
                                });
                            } else {
                                SwalService
                                    .error({
                                        title: `${this.entity.fullname || this.entity.email} was not removed!`,
                                        text: `Please try to remove it again.`
                                    });
                            }
                        }, (error: DataError) => {
                            this.loaderService.hide();
                            this.fallback(error);
                        });
                }
            });
    }
}
