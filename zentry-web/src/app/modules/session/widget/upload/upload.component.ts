import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionUploadService } from '../../session.upload.service';
import { SessionService } from '../../session.service';
import { filter, takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../shared/classes/data-error';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';

@Component({
    selector: 'app-session-widget-upload',
    templateUrl: './upload.component.html',
    styleUrls: ['./upload.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class UploadComponent extends BaseDetachedComponent implements OnInit {
    public list: Array<SessionJsonapiResource> = [];
    public listToggled: boolean = false;

    public paused: Array<SessionJsonapiResource> = [];

    public current: SessionJsonapiResource | null = null;
    public currentProgress: number = 0;

    public pausing: SessionJsonapiResource | null = null;
    public resuming: SessionJsonapiResource | null = null;

    constructor(
        private cdr: ChangeDetectorRef,
        private _sessionService: SessionService,
        private _sessionUploadService: SessionUploadService,
    ) {
        super(cdr);

        cdr.detach();
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    get queueList(): Array<SessionJsonapiResource> {
        return this.list;
    }

    get locked(): boolean {
        return !!this.pausing || !!this.resuming;
    }

    ngOnInit(): void {
        this._sessionUploadService
            .list
            .pipe(takeUntil(this._destroy$))
            .subscribe((queue: Array<SessionJsonapiResource>) => {
                this.list = queue;
                this.detectChanges();
            });

        this._sessionUploadService
            .paused
            .pipe(takeUntil(this._destroy$))
            .subscribe((paused: Array<SessionJsonapiResource>) => {
                this.paused = paused;
                this.detectChanges();
            });

        this._sessionUploadService
            .current
            .pipe(
                filter((value: SessionJsonapiResource | null) => value !== null),
                takeUntil(this._destroy$)
            )
            .subscribe((value: SessionJsonapiResource | null) => {
                this.current = value;
                this.detectChanges();
            });

        this._sessionUploadService
            .currentProgress
            .pipe(
                takeUntil(this._destroy$)
            )
            .subscribe((value: number) => {
                this.currentProgress = value;
                this.detectChanges();
            });
    }

    queueToggle(): void {
        this.listToggled = !this.listToggled;
        this.detectChanges();
    }

    pause(entity: SessionJsonapiResource): void {
        this.pausing = entity;
        this.detectChanges();

        this._sessionUploadService
            .pause(entity)
            .subscribe(() => {
                this.pausing = null;
                this.detectChanges();
            }, (error: DataError) => {
                this.pausing = null;
                this.detectChanges();
                this.fallback(error);
            });
    }

    resume(entity: SessionJsonapiResource): void {
        this.resuming = entity;
        this.detectChanges();

        this._sessionUploadService
            .resume(entity)
            .subscribe(() => {
                this.resuming = null;
                this.detectChanges();
            }, (error: DataError) => {
                this.resuming = null;
                this.detectChanges();
                this.fallback(error);
            });
    }

    isRecording(entity: SessionJsonapiResource): boolean {
        return this.sessionService.isStarted && this.sessionService.identity === entity.id;
    }

    isCurrent(entity: SessionJsonapiResource): boolean {
        return this.current && this.current.id === entity.id;
    }

    isPaused(entity: SessionJsonapiResource): boolean {
        return this.paused.findIndex((r: SessionJsonapiResource) => r.id === entity.id) !== -1;
    }

    isPausing(entity: SessionJsonapiResource): boolean {
        return this.pausing && this.pausing.id === entity.id;
    }

    isResuming(entity: SessionJsonapiResource): boolean {
        return this.resuming && this.resuming.id === entity.id;
    }
}
