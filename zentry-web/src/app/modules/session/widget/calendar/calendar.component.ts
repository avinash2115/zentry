import { ChangeDetectionStrategy, ChangeDetectorRef, Component, EventEmitter, OnInit, Output } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionService } from '../../session.service';
import { EStatus, SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { RecordedService } from '../../recorded/recorded.service';
import { RecordedParticipantService } from '../../recorded/recorded.participant.service';
import { IpcService } from '../../../../shared/services/ipc.service';
import { takeUntil } from 'rxjs/operators';
import { UtilsService } from '../../../../shared/services/utils.service';
import { SwalService } from '../../../../shared/services/swal.service';
import * as moment from 'moment';

@Component({
    selector: 'app-session-widget-calendar',
    templateUrl: './calendar.component.html',
    styleUrls: ['./calendar.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [RecordedService, RecordedParticipantService]
})
export class CalendarComponent extends BaseDetachedComponent implements OnInit {
    public data: Array<SessionJsonapiResource> = [];
    private _current: SessionJsonapiResource | null;

    @Output() started: EventEmitter<SessionJsonapiResource> = new EventEmitter<SessionJsonapiResource>();

    constructor(
        protected cdr: ChangeDetectorRef,
        protected ipcService: IpcService,
        private _sessionService: SessionService
    ) {
        super(cdr);
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    get navigatable(): boolean {
        return this.data.length > 1;
    }

    get current(): SessionJsonapiResource {
        return this._current;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.fetch();
    }

    navigate(backwards: boolean = false): void {
        if (!(this.current instanceof SessionJsonapiResource)) {
            this._current = this.data[0] || null;
        } else {
            const index: number = this.data.findIndex((r: SessionJsonapiResource) => r.id === this.current.id);

            if (backwards) {
                if (index === 0) {
                    this._current = this.data[this.data.length - 1];
                } else {
                    this._current = this.data[index - 1];
                }
            } else {
                if (index === this.data.length - 1) {
                    this._current = this.data[0];
                } else {
                    this._current = this.data[index + 1];
                }
            }
        }

        this.detectChanges();
    }

    launch(): void {
        this.started.next(this.current);
    }

    fetch(): void {
        this.sessionService
            .list({
                    statuses: {
                        collection: [EStatus.new]
                    },
                    scheduled_to: {
                        range: {
                            gte: moment().utc().toISOString()
                        }
                    }
                }
            )
            .subscribe((data: Array<SessionJsonapiResource>) => {
                this.data = data;

                const today: Date = new Date();

                this._current = [...data].sort(function (a: SessionJsonapiResource, b: SessionJsonapiResource) {
                    return Math.abs(today.getTime() - a.scheduledToDate.toDate().getTime()) - Math.abs(today.getTime() - b.scheduledToDate.toDate().getTime());
                })[0];

                this.loadingCompleted();

                this.ipcService
                    .deepLink
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((url: string | null) => {
                        if (url !== null) {
                            this.ipcService.deepLinkHandled(url);

                            const id: string | null = UtilsService.getParameterByName('quickstart_id', '?' + url.split('://')[1]);

                            if (id !== null) {
                                const quickSession: SessionJsonapiResource | undefined = this.data.find((r: SessionJsonapiResource) => r.id === id);

                                if (quickSession instanceof SessionJsonapiResource) {
                                    SwalService.warning({
                                        title: 'Are you sure?',
                                        text: `You are going to start the ${quickSession.name} session`
                                    }).then((answer: { value: boolean }) => {
                                        if (answer.value) {
                                            this.started.next(quickSession);
                                        }
                                    });
                                }
                            }
                        }
                    });
            });
    }
}
