import { Directive, TemplateRef } from '@angular/core';

@Directive({
    selector: '[appListBodyStates]'
})
export class ListBodyStatesDirective {
    constructor(
        public template: TemplateRef<any>
    ) {
    }
}
