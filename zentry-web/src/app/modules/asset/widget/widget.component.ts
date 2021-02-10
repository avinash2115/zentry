import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { LoaderService } from '../../../shared/services/loader.service';

@Component({
    selector: 'app-asset-widget',
    templateUrl: './widget.component.html',
    styleUrls: ['./widget.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class WidgetComponent extends BaseDetachedComponent implements OnInit {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
    ) {
        super(cdr);
    }

    get macOS(): string {
        return window.assets.widgets.macOS;
    }

    get windows(): string {
        return window.assets.widgets.windows;
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Download Widget');
    }
}
