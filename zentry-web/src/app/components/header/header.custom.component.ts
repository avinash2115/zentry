import { ChangeDetectionStrategy, ChangeDetectorRef, Component } from '@angular/core';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { ActivatedRoute, Router } from '@angular/router';
import { EchoService } from '../../shared/services/echo.service';
import { LoaderService } from '../../shared/services/loader.service';
import { HeaderComponent } from './header.component';

@Component({
    selector: 'app-header-custom',
    templateUrl: './header.custom.component.html',
    styleUrls: ['./header.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class HeaderCustomComponent extends HeaderComponent {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected echoService: EchoService,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr, router, activatedRoute, echoService, loaderService, authenticationService);
    }
}
