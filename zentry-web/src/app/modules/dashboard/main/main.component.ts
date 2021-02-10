import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../shared/services/layout.service';
import { UserJsonapiResource } from '../../../resources/user/user.jsonapi.service';
import { AuthenticationService } from '../../authentication/authentication.service';
import { takeUntil } from 'rxjs/operators';

@Component({
    selector: 'app-dashboard-main',
    templateUrl: './main.component.html',
    styleUrls: ['./main.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class MainComponent extends BaseDetachedComponent implements OnInit {
    public today: Date = new Date();
    public authUser: UserJsonapiResource;

    private todayTimer: any;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected authenticationService: AuthenticationService,
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.hidePresentation();
        this.layoutService.unwrapContent();

        this.layoutService.changeTitle('Dashboard');

        this.todayTimer = window.helpers.interval(() => {
            this.today = new Date();
            this.detectChanges();
        }, 1000);

        this._destroy$
            .subscribe(() => {
                this.layoutService.showPresentation();
                this.layoutService.wrapContent();
                this.todayTimer.clear();
            });

        this.authenticationService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((user: UserJsonapiResource) => {
                this.authUser = user;

                this.loadingCompleted();
            });
    }
}
