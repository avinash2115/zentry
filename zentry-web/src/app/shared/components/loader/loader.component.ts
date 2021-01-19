import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../classes/abstracts/component/base-detached-component';

@Component({
    selector: 'app-loader',
    templateUrl: './loader.component.html',
    styleUrls: ['./loader.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class LoaderComponent extends BaseDetachedComponent implements OnInit {
    @Input() fullscreen: boolean = false;

    constructor(
        private cdr: ChangeDetectorRef,
    ) {
        super(cdr)
    }

    ngOnInit(): void {
    }
}
