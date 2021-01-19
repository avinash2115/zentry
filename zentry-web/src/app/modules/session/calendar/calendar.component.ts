import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { CalendarEvent, CalendarView } from 'angular-calendar';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import * as moment from 'moment';
import { WeekDay } from '@angular/common';
import { SessionService } from '../session.service';
import { SessionJsonapiResource, EStatus } from '../../../resources/session/session.jsonapi.service';
import { AbstractControl, FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { WhitespaceValidator } from '../../../shared/validators/whitespace.validator';
import { LoaderService } from '../../../shared/services/loader.service';
import {
    ParticipantJsonapiResource as UserParticipantJsonapiResource,
    ParticipantJsonapiResource
} from '../../../resources/user/participant/participant.jsonapi.service';
import { throttleable } from '../../../shared/decorators/throttleable.decorator';
import { switchMap, take, takeUntil } from 'rxjs/operators';
import { Subject } from 'rxjs/internal/Subject';
import { SessionParticipantService } from '../session.participant.service';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { LayoutService } from '../../../shared/services/layout.service';
import { CrmService } from '../../../shared/services/crm.service';
import { MonthViewDay } from 'calendar-utils';
import { TeamJsonapiResource } from '../../../resources/user/team/team.jsonapi.service';
import { SchoolJsonapiResource } from '../../../resources/user/team/school/school.jsonapi.service';
import { ServiceJsonapiResource } from '../../../resources/service/service.jsonapi.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';

@Component({
    selector: 'app-session-calendar',
    templateUrl: './calendar.component.html',
    styleUrls: ['./calendar.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        SessionService,
        SessionParticipantService
    ]
})
export class CalendarComponent extends BaseDetachedComponent {
    @Input() embedded: boolean = false;

    public form: FormGroup;
    public events: Array<CalendarEvent> = [];
    public monthDay: MonthViewDay | null = null;
    public available: Array<ParticipantJsonapiResource> = [];
    public participants: Array<ParticipantJsonapiResource> = [];

    public readonly views: typeof CalendarView = CalendarView;
    private _viewActive: CalendarView = CalendarView.Week;
    private _viewDate: Date = new Date();

    private _services: Array<ServiceJsonapiResource> = [];
    private _teams: Array<TeamJsonapiResource> = [];
    private _schools: Array<SchoolJsonapiResource> = [];

    private readonly _formDestroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        protected cdr: ChangeDetectorRef,
        protected fb: FormBuilder,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected sessionService: SessionService,
        public crmService: CrmService
    ) {
        super(cdr);
    }

    get viewActive(): CalendarView {
        return this._viewActive;
    }

    get viewDate(): Date {
        return this._viewDate;
    }

    set viewDate(value: Date) {
        this._viewDate = value;

        this.detectChanges();
    }

    get services(): Array<ServiceJsonapiResource> {
        return this._services;
    }

    get schools(): Array<SchoolJsonapiResource> {
        return this._schools;
    }

    get participantsSelected(): Array<UserParticipantJsonapiResource> {
        return (this.form.get('participants') as FormArray).controls.map((c: AbstractControl) => c.value as UserParticipantJsonapiResource);
    }

    ngOnInit(): void {
        if (!this.embedded) {
            this.layoutService.changeTitle('Schedule');
        }

        this.fetch();
    }

    fetch(): void {
        this.loadingTrigger();

        this.sessionService
            .list({}, {[this.sessionService.sessionJsonapiService.type]: [`-scheduled_on`]})
            .subscribe((data: Array<SessionJsonapiResource>) => {
                this.events = data
                    .filter((r: SessionJsonapiResource) => {
                        return r.isScheduled || r.isEnded || r.isWrapped;
                    })
                    .map((r: SessionJsonapiResource) => {
                        return {
                            id: r.id,
                            title: r.name,
                            start: r.isScheduled && r.isNew ? r.scheduledOnDate.toDate() : r.startedAtDate.toDate(),
                            end: r.isScheduled && !r.isEnded ? r.scheduledToDate.toDate() : r.endedAtDate.toDate(),
                            meta: {
                                resource: r
                            }
                        };
                    });

                this.handleEvents();

                this.loadingCompleted();

                this.viewSwitch(this._viewActive);
            });
    }

    viewSwitch(value: CalendarView): void {
        this._viewActive = value;

        this.detectChanges();

        setTimeout(() => {
            switch (this._viewActive) {
                case CalendarView.Week:
                    document.getElementsByClassName('cal-week-view')[0].scrollTop = (document.getElementsByClassName('calendar--weekly--timemarker')[0] as HTMLElement).offsetTop - 30;
                    this.detectChanges();
                    break;
                case CalendarView.Day:
                    document.getElementsByClassName('cal-week-view')[0].scrollTop = (document.getElementsByClassName('calendar--daily--timemarker')[0] as HTMLElement).offsetTop - 30;
                    this.detectChanges();
                    break;
            }
        }, 100);
    }

    copy(): void {
        const entity: SessionJsonapiResource = this.form.get('resource').value;
        const resource: SessionJsonapiResource = this.sessionService.sessionJsonapiService.new();

        Converter.build(entity.toObject(), resource);

        resource.id = '';
        resource.is_new = true;
        resource.status = EStatus.new;

        this.build(resource);
    }

    delete(): void {
        const resource: SessionJsonapiResource = this.form.get('resource').value;

        SwalService.warning({
            title: 'Are you sure?',
            text: `You are going to remove ${resource.name}!`
        }).then((answer: { value: boolean }) => {
            if (answer.value) {
                this.loaderService.show();

                this.sessionService
                    .remove(resource)
                    .subscribe((status: boolean) => {
                        if (status) {
                            const idx: number = this.events.findIndex((event: CalendarEvent) => event.id === resource.id);
                            this.events.splice(idx, 1);
                            this.handleEvents();
                            this.cancel();
                            this.detectChanges();
                        }
                        this.loaderService.hide();
                    }, (error: DataError) => {
                        this.loaderService.hide();
                        this.fallback(error);
                    });
            }
        });
    }

    viewIsActive(value: CalendarView): boolean {
        return this.viewActive === value;
    }

    isToday(date: Date): boolean {
        return moment().isSame(moment(date), 'd');
    }

    isWeekend(date: Date): boolean {
        const weekday: number = moment(date).weekday();

        return weekday === WeekDay.Saturday || weekday === WeekDay.Sunday;
    }

    sort(events: Array<CalendarEvent>): Array<CalendarEvent> {
        return events.sort((a: CalendarEvent, b: CalendarEvent) => a.start.getTime() - b.start.getTime());
    }

    limit(events: Array<CalendarEvent>): Array<CalendarEvent> {
        return events.slice(0, 3);
    }

    colorCoding(event: CalendarEvent | undefined): string {
        switch (event.meta.resource.sessionType) {
            default:
                return '';
        }
    }

    build(entity?: SessionJsonapiResource): void {
        this.loaderService.show();

        combineLatest([
            this.sessionService.serviceService.list({}, {[this.sessionService.serviceService.serviceJsonapiService.type]: [`name`]}),
            this.sessionService.userService.teamService.list(),
            this.sessionService.participantService.fetchAvailable()
        ]).pipe(take(1))
            .subscribe(([services, teams, participants]: [Array<ServiceJsonapiResource>, Array<TeamJsonapiResource>, Array<UserParticipantJsonapiResource>]) => {
                this._services = services;
                this._teams = teams;
                this._schools = [];

                teams.forEach((t: TeamJsonapiResource) => {
                    this._schools = this._schools.concat(t.schools);
                });

                this._schools.sort((a: SchoolJsonapiResource, b: SchoolJsonapiResource) => a.name.localeCompare(b.name));

                this.participants = participants;

                this.form = this.fb.group({
                    resource: [entity instanceof SessionJsonapiResource ? entity : this.sessionService.sessionJsonapiService.new()],
                    name: [entity instanceof SessionJsonapiResource ? entity.name : null, [Validators.required, Validators.maxLength(255), WhitespaceValidator]],
                    scheduled_date: [entity instanceof SessionJsonapiResource && entity.scheduledOn ?
                        (entity.isEnded ? entity.startedAtDate.format('YYYY-MM-DD') : entity.scheduledOnDate.format('YYYY-MM-DD'))
                        : null, [Validators.required]],
                    scheduled_on: [entity instanceof SessionJsonapiResource && entity.scheduledOn ?
                        (entity.isEnded ? entity.startedAtDate.format('HH:mm') : entity.scheduledOnDate.format('HH:mm'))
                        : null, [Validators.required]],
                    scheduled_to: [entity instanceof SessionJsonapiResource && entity.scheduledTo ?
                        (entity.isEnded ? entity.endedAtDate.format('HH:mm') : entity.scheduledToDate.format('HH:mm'))
                        : null, [Validators.required]],
                    service_id: [entity instanceof SessionJsonapiResource && entity.service instanceof ServiceJsonapiResource ? entity.service.id : null, [Validators.required]],
                    school_id: [entity instanceof SessionJsonapiResource && entity.school instanceof SchoolJsonapiResource ? entity.school.id : null, [Validators.required]],
                    participant_id: [null],
                    participants: new FormArray(
                        entity instanceof SessionJsonapiResource ?
                            entity.participants.map((r: ParticipantJsonapiResource) => {
                                return new FormControl(r);
                            }) : []
                    )
                });

                if (entity instanceof SessionJsonapiResource && !entity.is_new && (!entity.isNew || entity.hasSources)) {
                    this.form.disable();
                } else {
                    if (entity instanceof SessionJsonapiResource) {
                        this.detectChanges();

                        this.form.enable();
                    }
                }

                this.selectControlFilter();

                this.form
                    .valueChanges
                    .pipe(takeUntil(this._formDestroy$))
                    .subscribe(() => this.detectChanges());

                this.form
                    .get('participant_id')
                    .valueChanges
                    .pipe(takeUntil(this._formDestroy$))
                    .subscribe((value: UserParticipantJsonapiResource | null) => {
                        if (value instanceof UserParticipantJsonapiResource) {
                            const index: number = (this.form.get('participants') as FormArray).controls.findIndex((c: AbstractControl) => c.value.id === value.id);

                            if (index === -1) {
                                (this.form.get('participants') as FormArray).controls.push(new FormControl(value));
                            }

                            this.form.get('participant_id').patchValue(null, {emitEvent: true});

                            this.selectControlFilter();
                        }

                        this.detectChanges();
                    });

                this.loaderService.hide();

                this.detectChanges();
            });
    }

    submit(): void {
        if (this.form.valid) {
            this.loaderService.show();

            const {
                resource,
                name,
                scheduled_date,
                scheduled_on,
                scheduled_to,
                service_id,
                school_id,
                participants
            } = this.form.getRawValue();

            if (resource.is_new) {
                this.sessionService
                    .make()
                    .pipe(
                        switchMap((entity: SessionJsonapiResource) => {
                            entity.name = name;
                            entity.scheduledOn = moment(`${scheduled_date} ${scheduled_on}`).toISOString(false);
                            entity.scheduledTo = moment(`${scheduled_date} ${scheduled_to}`).toISOString(false);
                            entity.addRelationshipsArray(participants, 'participants');

                            if (!!service_id) {
                                const service: ServiceJsonapiResource | undefined = this.services.find((s: ServiceJsonapiResource) => s.id === service_id);

                                if (service instanceof ServiceJsonapiResource) {
                                    entity.addRelationship(service, 'service');
                                }
                            }

                            if (!!school_id) {
                                const school: SchoolJsonapiResource | undefined = this.schools.find((s: SchoolJsonapiResource) => s.id === school_id);

                                if (school instanceof SchoolJsonapiResource) {
                                    const team: TeamJsonapiResource | undefined = this._teams
                                        .find((t: TeamJsonapiResource) => t.schools.findIndex((s: SchoolJsonapiResource) => s.id === school_id) !== -1);

                                    entity.addRelationship(team, 'team');
                                    entity.addRelationship(school, 'school');
                                }
                            }

                            return this.sessionService.save();
                        })
                    )
                    .subscribe((r: SessionJsonapiResource) => {
                        this.events.push({
                            id: r.id,
                            title: r.name,
                            start: r.isScheduled && r.isNew ? r.scheduledOnDate.toDate() : r.startedAtDate.toDate(),
                            end: r.isScheduled && !r.isEnded ? r.scheduledToDate.toDate() : r.endedAtDate.toDate(),
                            meta: {
                                resource: r
                            }
                        });

                        this.events = [...this.events];

                        this.handleEvents();

                        this.loaderService.hide();

                        SwalService.toastSuccess({title: `${r.name} has been scheduled!`});

                        this.cancel();
                        this.detectChanges();
                    }, (error: DataError) => {
                        this.loaderService.hide();
                        this.fallback(error);
                    });
            } else {
                this.sessionService
                    .get(resource.id)
                    .pipe(
                        switchMap((entity: SessionJsonapiResource) => {
                            entity.name = name;
                            entity.scheduledOn = moment(`${scheduled_date} ${scheduled_on}`).toISOString(false);
                            entity.scheduledTo = moment(`${scheduled_date} ${scheduled_to}`).toISOString(false);
                            entity.participants.forEach((r: UserParticipantJsonapiResource) => {
                                entity.removeRelationship('participants', r.id);
                            });
                            entity.addRelationshipsArray(participants, 'participants');

                            return this.sessionService.save();
                        })
                    )
                    .subscribe((r: SessionJsonapiResource) => {
                        const event: CalendarEvent = {
                            id: r.id,
                            title: r.name,
                            start: r.isScheduled && r.isNew ? r.scheduledOnDate.toDate() : r.startedAtDate.toDate(),
                            end: r.isScheduled && !r.isEnded ? r.scheduledToDate.toDate() : r.endedAtDate.toDate(),
                            meta: {
                                resource: r
                            }
                        };

                        const index: number = this.events.findIndex((e: CalendarEvent) => e.id === r.id);

                        if (index !== -1) {
                            this.events[index] = event;
                            this.events = [...this.events];
                        } else {
                            this.events.push(event);
                        }

                        this.handleEvents();

                        this.loaderService.hide();

                        SwalService.toastSuccess({title: `${r.name} has been changed!`});
                        this.cancel();
                        this.detectChanges();
                    }, (error: DataError) => {
                        this.loaderService.hide();
                        this.fallback(error);
                    });
            }
        } else {
            this.form.markAllAsTouched();
            this.form.updateValueAndValidity();

            this.detectChanges();
        }
    }

    cancel(): void {
        this.form = null;
        this._formDestroy$.next(true);
        this.detectChanges();
    }

    participantRemove(entity: UserParticipantJsonapiResource): void {
        const index: number = (this.form.get('participants') as FormArray).controls.findIndex((c: AbstractControl) => c.value.id === entity.id);

        if (index !== -1) {
            (this.form.get('participants') as FormArray).controls.splice(index, 1);
        }

        this.selectControlFilter();
    }

    @throttleable(150)
    ngSelectMouseover(): void {
        this.detectChanges();
    }

    selectControlSearch(term: string, item: UserParticipantJsonapiResource): boolean {
        term = term.toLocaleLowerCase();

        return item.fullname.toLocaleLowerCase().indexOf(term) > -1 || item.email.toLocaleLowerCase().indexOf(term) > -1;
    }

    dayEventsListToggle(day?: MonthViewDay): void {
        this.monthDay = day;
        this.detectChanges();
    }

    private selectControlFilter(): void {
        this.available = [];

        const selected: Array<string> = (this.form.get('participants') as FormArray).controls.map((c: AbstractControl) => {
            return c.value.id;
        });

        this.available = [...this.participants.filter((r: UserParticipantJsonapiResource) => !selected.includes(r.id))];
        this.available.sort((a: UserParticipantJsonapiResource, b: UserParticipantJsonapiResource) => a.fullname.localeCompare(b.fullname));
    }

    private handleEvents(): void {
        this.events = [...this.events.map((r: CalendarEvent) => {
            if (r.end.getTime() - r.start.getTime() < 900000) {
                r.end = new Date(r.start.getTime() + 900000);
            }

            return r;
        })];
    }
}
