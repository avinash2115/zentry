import {
    ComponentFactory,
    ComponentFactoryResolver,
    ComponentRef,
    Directive,
    ElementRef,
    Host,
    Inject,
    Input,
    OnDestroy,
    OnInit,
    Optional,
    ViewContainerRef
} from '@angular/core';
import { FORM_ERRORS } from '../../../consts/form/errors';
import { ControlErrorContainerDirective } from './error-container.directive';
import { AbstractControl, NgControl, ValidationErrors } from '@angular/forms';
import { Subject } from 'rxjs/internal/Subject';
import { takeUntil } from 'rxjs/operators';
import { merge } from 'rxjs/internal/observable/merge';
import { FormSubmitDirective } from '../submit.directive';
import { EMPTY } from 'rxjs/internal/observable/empty';
import { Observable } from 'rxjs/internal/Observable';
import { fromEvent } from 'rxjs/internal/observable/fromEvent';
import { NgSelectComponent } from '@ng-select/ng-select';
import { Observer } from 'rxjs/internal/types';
import { ControlErrorComponent } from '../../../components/form/control/error.component';

@Directive({
    selector: '[appControlError]'
})
export class ControlErrorDirective implements OnInit, OnDestroy {
    @Input() customErrors: object = {};
    private ref: ComponentRef<ControlErrorComponent>;
    private container: ViewContainerRef;
    private readonly destroy$: Subject<boolean> = new Subject<boolean>();
    private submit$: Observable<Event>;
    private touched$: Observable<Event>;

    constructor(
        @Optional() controlErrorContainer: ControlErrorContainerDirective,
        @Inject(FORM_ERRORS) private errors: object,
        @Optional() @Host() private form: FormSubmitDirective,
        @Optional() @Host() private ngSelect: NgSelectComponent,
        private vcr: ViewContainerRef,
        private resolver: ComponentFactoryResolver,
        private controlRef: NgControl,
        private element: ElementRef<HTMLElement>
    ) {
        this.submit$ = this.form ? this.form.submit : EMPTY;
        this.container = controlErrorContainer ? controlErrorContainer.vcr : vcr;
        this.touched$ = this.ngSelect ?
            new Observable<Event>((observer: Observer<Event>) => {
                this.ngSelect.blurEvent.subscribe((event: Event) => {
                    observer.next(event);
                });
            }).pipe(takeUntil(this.destroy$))
            : fromEvent(this.element.nativeElement, 'blur').pipe(takeUntil(this.destroy$));
    }

    get control(): AbstractControl {
        return this.controlRef.control;
    }

    ngOnInit(): void {
        merge(this.submit$, this.touched$, this.control.valueChanges)
            .pipe(takeUntil(this.destroy$))
            .subscribe(() => {
                const controlErrors: ValidationErrors = this.control.errors;
                if (controlErrors) {
                    const firstKey: string = Object.keys(controlErrors)[0];
                    const getError: any = this.errors[firstKey];
                    const text: string = this.customErrors[firstKey] || getError && getError(controlErrors[firstKey]);
                    this.setError(text);
                } else if (this.ref) {
                    this.setError(null);
                }
            });
    }

    ngOnDestroy(): void {
        this.destroy$.next(true);
        this.destroy$.complete();
    }

    private setError(text: string): void {
        if (!this.ref) {
            const factory: ComponentFactory<ControlErrorComponent> = this.resolver.resolveComponentFactory(ControlErrorComponent);
            this.ref = this.container.createComponent(factory);
        }

        this.ref.instance.setErrorText(text);
    }
}
