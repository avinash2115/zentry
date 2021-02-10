import { ChangeDetectionStrategy, ChangeDetectorRef, Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../../resources/user/participant/participant.jsonapi.service';
import { RecordedService } from '../../../recorded.service';
import { LoaderService } from '../../../../../../shared/services/loader.service';
import { BaseDetachedComponent } from '../../../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionJsonapiResource } from '../../../../../../resources/session/session.jsonapi.service';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../../../../resources/session/poi/poi.jsonapi.service';

enum EView {
    soap,
    goals,
    clips,
    notes
}

@Component({
    selector: 'app-session-recorded-participant-view',
    templateUrl: './view.component.html',
    styleUrls: ['./view.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ViewComponent extends BaseDetachedComponent implements OnInit {
    @Output() sourceSeeked: EventEmitter<number> = new EventEmitter<number>();
    @Output() sourceReplaced: EventEmitter<string> = new EventEmitter<string>();
    @Output() shared: EventEmitter<SessionPoiJsonapiResource> = new EventEmitter<SessionPoiJsonapiResource>();

    public recordedEntity: SessionJsonapiResource | null;
    public entity: UserParticipantJsonapiResource | null = null;

    public readonly views: typeof EView = EView;

    public viewActive: EView = EView.soap;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected _recordedService: RecordedService
    ) {
        super(cdr);

        cdr.detach();
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    ngOnInit(): void {
        this.recordedService
            .entity
            .subscribe((entity: SessionJsonapiResource | null) => {
                this.recordedEntity = entity;
                this.detectChanges();
            });

        this.recordedService
            .participantService
            .entity
            .subscribe((entity: UserParticipantJsonapiResource) => {
                this.entity = entity;
                this.detectChanges();
            });

        this.detectChanges();
    }

    activate(value: EView): void {
        this.viewActive = value;
        this.detectChanges();
    }

    isActive(value: EView): boolean {
        return this.viewActive === value;
    }

    onClipSeeked(time: number): void {
        this.sourceSeeked.emit(time);
    }

    onClipReplaced(url: string): void {
        this.sourceReplaced.emit(url);
    }

    onClipShared(entity: SessionPoiJsonapiResource): void {
        this.shared.emit(entity);
    }
}
