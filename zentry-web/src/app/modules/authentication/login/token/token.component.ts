import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { ActivatedRoute, Router } from '@angular/router';
import { LayoutService } from '../../../../shared/services/layout.service';
import { AuthenticationService } from '../../authentication.service';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../shared/classes/data-error';
import { SwalService } from '../../../../shared/services/swal.service';

@Component({
    selector: 'app-authentication-login-token',
    templateUrl: './token.component.html',
    styleUrls: ['./token.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class TokenComponent extends BaseDetachedComponent implements OnInit {
    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.layoutService.changeTitle('Login');

        this.authenticationService
            .authorizeLoginToken(this.activatedRoute.snapshot.params.id)
            .pipe(takeUntil(this._destroy$))
            .subscribe(({redirectTo}: { redirectTo: string }) => {
                setTimeout(() => {
                    this.router.navigate([redirectTo]);
                }, 3000);
            }, (error: DataError) => {
                SwalService.error({title: 'Something went wrong', text: error.message}).then(() => {
                    this.router.navigate(['/auth/login']);
                });
            });
    }
}
