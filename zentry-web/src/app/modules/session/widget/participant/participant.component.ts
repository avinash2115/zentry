import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit, ViewChild } from '@angular/core';
import { switchMap, takeUntil } from 'rxjs/operators';
import { ParticipantJsonapiResource } from '../../../../resources/user/participant/participant.jsonapi.service';
import { SessionService } from '../../session.service';
import { PerfectScrollbarConfigInterface, PerfectScrollbarDirective } from 'ngx-perfect-scrollbar';
import { throttleable } from '../../../../shared/decorators/throttleable.decorator';
import { NgSelectComponent } from '@ng-select/ng-select';
import { DataError } from '../../../../shared/classes/data-error';
import { EMAIL_VALIDATOR_PATTERN } from '../../../../shared/consts/form/patterns';
import { Converter } from '../../../../../vendor/vp-ngx-jsonapi/services/converter';
import { UserJsonapiResource } from '../../../../resources/user/user.jsonapi.service';
import { BaseAuthorizedComponent } from '../../../../shared/classes/abstracts/component/base-authorized-component';
import { LoaderService } from '../../../../shared/services/loader.service';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { UtilsService } from '../../../../shared/services/utils.service';

@Component({
    selector: 'app-session-widget-participant',
    templateUrl: './participant.component.html',
    styleUrls: ['./participant.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantComponent extends BaseAuthorizedComponent implements OnInit {
    @ViewChild('selectedList', {static: false}) public selectedList: PerfectScrollbarDirective;

    public selected: Array<ParticipantJsonapiResource> = [];
    public attached: Array<ParticipantJsonapiResource> = [];
    public available: Array<ParticipantJsonapiResource> = [];

    public isForm: boolean = false;

    public readonly scrollbarConfig: PerfectScrollbarConfigInterface = {
        suppressScrollY: true,
    }

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected authenticationService: AuthenticationService,
        protected _sessionService: SessionService,
    ) {
        super(cdr, loaderService, authenticationService);

        cdr.detach();
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    get formattedAvailableParticipants(): Array<ParticipantJsonapiResource> {
        return this.available.sort((a, b) => a.fullname.localeCompare(b.fullname))
    }

    ngOnInit(): void {
        super.initialize((authUser: UserJsonapiResource) => {
            this.sessionService
                .userService
                .get(authUser.id)
                .pipe(
                    switchMap(() => this.sessionService.participantService.fetchAvailable())
                )
                .subscribe((data: Array<ParticipantJsonapiResource>) => {
                    this.available = data;
                }, (error: DataError) => this.fallback(error))
        });

        this.sessionService
            .participantService
            .selected
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                this.selected = data;
                this.detectChanges();
            });

        this.sessionService
            .participantService
            .attached
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                this.attached = data;
                this.detectChanges();
            });

        this.sessionService
            .participantService
            .available
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<ParticipantJsonapiResource>) => {
                this.available = data;
                this.detectChanges();
            });
    }

    @throttleable(150)
    ngSelectMouseover(): void {
        this.detectChanges();
    }

    isScrollLeftAvailable(): boolean {
        return this.selectedList && this.selectedList.ps().scrollbarXActive && this.selectedList.ps().reach.x !== 'start';
    }

    isScrollRightAvailable(): boolean {
        return this.selectedList && this.selectedList.ps().scrollbarXActive && this.selectedList.ps().reach.x !== 'end';
    }

    scrollLeft(): void {
        if (this.isScrollLeftAvailable()) {
            this.selectedList.scrollToX(Number(this.selectedList.ps().lastScrollLeft) - 48, 500);
            this.detectChanges();
        }
    }

    scrollRight(): void {
        if (this.isScrollRightAvailable()) {
            this.selectedList.scrollToX(Number(this.selectedList.ps().lastScrollLeft) + 48, 500);
            this.detectChanges();
        }
    }

    selectControlSearch(term: string, item: ParticipantJsonapiResource): boolean {
        term = term.toLocaleLowerCase();

        return item.fullname.toLocaleLowerCase().indexOf(term) > -1 || item.email.toLocaleLowerCase().indexOf(term) > -1;
    }

    selectControlPick(entities: ParticipantJsonapiResource[], ngSelectComponent?: NgSelectComponent): void {
        if (ngSelectComponent) {
            ngSelectComponent.close();
        }

        this.loaderService.show();

        this.sessionService
            .participantService
            .addMultiple(entities)
            .subscribe(() => {
                this.isForm = false;
                this.loaderService.hide();
            }, (error: DataError) => {
                this.fallback(error);
                this.loaderService.hide();
            });

        this.detectChanges();
    }

    selectControlCreate(subject: string): Promise<ParticipantJsonapiResource> {
        return new Promise((resolve, reject) => {
            const regex = new RegExp(EMAIL_VALIDATOR_PATTERN);
            if (!regex.test(subject) && !subject.split(' ')[0]) {
                reject();
            } else {
                const resource: ParticipantJsonapiResource = Converter.getService('users_participants').new() as ParticipantJsonapiResource;

                if ((new RegExp(EMAIL_VALIDATOR_PATTERN)).test(subject)) {
                    resource.email = subject;
                    resource.firstName = '';
                    resource.lastName = '';
                } else {
                    const fullName: Array<string> = subject.split(' ');
                    resource.email = '';
                    resource.firstName = fullName[0];
                    resource.lastName = '';

                    if (fullName[1] !== undefined) {
                        resource.lastName = fullName[1];
                    }
                }

                resolve(resource);
            }
        });
    }

    remove(entity: ParticipantJsonapiResource): void {
        this.sessionService
            .participantService
            .remove(entity)
            .subscribe(() => {
                this.detectChanges();
            }, (error: DataError) => {
                this.fallback(error);
            });
    }

    isAttached(entity: ParticipantJsonapiResource): boolean {
        return this.attached.findIndex((r: ParticipantJsonapiResource) => r.id === entity.id) !== -1;
    }

    attach(entity: ParticipantJsonapiResource): void {
        if (this.sessionService.isStarted) {
            this.sessionService
                .participantService
                .attach(entity)
                .subscribe(() => {
                    this.detectChanges();
                }, (error: DataError) => {
                    this.fallback(error);
                });
        }
    }
}
