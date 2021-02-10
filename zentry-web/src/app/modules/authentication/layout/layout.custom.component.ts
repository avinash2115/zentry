import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit, ViewEncapsulation } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutComponent } from './layout.component';

@Component({
    selector: 'app-layout-custom',
    templateUrl: './layout.custom.component.html',
    styleUrls: ['./layout.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    encapsulation: ViewEncapsulation.None
})
export class LayoutCustomComponent extends LayoutComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
    ) {
        super(cdr);
    }
}
