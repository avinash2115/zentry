import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import {
    PasswordResetJsonapiResource,
    PasswordResetJsonapiService
} from '../../../resources/helpers/password/reset-jsonapi.service';
import { AuthenticationService } from '../authentication.service';
import { DataError } from '../../../shared/classes/data-error';
import { IAcknowledgeResponse } from '../../../shared/interfaces/acknowledge-response.interface';
import { Router } from '@angular/router';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { EMAIL_VALIDATOR_PATTERN } from '../../../shared/consts/form/patterns';

@Component({
    selector: 'app-authentication-forgot-password',
    templateUrl: './forgot-password.component.html',
    styleUrls: ['./forgot-password.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ForgotPasswordComponent extends BaseDetachedComponent implements OnInit {
    public form: FormGroup;
    public error: string;
    public isSuccess: boolean = false;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService,
        protected passwordResetJsonapiService: PasswordResetJsonapiService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Forgot password');

        this.form = this.fb.group({
            email: [null, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]]
        });
    }

    submit(): void {
        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        const {email} = this.form.getRawValue();
        const resource: PasswordResetJsonapiResource = this.passwordResetJsonapiService.new();

        resource.email = email;

        this.error = null;

        this.form.disable();

        this.sendingTrigger();

        this.authenticationService
            .requestResetPassword(resource)
            .subscribe(({acknowledge}: IAcknowledgeResponse) => {
                if (acknowledge) {
                    this.isSuccess = true;
                    this.sendingCompleted();
                }
            }, (error: DataError) => {
                this.error = error.message;
                this.form.enable();
                this.sendingCompleted()
            });
    }
}
