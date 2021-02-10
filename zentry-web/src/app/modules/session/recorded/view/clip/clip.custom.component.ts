import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnChanges, OnInit, SimpleChanges } from '@angular/core';
import { RecordedService } from '../../recorded.service';
import { takeUntil } from 'rxjs/operators';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../../../resources/session/poi/poi.jsonapi.service';
import { combineLatest } from 'rxjs';
import { ITranscript } from '../../recorded.poi.transcript.service';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';
import { ClipComponent } from './clip.component';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../resources/user/participant/participant.jsonapi.service';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../../../../../resources/session/poi/participant/participant.jsonapi.service';

@Component({
    selector: 'app-session-recorded-view-clip-custom',
    templateUrl: './clip.custom.component.html',
    styleUrls: ['./clip.custom.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ClipCustomComponent extends ClipComponent implements OnInit, OnChanges {
    @Input() participant: UserParticipantJsonapiResource;

    private _pois: Array<SessionPoiJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected _loaderService: LoaderService,
        protected _recordedService: RecordedService
    ) {
        super(cdr, _loaderService, _recordedService);

        cdr.detach();
    }

    ngOnInit(): void {
        this.loadingTrigger();

        combineLatest([
            this.recordedService.entity,
            this.recordedService.poiService.pois,
            this.recordedService.poiService.transcriptService.transcripts
        ]).pipe(takeUntil(this._destroy$))
            .subscribe(([entity, pois, transcripts]: [SessionJsonapiResource, Array<SessionPoiJsonapiResource>, Array<ITranscript>]) => {
                this.recordedEntity = entity;
                this._pois = pois;

                this.init();

                if (this.recordedEntity && !this.recordedEntity.isEntirelyRecorded) {
                    this.seek(this.data[0], false);
                }

                this.loadingCompleted();

                setTimeout(() => {
                    this.detectChanges();
                }, 300);
            });
    }

    ngOnChanges(changes: SimpleChanges): void {
        this.init();
    }

    private init(): void {
        this.data = this._pois.filter((r: SessionPoiJsonapiResource) => r.participants.findIndex((p: SessionPoiParticipantJsonapiResource) => p.raw.id === this.participant.id) !== -1);
        this.detectChanges();
    }
}
