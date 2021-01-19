import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthenticationService } from '../../authentication.service';
import { DataError } from '../../../../shared/classes/data-error';
import { Router } from '@angular/router';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../../shared/services/layout.service';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../shared/consts/form/patterns';
import { StandardComponent } from './standard.component';

@Component({
    selector: 'app-authentication-login-standard-custom',
    templateUrl: './standard.custom.component.html',
    styleUrls: ['./standard.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class StandardCustomComponent extends StandardComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr, router, fb, layoutService, authenticationService);
    }

    get subtitle(): boolean {
        return !window.config.native;
    }
}
