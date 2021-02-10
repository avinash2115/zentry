import { ChangeDetectionStrategy, ChangeDetectorRef, Component } from '@angular/core';

@Component({
    selector: 'app-control-error',
    template: `
        <p
            *ngIf="!hide"
            class="control-error error"
        >{{text}}</p>
    `,
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ControlErrorComponent {
    public text: string;
    public hide: boolean = true;

    constructor(
        private cdr: ChangeDetectorRef
    ) {
        this.cdr.detach();
    }

    setErrorText(errorText: string): void {
        if (errorText !== this.text) {
            this.text = errorText;
            this.hide = !errorText;
            this.detectChanges();
        } else {
            this.detectChanges();
        }
    }

    private detectChanges(): void {
        if (!this.cdr['destroyed']) {
            this.cdr.detectChanges();
        }
    }
}
