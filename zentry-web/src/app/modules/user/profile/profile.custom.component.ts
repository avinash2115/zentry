import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
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
import { ProfileComponent } from './profile.component';
import { SwalService } from '../../../shared/services/swal.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { CrmJsonapiResource } from '../../../resources/user/crm/crm.jsonapi.service';
import { DriverJsonapiResource as CrmDriverJsonapiResource, EDriver as ECrmDriver } from '../../../resources/user/crm/driver/driver.jsonapi.service';
import { Observable } from 'rxjs/internal/Observable';

enum ESteps {
    general,
    security,
    integrations
}

@Component({
    selector: 'app-user-profile-custom',
    templateUrl: './profile.custom.component.html',
    styleUrls: ['./profile.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        UserService
    ]
})
export class ProfileCustomComponent extends ProfileComponent implements OnInit {
    public formIntegration: FormGroup;

    public steps: typeof ESteps = ESteps;
    private _stepActive: ESteps = ESteps.general;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected domSanitizer: DomSanitizer,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected userService: UserService
    ) {
        super(cdr, fb, layoutService, domSanitizer, loaderService, authenticationService, userService);
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

    stepIsActive(value: ESteps): boolean {
        return this._stepActive === value;
    }

    stepActivate(value: ESteps): void {
        this._stepActive = value;

        switch (this._stepActive) {
            case ESteps.general:
                this.formPassword = null;
                this.formIntegration = null;
                break;
            case ESteps.security:
                this.form = null;
                this.formIntegration = null;

                this.formPassword = this.fb.group({
                    password: [null, [Validators.required, Validators.minLength(8), Validators.maxLength(255), WhitespaceValidator]],
                    passwordRepeat: [null, [Validators.required, Validators.minLength(8), Validators.maxLength(255), WhitespaceValidator]]
                }, {validators: [PasswordMathValidator('password', 'passwordRepeat')]});
                break;
            case ESteps.integrations:
                this.loaderService.show();

                this.form = null;
                this.formPassword = null;

                combineLatest([
                    this.userService.crms,
                    this.userService.crmsDrivers
                ]).subscribe(([storages, storagesOptions]: [Array<CrmJsonapiResource>, Array<CrmDriverJsonapiResource>]) => {
                    const therapylogResource: CrmJsonapiResource = storages.find((r: CrmJsonapiResource) => r.driver === ECrmDriver.therapylog) || this.userService.crmJsonapiService.new();
                    const therapylogDriver: CrmDriverJsonapiResource = storagesOptions.find((r: CrmDriverJsonapiResource) => r.driverType === ECrmDriver.therapylog);

                    therapylogResource.driver = therapylogDriver.driverType;

                    this.formIntegration = this.fb.group({
                        driver: [therapylogDriver],
                        resource: [therapylogResource],
                        email: [null, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
                        password: [null, [Validators.required, Validators.minLength(8), Validators.maxLength(255), WhitespaceValidator]]
                    });

                    this.detectChanges();

                    this.loaderService.hide();
                });
                break;
        }

        this.detectChanges();
    }

    build(): void {
        this.form = this.fb.group({
            email: [this.entity.email, [Validators.required, Validators.pattern(EMAIL_VALIDATOR_PATTERN)]],
            first_name: [this.entity.profile.firstName, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
            last_name: [this.entity.profile.lastName, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
            phone_code: [1, [Validators.maxLength(4)]],
            phone_number: [this.entity.profile.phoneNumber, [Validators.maxLength(20)]]
        });

        this.detectChanges();
    }

    cancel(): void {
        this.form = null;

        if (!this.stepIsActive(this.steps.security)) {
            this.formPassword = null;
        } else {
            this.formPassword.reset();
        }

        if (!this.stepIsActive(this.steps.integrations)) {
            this.formIntegration = null;
        } else {
            this.formIntegration.reset();
        }

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

                SwalService.toastSuccess({title: `Profile has been saved!`});

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

                SwalService.toastSuccess({title: `Password has been updated!`});

                this.detectChanges();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    saveIntegration(): void {
        if (!this.formIntegration.valid) {
            this.formIntegration.markAllAsTouched();
            return;
        }

        this.loaderService.show();

        const {driver, resource} = this.formIntegration.getRawValue();

        const config: { [key: string]: string } = {};

        driver.config.forEach((key: string) => {
            config[key] = this.formIntegration.getRawValue()[key];
        });

        let observable: Observable<boolean>;

        if (resource.is_new) {
            observable = this.userService.createCRM(driver.driverType, config)
        } else {
            resource.config = config;
            observable = this.userService.changeCRM(resource);
        }

        observable
            .subscribe(() => {
                this.loaderService.hide();

                SwalService.toastSuccess({title: `Integration has been configured!`});

                this.stepActivate(ESteps.integrations);
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error, 'Unable to verify login at TeleTeachers');
            });
    }
}
