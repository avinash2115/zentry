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
import { RegistrationComponent } from './registration.component';

@Component({
    selector: 'app-authentication-registration-custom',
    templateUrl: './registration.custom.component.html',
    styleUrls: ['./registration.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class RegistrationCustomComponent extends RegistrationComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr, router, fb, layoutService, authenticationService);
    }
}
