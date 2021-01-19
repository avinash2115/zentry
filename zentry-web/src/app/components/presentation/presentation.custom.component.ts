import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../shared/classes/abstracts/component/base-detached-component';
import { takeUntil } from 'rxjs/operators';
import { LayoutService } from '../../shared/services/layout.service';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { PresentationComponent } from './presentation.component';
import { Router } from '@angular/router';

@Component({
    selector: 'app-presentation-custom',
    templateUrl: './presentation.custom.component.html',
    styleUrls: ['./presentation.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class PresentationCustomComponent extends PresentationComponent{
    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService,
        protected router: Router
    ) {
        super(cdr, layoutService, authenticationService, router);
    }
}
