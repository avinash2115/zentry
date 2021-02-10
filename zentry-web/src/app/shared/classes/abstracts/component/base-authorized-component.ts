import { ChangeDetectorRef } from '@angular/core';
import { BaseDetachedComponent } from './base-detached-component';
import { LoaderService } from '../../../services/loader.service';
import { takeUntil } from 'rxjs/operators';
import { AuthenticationService } from '../../../../modules/authentication/authentication.service';
import { UserJsonapiResource } from '../../../../resources/user/user.jsonapi.service';

export class BaseAuthorizedComponent extends BaseDetachedComponent {
    public authUser: UserJsonapiResource;

    public loadingFullScreen: boolean = false;

    constructor(
        protected cdrRef: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
    ) {
        super(cdrRef);
    }

    initialize(callback?: (authUser: UserJsonapiResource) => void): void {
        this.loaderService
            .state
            .pipe(takeUntil(this._destroy$))
            .subscribe((state: boolean) => {
                if (state) {
                    document.getElementsByTagName('body')[0].classList.add('locked');
                } else {
                    document.getElementsByTagName('body')[0].classList.remove('locked');
                }

                this.loadingFullScreen = state;
                this.detectChanges();
            });

        this.authenticationService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((authUser: UserJsonapiResource) => {
                this.authUser = authUser;
                this.detectChanges();

                if (callback) {
                    callback(authUser);
                }
            });
    }
}
