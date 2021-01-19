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
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { SessionService } from '../../session.service';
import {
    PoiJsonapiResource,
    PoiJsonapiResource as SessionPoiJsonapiResource
} from '../../../../resources/session/poi/poi.jsonapi.service';
import { takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../shared/classes/data-error';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { PerfectScrollbarConfigInterface, PerfectScrollbarDirective } from 'ngx-perfect-scrollbar';
import { LoaderService } from '../../../../shared/services/loader.service';

@Component({
    selector: 'app-session-widget-clip',
    templateUrl: './clip.component.html',
    styleUrls: ['./clip.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ClipComponent extends BaseDetachedComponent implements OnInit {
    @ViewChild('selectedList', {static: false}) public selectedList: PerfectScrollbarDirective;
    @ViewChild('clipName', {static: false}) public clipName: ElementRef;

    @Output() editing: EventEmitter<boolean> = new EventEmitter<boolean>(false);

    public poi: PoiJsonapiResource | null = null;
    public pois: Array<PoiJsonapiResource> = [];

    public poiEditInline: PoiJsonapiResource | null = null;
    public poiEdit: PoiJsonapiResource | null = null;

    public session: SessionJsonapiResource | null;

    public readonly scrollbarConfig: PerfectScrollbarConfigInterface = {
        suppressScrollX: true,
    };

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        private _sessionService: SessionService,
    ) {
        super(cdr);
    }

    get sessionService(): SessionService {
        return this._sessionService;
    }

    get isEditing(): boolean {
        return this.poiEdit instanceof PoiJsonapiResource;
    }

    ngOnInit(): void {
        this.sessionService
            .entity
            .pipe(takeUntil(this._destroy$))
            .subscribe((entity: SessionJsonapiResource | null) => {
                this.session = entity;
                this.detectChanges();
            });

        this.sessionService
            .poiService
            .poi
            .pipe(takeUntil(this._destroy$))
            .subscribe((entity: PoiJsonapiResource) => {
                this.poi = entity;

                this.detectChanges();

                if (this.selectedList) {
                    setTimeout(() => {
                        this.selectedList.ps().update();
                        this.detectChanges();
                    }, 150);
                }
            });

        this.sessionService
            .poiService
            .pois
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<PoiJsonapiResource>) => {
                this.pois = data;

                this.detectChanges();

                if (this.selectedList) {
                    setTimeout(() => {
                        this.selectedList.ps().update();
                        this.detectChanges();
                    }, 150);
                }
            });
    }

    edit(entity: PoiJsonapiResource): void {
        this.poiEdit = entity;
        this.editing.emit(true);
        this.detectChanges();

        setTimeout(() => {
            (this.clipName.nativeElement as HTMLInputElement).focus();

            this.detectChanges();
        });
    }

    save(): void {
        if (!this.poiEdit.is_new) {
            this.loaderService.show();
        }

        this.sessionService
            .poiService
            .edit(!this.poiEdit.is_new ? this.poiEdit : null)
            .subscribe(() => {
                this.loaderService.hide();

                this.poiEdit.clean();

                this.cancel();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    cancel(): void {
        if (!this.poiEdit.is_new && this.poiEdit.dirty) {
            this.poiEdit
                .reloadResource({
                    preserveRelationships: true
                })
                .then(() => {
                    this.poiEdit.clean();
                    this.poiEdit = null;

                    this.editing.emit(false);
                    this.detectChanges();
                }, (error: DataError) => {
                    this.fallback(error);
                });
        } else {
            this.poiEdit = null;
            this.editing.emit(false);
            this.detectChanges();
        }
    }

    remove(entity: SessionPoiJsonapiResource): void {
        entity.readonly = true;

        this.detectChanges();

        this.sessionService
            .poiService
            .remove(entity)
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
                entity.readonly = false;

                this.detectChanges();
            }, (error: DataError) => {
                entity.readonly = false;

                this.fallback(error);
            });
    }

    editingInline(entity: PoiJsonapiResource): void {
        this.poiEditInline = entity;
        this.detectChanges();
    }

    editingInlineCancel(): void {
        const r: SessionPoiJsonapiResource = this.poiEditInline;

        this.poiEditInline = null;
        this.detectChanges();

        this.sessionService
            .poiService
            .edit(!r.is_new ? r : null)
            .subscribe(
                () => {},
                (error: DataError) => this.fallback(error)
            );
    }

    isEditingInline(entity: PoiJsonapiResource): boolean {
        return this.poiEditInline instanceof PoiJsonapiResource && this.poiEditInline.id === entity.id;
    }
}
