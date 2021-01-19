import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { AuthenticationService } from '../authentication.service';
import { DataError } from '../../../shared/classes/data-error';
import { Router } from '@angular/router';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { EMAIL_VALIDATOR_PATTERN } from '../../../shared/consts/form/patterns';
import { WhitespaceValidator } from '../../../shared/validators/whitespace.validator';

@Component({
    selector: 'app-authentication-registration',
    templateUrl: './registration.component.html',
    styleUrls: ['./registration.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class RegistrationComponent extends BaseDetachedComponent implements OnInit {
    public form: FormGroup;
    public error: string;
    public isPasswordVisible: boolean = false;
    public stepBasicPassed: boolean = false;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr);
    }

    get isStepProfile(): boolean {
        const {email, password} = this.form.getRawValue();

        return this.stepBasicPassed && !!email && !!password;
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Sign Up');

        this.form = this.fb.group({
            email: [null, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
            password: [null, [Validators.required, Validators.minLength(8), Validators.maxLength(255), WhitespaceValidator]],
            first_name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
            last_name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
        });
    }

    next(): void {
        this.form.controls['email'].markAsTouched({onlySelf: true});
        this.form.controls['password'].markAsTouched({onlySelf: true});

        this.stepBasicPassed = this.form.controls['email'].valid && this.form.controls['password'].valid;

        this.detectChanges();
    }

    submit(): void {
        if (this.isPasswordVisible) {
            this.togglePasswordVisibility();
        }

        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        const {email, password, first_name, last_name} = this.form.getRawValue();

        const data: IDataObject = {
            data: {
                type: 'users',
                attributes: {
                    email,
                    password,
                    password_repeat: password,
                    remember: true
                },
                relationships: {
                    profile: {
                        data: {
                            attributes: {
                                first_name,
                                last_name
                            }
                        }
                    }
                }
            }
        };

        this.form.disable();

        this.error = null;

        this.sendingTrigger();

        this.authenticationService
            .registration(data)
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
}
