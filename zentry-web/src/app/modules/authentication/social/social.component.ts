import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { AuthenticationService } from '../authentication.service';
import { AuthenticationSocialService } from '../authentication.social.service';
import { IconName } from '@fortawesome/fontawesome-common-types';
import { takeUntil } from 'rxjs/operators';
import { Observable } from 'rxjs';
import {
    DriverJsonapiResource as SSODriverJsonapiResource,
    EDriver
} from '../../../resources/sso/driver/driver.jsonapi.service';
import { LoaderService } from '../../../shared/services/loader.service';
import { Router } from '@angular/router';
import { SwalService } from '../../../shared/services/swal.service';
import { DataError } from '../../../shared/classes/data-error';

@Component({
    selector: 'app-authentication-social',
    templateUrl: './social.component.html',
    styleUrls: ['./social.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [AuthenticationSocialService]
})
export class SocialComponent extends BaseDetachedComponent implements OnInit {
    public drivers: Array<SSODriverJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        public authenticationSocialService: AuthenticationSocialService,
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.detectChanges();

        this.fetch().subscribe((drivers: Array<SSODriverJsonapiResource>) => {
            this.drivers = drivers;

            this.loadingCompleted();
        });
    }

    icon(driver: SSODriverJsonapiResource): IconName {
        switch (driver.driverType) {
            case EDriver.google:
                return 'google';
            default:
                throw Error('Provider is not supported');
        }
    }

    authorize(driver: SSODriverJsonapiResource): void {
        this.loaderService.show();

        this.authenticationService
            .social(driver)
            .pipe(takeUntil(this._destroy$))
            .subscribe(({redirectTo}: { redirectTo: string }) => {
                this.loaderService.hide();
                this.router.navigate([redirectTo]);
            }, (error: DataError) => {
                SwalService.error({title: 'Something went wrong', text: error.message});
                this.loaderService.hide();
            });
    }

    private fetch(): Observable<Array<SSODriverJsonapiResource>> {
        if (!this.isLoading) {
            this.loadingTrigger();
        }

        return this.authenticationSocialService.drivers;
    }
}
