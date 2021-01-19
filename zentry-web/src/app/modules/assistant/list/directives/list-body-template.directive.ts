import { Directive, TemplateRef } from '@angular/core';

@Directive({
    selector: '[appListBodyTemplate]'
})
export class ListBodyTemplateDirective {
    constructor(
        public template: TemplateRef<any>
    ) {
    }
}
