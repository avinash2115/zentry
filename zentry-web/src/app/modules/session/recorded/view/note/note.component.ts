import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, SimpleChanges } from '@angular/core';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { RecordedService } from '../../recorded.service';
import { takeUntil } from 'rxjs/operators';
import { SessionJsonapiResource } from '../../../../../resources/session/session.jsonapi.service';
import { NoteJsonapiResource } from '../../../../../resources/session/note/note.jsonapi.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';
import { SwalService } from '../../../../../shared/services/swal.service';
import { DataError } from '../../../../../shared/classes/data-error';
import { LoaderService } from '../../../../../shared/services/loader.service';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../../../resources/user/participant/participant.jsonapi.service';

@Component({
    selector: 'app-session-recorded-view-note',
    templateUrl: './note.component.html',
    styleUrls: ['./note.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class NoteComponent extends BaseDetachedComponent implements OnInit {
    @Input() participant: UserParticipantJsonapiResource | null;
    @Input() recordedEntity: SessionJsonapiResource | null;

    public data: Array<NoteJsonapiResource> = [];

    public texting: boolean = false;
    public uploading: boolean = false;
    public uploadingProgress: number = 0;

    public editing: NoteJsonapiResource | null = null;

    private _notes: Array<NoteJsonapiResource> = [];

    constructor(
        protected cdr: ChangeDetectorRef,
        protected loaderService: LoaderService,
        protected _recordedService: RecordedService
    ) {
        super(cdr);
    }

    get recordedService(): RecordedService {
        return this._recordedService;
    }

    ngOnInit(): void {
        this.loadingTrigger();

            this.recordedService.noteService.list
            .pipe(takeUntil(this._destroy$))
            .subscribe((data: Array<NoteJsonapiResource>) => {
                this._notes = data;

                this.init();

                this.loadingCompleted();
            });
    }

    ngOnChanges(changes: SimpleChanges): void {
        this.init();
    }

    text(): void {
        this.cancel();

        this.texting = true;
        this.detectChanges();
    }

    create(value: string): void {
        if (this.texting) {
            this.loaderService.show();

            this.recordedService
                .noteService
                .add(value, this.participant)
                .subscribe(() => {
                    this.loaderService.hide();
                    SwalService.toastSuccess({title: 'Note has been created!'});
                    this.cancel();
                }, (error: DataError) => {
                    this.loaderService.hide();
                    this.cancel();
                    this.fallback(error);
                });
        }
    }

    upload(event: Event): void {
        const files: FileList = (event.target as HTMLInputElement).files;

        if (files.length > 0) {
            this.cancel();

            this.uploading = true;
            this.detectChanges();

            this.recordedService
                .noteService
                .upload(files[0], this.participant)
                .subscribe((result: number | NoteJsonapiResource) => {
                    if (result instanceof NoteJsonapiResource) {
                        SwalService.toastSuccess({title: 'Note has been uploaded!'});
                        this.cancel();
                    } else {
                        this.uploadingProgress = result;
                        this.detectChanges();
                    }
                }, (error: DataError) => {
                    this.cancel();
                    this.fallback(error);
                });
        }
    }

    edit(entity: NoteJsonapiResource): void {
        this.cancel();

        this.recordedService
            .noteService
            .direct(entity)
            .subscribe(() => {
                this.editing = entity;
                this.detectChanges();
            });
    }

    isEditing(entity: NoteJsonapiResource): boolean {
        return this.editing instanceof NoteJsonapiResource && this.editing.id === entity.id;
    }

    save(): void {
        this.loaderService.show();

        this.recordedService
            .noteService
            .save()
            .subscribe(() => {
                this.loaderService.hide();
                SwalService.toastSuccess({title: 'Note has been updated!'});
                this.cancel();
            }, (error: DataError) => {
                this.loaderService.hide();
                this.cancel();
                this.fallback(error);
            });
    }

    cancel(): void {
        this.texting = false;
        this.uploading = false;
        this.uploadingProgress = 0;
        this.editing = null;
        this.detectChanges();
    }

    remove(entity: NoteJsonapiResource): void {
        SwalService
            .remove({
                title: `Are you sure?`,
                text: `Note is going to be removed!`
            })
            .then((answer: { value: boolean }) => {
                if (answer.value) {
                    this.loaderService.show();

                    this.recordedService
                        .noteService
                        .remove(entity)
                        .pipe(takeUntil(this._destroy$))
                        .subscribe((result: boolean) => {
                            this.loaderService.hide();

                            if (result) {
                                SwalService.toastSuccess({title: `Note has been removed!`});
                            } else {
                                SwalService
                                    .error({
                                        title: `Note was not removed!`,
                                        text: `Please try to remove it again.`
                                    });
                            }
                        }, (error: DataError) => {
                            this.loaderService.hide();
                            this.fallback(error);
                        });
                }
            });
    }

    private init(): void {
        if (this.participant instanceof UserParticipantJsonapiResource) {
            this.data = this._notes.filter((r: NoteJsonapiResource) => r.participant instanceof UserParticipantJsonapiResource && r.participant.id === this.participant.id);
        } else {
            this.data = this._notes;
        }

        this.detectChanges();
    }
}
