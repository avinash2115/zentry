import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { DomSanitizer } from '@angular/platform-browser';
import { LoaderService } from '../../../shared/services/loader.service';
import { UserService } from '../user.service';
import { filter, takeUntil } from 'rxjs/operators';
import { AuthenticationService } from '../../authentication/authentication.service';
import { UserJsonapiResource } from '../../../resources/user/user.jsonapi.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { DataError } from '../../../shared/classes/data-error';
import { PasswordMathValidator } from '../../../shared/validators/password-match.validator';
import { EMAIL_VALIDATOR_PATTERN } from '../../../shared/consts/form/patterns';
import { WhitespaceValidator } from '../../../shared/validators/whitespace.validator';

@Component({
    selector: 'app-user-profile',
    templateUrl: './profile.component.html',
    styleUrls: ['./profile.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        UserService
    ]
})
export class ProfileComponent extends BaseDetachedComponent implements OnInit {
    public entity: UserJsonapiResource;
    public form: FormGroup;
    public formPassword: FormGroup;

    public isPasswordVisible: boolean = false;
    public isPasswordStepVisible: boolean = false;
    public isPasswordRepeatVisible: boolean = false;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected domSanitizer: DomSanitizer,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected userService: UserService,
    ) {
        super(cdr);
    }

    get showMismatchError(): boolean {
        return this.formPassword && this.formPassword.hasError('mismatch');
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('My Profile');

        this.userService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: UserJsonapiResource | null) => entity instanceof UserJsonapiResource)
            )
            .subscribe((entity: UserJsonapiResource) => {
                this.entity = entity;

                this.form = this.fb.group({
                    email: [this.entity.email, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                    first_name: [this.entity.profile.firstName, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    last_name: [this.entity.profile.lastName, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    phone_code: [this.entity.profile.phoneCode, [Validators.maxLength(4)]],
                    phone_number: [this.entity.profile.phoneNumber, [Validators.maxLength(20)]],
                });

                this.formPassword = this.fb.group({
                    password: [null, [Validators.required, Validators.minLength(8), Validators.maxLength(255), WhitespaceValidator]],
                    passwordRepeat: [null, [Validators.required, Validators.minLength(8), Validators.maxLength(255), WhitespaceValidator]]
                }, {validators: [PasswordMathValidator('password', 'passwordRepeat')]});

                this.form.disable();

                this.loadingCompleted();
            });

        this.authenticationService
            .entityLoaded
            .subscribe((user: UserJsonapiResource) => {
                this.userService
                    .get(user.id)
                    .subscribe(() => {
                    }, (error: DataError) => {
                        this.fallback(error);
                    });
            });
    }

    togglePasswordVisibility(): void {
        this.isPasswordVisible = !this.isPasswordVisible
        this.detectChanges();
    }

    togglePasswordRepeatVisibility(): void {
        this.isPasswordRepeatVisible = !this.isPasswordRepeatVisible
        this.detectChanges();
    }

    togglePasswordStep(event: any): void {
        this.isPasswordStepVisible = event.currentTarget.checked;

        if (this.isPasswordStepVisible) {
            this.cancel();
        } else {
            this.formPassword.reset();
        }

        this.detectChanges();
    }

    edit(): void {
        this.form.enable();
        this.detectChanges();
    }

    cancel(): void {
        this.form.disable();

        this.userService.refresh();

        this.detectChanges();
    }

    save(): void {
        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        this.loaderService.show();

        const {email, first_name, last_name, phone_code, phone_number} = this.form.getRawValue();

        if (this.entity.email !== email) {
            this.entity.email = email;
            this.entity.forceDirty();
        }

        if (this.entity.profile.firstName !== first_name) {
            this.entity.profile.firstName = first_name;
            this.entity.forceDirty();
        }

        if (this.entity.profile.lastName !== last_name) {
            this.entity.profile.lastName = last_name;
            this.entity.forceDirty();
        }

        if (this.entity.profile.phoneCode !== phone_code) {
            this.entity.profile.phoneCode = phone_code;
            this.entity.forceDirty();
        }

        if (this.entity.profile.phoneNumber !== phone_number) {
            this.entity.profile.phoneNumber = phone_number;
            this.entity.forceDirty();
        }

        this.userService
            .save()
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                this.loaderService.hide();
                this.cancel();
                this.authenticationService.reload();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    savePassword(): void {
        if (!this.formPassword.valid) {
            this.formPassword.markAllAsTouched();
            return;
        }

        this.loaderService.show();

        const {password, passwordRepeat} = this.formPassword.getRawValue();

        this.entity.password = password;
        this.entity.passwordRepeat = passwordRepeat;

        this.userService
            .save()
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                this.loaderService.hide();
                this.togglePasswordStep({currentTarget: {checked: false}});
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }
}
