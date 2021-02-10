import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    ElementRef,
    EventEmitter,
    OnInit,
    Output,
    ViewChild
} from '@angular/core';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { RecordedService } from '../../recorded.service';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../../shared/classes/data-error';
import { LoaderService } from '../../../../../shared/services/loader.service';
import {
    EType as ESessionPoiType,
    PoiJsonapiResource as SessionPoiJsonapiResource
} from '../../../../../resources/session/poi/poi.jsonapi.service';
import { combineLatest } from 'rxjs';
import { ITranscript } from '../../recorded.poi.transcript.service';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';
import { TagInputComponent } from 'ngx-chips';

@Component({
    selector: 'app-session-recorded-view-clip',
    templateUrl: './clip.component.html',
    styleUrls: ['./clip.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ClipComponent extends BaseDetachedComponent implements OnInit {
    @Output() sourceSeeked: EventEmitter<number> = new EventEmitter<number>();
    @Output() sourceReplaced: EventEmitter<string> = new EventEmitter<string>();
    @Output() shared: EventEmitter<SessionPoiJsonapiResource> = new EventEmitter<SessionPoiJsonapiResource>();

    public recordedEntity: SessionJsonapiResource | null;
    public data: Array<SessionPoiJsonapiResource> = [];
    public transcriptVisible: SessionPoiJsonapiResource | null = null;

    public editMode: {
        resource: SessionPoiJsonapiResource | null
        name: boolean,
        tags: boolean
    } = {
        resource: null,
        name: null,
        tags: false
    }

    public removalMode: SessionPoiJsonapiResource | null;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected _recordedService: RecordedService,
    ) {
        super(cdr);

        cdr.detach();
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        combineLatest([
            this.recordedService.entity,
            this.recordedService.poiService.pois,
            this.recordedService.poiService.transcriptService.transcripts,
        ]).pipe(takeUntil(this._destroy$))
            .subscribe(([entity, pois, transcripts]: [SessionJsonapiResource, Array<SessionPoiJsonapiResource>, Array<ITranscript>]) => {
                this.recordedEntity = entity;
                this.data = pois;

                if (this.recordedEntity && !this.recordedEntity.isEntirelyRecorded) {
                    this.seek(this.data[0], false);
                }

                this.loadingCompleted();

                setTimeout(() => this.detectChanges(), 300)
            });
    }

    isType(poi: SessionPoiJsonapiResource, type: ESessionPoiType): boolean {
        return poi.isPoiType(type);
    }

    onEditMode(type: string, poi: SessionPoiJsonapiResource): void {
        setTimeout(() => {
            this.editMode.resource = poi;
            this.editMode[type] = true;

            this.recordedEntity.forceDirty();

            this.detectChanges();

            setTimeout(() => {
                switch (type) {
                    case 'name':
                        (document.getElementById(poi.id + '-name') as HTMLInputElement).setSelectionRange(0, 0);
                        (document.getElementById(poi.id + '-name') as HTMLInputElement).focus();
                        break;
                    case 'tags':
                        (document.getElementById(poi.id + '-tags').querySelectorAll('input')[0] as HTMLInputElement).focus()
                        break;
                }

                this.detectChanges();
            }, 100);

        }, 300);
    }

    offEditMode(mode: string): void {
        this.editMode[mode] = false;

        this.detectChanges();
    }

    isEditMode(type: string, entity: SessionPoiJsonapiResource): boolean {
        return this.editMode.resource instanceof SessionPoiJsonapiResource && this.editMode.resource.id === entity.id && this.editMode[type];
    }

    save(entity: SessionPoiJsonapiResource, mode: string, event?: FocusEvent): void {
        if (event && event.relatedTarget instanceof EventTarget && (event.relatedTarget as HTMLElement).id === 'cancel') {
            return
        }

        setTimeout(() => {
            this.offEditMode(mode);

            this.recordedService
                .poiService
                .edit(entity)
                .pipe(takeUntil(this._destroy$))
                .subscribe(
                    () => {
                    },
                    (error: DataError) => this.fallback(error)
                );
        }, 150);
    }

    cancel(entity: SessionPoiJsonapiResource, mode: string): void {
        entity.attributeRestore(mode);
        this.offEditMode(mode);
    }

    startRemoval(entity: SessionPoiJsonapiResource): void {
        this.removalMode = entity;

        this.detectChanges();
    }

    confirmRemoval(): void {
        this.loaderService.show();

        this.recordedService
            .poiService
            .remove(this.removalMode)
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                this.loaderService.hide();
                this.cancelRemoval();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    cancelRemoval(): void {
        this.removalMode = null;
    }

    seek(entity: SessionPoiJsonapiResource, autoplay: boolean = true): void {
        if (!this.recordedEntity.isEntirelyRecorded) {
            this.recordedService
                .poiVideoURL(entity)
                .pipe(takeUntil(this._destroy$))
                .subscribe((url: string) => {
                    this.sourceReplaced.emit(url);

                    if (autoplay) {
                        setTimeout(() => this.sourceSeeked.emit(0), 200);
                    }
                }, (error: DataError) => {
                    this.fallback(error);
                });

            return;
        }

        this.sourceSeeked.emit(Number(entity.startedAtDate.diff(this.recordedEntity.startedAtDate, 's')));
    }

    share(entity: SessionPoiJsonapiResource): void {
        this.shared.emit(entity);
    }

    transcriptIsVisible(entity: SessionPoiJsonapiResource): boolean {
        return this.transcriptVisible instanceof SessionPoiJsonapiResource && this.transcriptVisible.id === entity.id;
    }

    transcriptToggle(entity: SessionPoiJsonapiResource): void {
        if (this.transcriptIsVisible(entity)) {
            this.transcriptVisible = null;
        } else {
            this.transcriptVisible = entity;
        }

        this.detectChanges();
    }
}
