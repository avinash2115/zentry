import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { Router } from '@angular/router';
import { LayoutService } from '../../../shared/services/layout.service';
import { LoaderService } from '../../../shared/services/loader.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { WhitespaceValidator } from '../../../shared/validators/whitespace.validator';
import { switchMap, takeUntil } from 'rxjs/operators';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { ProviderService } from '../providers.service';
import { ProviderJsonapiResource } from '../../../resources/provider/provider.jsonapi.service';

enum ESteps {
    general,
}

@Component({
    selector: 'app-providers-create',
    templateUrl: './create.component.html',
    styleUrls: ['./create.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [ProviderService]
})
export class CreateComponent extends BaseDetachedComponent implements OnInit {
    public form: FormGroup;

    public steps: typeof ESteps = ESteps;
    private _stepActive: ESteps = ESteps.general;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected router: Router,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected providerService: ProviderService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('providers | Create');

        this.build();
    }

    stepIsActive(value: ESteps): boolean {
        return this._stepActive === value;
    }

    stepIsValid(value: ESteps): boolean {
        switch (value) {
            case ESteps.general:
                return ['name','code'].filter((key: string) => {
                    return !this.form.get(key).dirty || !this.form.get(key).valid;
                }).length === 0;
            default:
                return false;
        }
    }

    stepActivate(value: ESteps): void {
        switch (value) {
            default:
                this._stepActive = value;
                break;
        }

        this.detectChanges();
    }

    submit(): void {
        if (this.form.valid) {
            this.loaderService.show();

            const {
                name,
                code,
            } = this.form.getRawValue();

            this.providerService
                .make()
                .pipe(
                    switchMap((r: ProviderJsonapiResource) => {
                        r.name = name;
                        r.code = code;

                        return this.providerService.save();
                    })
                )
                .subscribe((r: ProviderJsonapiResource) => {
                    this.loaderService.hide();

                    SwalService.toastSuccess({title: `Services has been created!`});

                    this.router.navigate(['/providers']);
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

    private build(): void {
        this.form = this.fb.group({
            name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
            code: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]]



        });

        this.form
            .valueChanges
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: any) => {
                this.detectChanges();
            });

        this.loadingCompleted();
    }
}
