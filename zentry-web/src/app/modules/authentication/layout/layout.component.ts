import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit, ViewEncapsulation } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';

@Component({
    selector: 'app-layout',
    templateUrl: './layout.component.html',
    styleUrls: ['./layout.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    encapsulation: ViewEncapsulation.None
})
export class LayoutComponent extends BaseDetachedComponent implements OnInit {

    constructor(
        protected cdr: ChangeDetectorRef,
    ) {
        super(cdr);
    }

    get isNative(): boolean {
        return window.config.native;
    }

    ngOnInit(): void {
    }
}
