import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { LoaderService } from '../../../shared/services/loader.service';
import { WidgetComponent } from './widget.component';

@Component({
    selector: 'app-asset-widget-custom',
    templateUrl: './widget.custom.component.html',
    styleUrls: ['./widget.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class WidgetCustomComponent extends WidgetComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
    ) {
        super(cdr, layoutService, loaderService);
    }
}
