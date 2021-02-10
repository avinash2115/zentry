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
import { ServiceService } from '../service.service';
import { ServiceJsonapiResource } from '../../../resources/service/service.jsonapi.service';

enum ESteps {
    general,
}

@Component({
    selector: 'app-service-create',
    templateUrl: './create.component.html',
    styleUrls: ['./create.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [ServiceService]
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
        protected serviceService: ServiceService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('Service | Create');

        this.build();
    }

    stepIsActive(value: ESteps): boolean {
        return this._stepActive === value;
    }

    stepIsValid(value: ESteps): boolean {
        switch (value) {
            case ESteps.general:
                return ['name'].filter((key: string) => {
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
                name
            } = this.form.getRawValue();

            this.serviceService
                .make()
                .pipe(
                    switchMap((r: ServiceJsonapiResource) => {
                        r.name = name;

                        return this.serviceService.save();
                    })
                )
                .subscribe((r: ServiceJsonapiResource) => {
                    this.loaderService.hide();

                    SwalService.toastSuccess({title: `${r.name} has been created!`});

                    this.router.navigate(['/service']);
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
            name: [null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]]
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
