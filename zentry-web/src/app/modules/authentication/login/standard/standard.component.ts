import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthenticationService } from '../../authentication.service';
import { DataError } from '../../../../shared/classes/data-error';
import { Router } from '@angular/router';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../../shared/services/layout.service';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../shared/consts/form/patterns';

@Component({
    selector: 'app-authentication-login-standard',
    templateUrl: './standard.component.html',
    styleUrls: ['./standard.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class StandardComponent extends BaseDetachedComponent implements OnInit {
    public form: FormGroup;
    public error: string;
    public isPasswordVisible: boolean = false;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Login');

        this.form = this.fb.group({
            email: [null, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
            password: [null, [Validators.required]],
            remember: [false]
        });
    }

    submit(): void {
        if (this.isPasswordVisible) {
            this.togglePasswordVisibility();
        }

        if (!this.form.valid) {
            this.form.markAllAsTouched();
            return;
        }

        const {email, password, remember}: { email: string, password: string, remember: boolean } = this.form.getRawValue();

        this.form.disable();

        this.error = null;

        this.sendingTrigger()

        this.authenticationService
            .login(email, password, remember)
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
