import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutService } from '../../../shared/services/layout.service';
import { LoaderService } from '../../../shared/services/loader.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { WhitespaceValidator } from '../../../shared/validators/whitespace.validator';
import { filter, switchMap, take, takeUntil } from 'rxjs/operators';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { ProviderService } from '../providers.service';
import { ProviderJsonapiResource } from '../../../resources/provider/provider.jsonapi.service';
import { TeamJsonapiResource } from '../../../resources/user/team/team.jsonapi.service';

enum ESteps {
    general,
}

@Component({
    selector: 'app-provider-view',
    templateUrl: './view.component.html',
    styleUrls: ['./view.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [ProviderService]
})
export class ViewComponent extends BaseDetachedComponent implements OnInit {
    public entity: ProviderJsonapiResource;

    public form: FormGroup;

    public steps: typeof ESteps = ESteps;
    private _stepActive: ESteps = ESteps.general;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected providerService: ProviderService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('Provider | Create');

        this.providerService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: ProviderJsonapiResource | null) => entity instanceof ProviderJsonapiResource)
            )
            .subscribe((entity: ProviderJsonapiResource) => {
                this.entity = entity;

                this.layoutService.changeTitle(`Providers | ${this.entity.name}`);

                if (this.activatedRoute.snapshot.queryParams.edit) {
                    this.router.navigate([], {relativeTo: this.activatedRoute, queryParams: {}});
                }

                this.build();
            });

        this.providerService
            .get(this.activatedRoute.snapshot.params.providerId)
            .subscribe(() => {
            }, (error: DataError) => this.fallback(error));
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
                name,
                code,
               
            } = this.form.getRawValue();

            this.providerService
                .entity
                .pipe(
                    take(1),
                    switchMap((r: ProviderJsonapiResource) => {
                        r.name = name;
                        r.code = code;
                       


                        return this.providerService.save();
                    })
                )
                .subscribe((r: ProviderJsonapiResource) => {
                    this.loaderService.hide();

                    SwalService.toastSuccess({title: `${r.name} has been created!`});

                    this.router.navigate(['/provider']);
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
            name: [this.entity.name, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
            code: [this.entity.code, [Validators.required, Validators.maxLength(255), WhitespaceValidator]]
        });

        this.form
            .valueChanges
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: any) => {
                this.detectChanges();
            });

        if (this.entity.hasSources) {
            this.form.disable();
        }

        this.loadingCompleted();
    }
}
