import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../shared/classes/abstracts/component/base-detached-component';
import { LoaderService } from '../../shared/services/loader.service';
import { takeUntil } from 'rxjs/operators';
import { LayoutService } from '../../shared/services/layout.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { LayoutComponent } from './layout.component';

@Component({
    selector: 'app-layout-custom',
    templateUrl: './layout.custom.component.html',
    styleUrls: ['./layout.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class LayoutCustomComponent extends LayoutComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
    ) {
        super(cdr, layoutService, loaderService);
    }
}
