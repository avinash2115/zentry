import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { RecordedService } from '../../recorded.service';
import { takeUntil } from 'rxjs/operators';
import { ITranscript } from '../../recorded.poi.transcript.service';
import { combineLatest } from 'rxjs';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';

@Component({
    selector: 'app-session-recorded-view-transcript',
    templateUrl: './transcript.component.html',
    styleUrls: ['./transcript.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class TranscriptComponent extends BaseDetachedComponent implements OnInit {
    public recordedEntity: SessionJsonapiResource | null;
    public data: Array<ITranscript> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        private _recordedService: RecordedService,
    ) {
        super(cdr);
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        combineLatest([
            this.recordedService.entity,
            this.recordedService.poiService.transcriptService.transcripts,
        ])
            .pipe(takeUntil(this._destroy$))
            .subscribe(([entity, data]: [SessionJsonapiResource, Array<ITranscript>]) => {
                this.recordedEntity = entity;
                this.data = data.sort((a: ITranscript, b: ITranscript) => a.poi.startedAtDate.unix() - b.poi.startedAtDate.unix())
                this.loadingCompleted();
            });
    }
}
