import { Directive, TemplateRef } from '@angular/core';

@Directive({
    selector: '[appListHeaderTemplate]'
})
export class ListHeaderTemplateDirective {
    constructor(
        public template: TemplateRef<any>
    ) {
    }
}
