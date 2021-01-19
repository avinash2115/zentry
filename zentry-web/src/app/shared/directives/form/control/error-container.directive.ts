import { Directive, ViewContainerRef } from '@angular/core';

@Directive({
    selector: '[appControlErrorContainer]'
})
export class ControlErrorContainerDirective {
    constructor(
        public vcr: ViewContainerRef
    ) {
    }
}
