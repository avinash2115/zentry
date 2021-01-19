import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { UserJsonapiResource } from '../../../resources/user/user.jsonapi.service';
import { AuthenticationService } from '../../authentication/authentication.service';
import { takeUntil } from 'rxjs/operators';
import { MainComponent } from './main.component';

@Component({
    selector: 'app-dashboard-main-custom',
    templateUrl: './main.custom.component.html',
    styleUrls: ['./main.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class MainCustomComponent extends MainComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService,
    ) {
        super(cdr, layoutService, authenticationService);
    }
}
