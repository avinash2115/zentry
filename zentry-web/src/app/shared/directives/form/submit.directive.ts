import { Directive, ElementRef, OnDestroy } from '@angular/core';
import { fromEvent } from 'rxjs/internal/observable/fromEvent';
import { shareReplay, takeUntil } from 'rxjs/operators';
import { Observable } from 'rxjs/internal/Observable';
import { Subject } from 'rxjs/internal/Subject';

@Directive({
    selector: '[appFormSubmit]'
})
export class FormSubmitDirective implements OnDestroy {
    private readonly destroy$: Subject<boolean> = new Subject<boolean>();
    private readonly submit$: Observable<Event> = fromEvent(this.element, 'submit')
        .pipe(shareReplay(1), takeUntil(this.destroy$));

    constructor(
        private host: ElementRef<HTMLFormElement>
    ) {
    }

    get submit(): Observable<Event> {
        return this.submit$;
    }

    private get element(): HTMLFormElement {
        return this.host.nativeElement;
    }

    ngOnDestroy(): void {
        this.destroy$.next(true);
        this.destroy$.complete();
    }
}
