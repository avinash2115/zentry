import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordMathValidator } from '../../../shared/validators/password-match.validator';
import { ActivatedRoute, Router } from '@angular/router';
import { PasswordResetJsonapiResource } from '../../../resources/helpers/password/reset-jsonapi.service';
import { AuthenticationService } from '../authentication.service';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';

@Component({
    selector: 'app-authentication-reset-password',
    templateUrl: './reset-password.component.html',
    styleUrls: ['./reset-password.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ResetPasswordComponent extends BaseDetachedComponent implements OnInit, OnDestroy {
    public form: FormGroup;
    public error: string;
    public resource: PasswordResetJsonapiResource;
    public isPasswordVisible: boolean = false;
    public isPasswordRepeatVisible: boolean = false;

    private token: string;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected route: ActivatedRoute,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr);
    }

    get showMismatchError(): boolean {
        return this.form && this.form.hasError('mismatch');
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Reset password');

        this.loadingTrigger();

        this.token = this.route.snapshot.params['token'];

        if (!this.token) {
            this.router.navigate(['/']);
        } else {
            this.getResource();
        }
    }

    ngOnDestroy(): void {
        this._destroy$.next(true);
        this._destroy$.complete();
    }

    submit(): void {
        if (this.isPasswordVisible) {
            this.togglePasswordVisibility();
        }

        if (this.isPasswordRepeatVisible) {
            this.togglePasswordRepeatVisibility();
        }

        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        const {password, passwordRepeat} = this.form.getRawValue();
        this.resource.password = password;
        this.resource.passwordRepeat = passwordRepeat;

        this.error = null;

        this.form.disable();

        this.sendingTrigger();

        this.authenticationService
            .resetPassword(this.resource)
            .subscribe(({redirectTo}: { redirectTo: string }) => {
                this.router.navigate([redirectTo]);
            }, (error: DataError) => {
                this.error = error.message;
                this.form.enable();
                this.sendingCompleted();
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

    private getResource() {
        this.authenticationService
            .getResetPasswordById(this.token)
            .subscribe((resource: PasswordResetJsonapiResource) => {
                this.resource = resource;
                this.buildForm();
            }, (error: DataError) => {
                if (error.status === 404) {
                    this.failure('Link is expired or not found! Please repeat password reset request.');
                } else {
                    this.failure(error.message);
                }
            });
    }

    private buildForm() {
        this.form = this.fb.group({
            password: [null, [Validators.required, Validators.minLength(8)]],
            passwordRepeat: [null, [Validators.required, Validators.minLength(8)]]
        }, {validators: [PasswordMathValidator('password', 'passwordRepeat')]});

        this.loadingCompleted();
    }

    private failure(error: string): void {
        SwalService
            .error({title: 'Something went wrong', text: error})
            .then(() => {
                this.router.navigate(['/auth/login']);
            });
    }
}
