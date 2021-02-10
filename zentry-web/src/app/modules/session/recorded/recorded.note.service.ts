import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { filter, map, switchMap, take } from 'rxjs/operators';
import { RecordedService } from './recorded.service';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';
import { NoteJsonapiResource } from '../../../resources/session/note/note.jsonapi.service';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { DataError } from '../../../shared/classes/data-error';
import { Observer } from 'rxjs/internal/types';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { UploadService } from '../../../shared/services/media/upload.service';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';

@Injectable()
export class RecordedNoteService implements OnDestroy {
    private _entity$: BehaviorSubject<NoteJsonapiResource | null> = new BehaviorSubject<NoteJsonapiResource>(null);

    private _list$: BehaviorSubject<Array<NoteJsonapiResource>> = new BehaviorSubject<Array<NoteJsonapiResource>>([]);

    private _uploadService: UploadService = this._injector.get(UploadService);

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _recordedService: RecordedService
    ) {
        this.reboot().subscribe();
    }

    ngOnDestroy(): void {
        this._entity$.complete();
        this._list$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get entity(): Observable<NoteJsonapiResource | null> {
        return this._entity$.asObservable();
    }

    get entityLoaded(): Observable<NoteJsonapiResource> {
        return this.entity.pipe(filter((resource: NoteJsonapiResource | null) => resource instanceof NoteJsonapiResource), take(1));
    }

    get list(): Observable<Array<NoteJsonapiResource>> {
        return this._list$.asObservable().pipe(map((value: Array<NoteJsonapiResource>) => value.sort((a: NoteJsonapiResource, b: NoteJsonapiResource) => (new Date(b.createdAt)).getTime() - (new Date(a.createdAt).getTime()))));
    }

    direct(entity: NoteJsonapiResource): Observable<NoteJsonapiResource> {
        this._entity$.next(entity);

        return of(this._entity$.value);
    }

    save(): Observable<NoteJsonapiResource> {
        return new Observable<NoteJsonapiResource>((observer: Observer<NoteJsonapiResource>) => {
            this._entity$
                .value
                .save()
                .then(() => {
                    const currentValue: Array<NoteJsonapiResource> = this._list$.value;
                    const currentIndex: number = currentValue.findIndex((r: NoteJsonapiResource) => r.id === this._entity$.value.id);

                    if (currentIndex !== -1) {
                        currentValue[currentIndex] = this._entity$.value;
                        this._list$.next(currentValue);
                    }

                    observer.next(this._entity$.value);
                    observer.complete();
                }, (error: DataError) => {
                    observer.error(error);
                });
        });
    }

    add(value: string, participant?: UserParticipantJsonapiResource): Observable<NoteJsonapiResource> {
        return this._recordedService
            .entityLoaded
            .pipe(
                switchMap((session: SessionJsonapiResource) => {
                    return new Observable<NoteJsonapiResource>((observer: Observer<NoteJsonapiResource>) => {
                        const resource: NoteJsonapiResource = this._recordedService.sessionService.sessionNoteJsonapiService.new();
                        resource.text = value;

                        if (participant instanceof UserParticipantJsonapiResource) {
                            resource.addRelationship(participant, 'participant');
                        }

                        resource.customCall({
                            method: 'POST',
                            params: {
                                beforepath: `${session.path}/relationships`,
                                preserveRelationships: true
                            }
                        }).then((result: IDataObject) => {
                            const resultResource: NoteJsonapiResource = this._recordedService.sessionService.sessionNoteJsonapiService.new();

                            Converter.build(result, resultResource);

                            const currentValue: Array<NoteJsonapiResource> = this._list$.value;

                            if (currentValue.findIndex((r: NoteJsonapiResource) => r.id === resultResource.id) === -1) {
                                currentValue.push(resultResource);
                                this._list$.next(currentValue);
                            }

                            observer.next(resultResource);
                            observer.complete();
                        }, (error: DataError) => observer.error(error));
                    });
                })
            );
    }

    upload(value: File, participant?: UserParticipantJsonapiResource): Observable<number | NoteJsonapiResource> {
        return this._recordedService
            .entityLoaded
            .pipe(
                switchMap((session: SessionJsonapiResource) => {
                    return new Observable<number | NoteJsonapiResource>((observer: Observer<number | NoteJsonapiResource>) => {
                        this._uploadService
                            .upload(
                                `${session.path}/relationships/${this._recordedService.sessionService.sessionNoteJsonapiService.path}${participant instanceof UserParticipantJsonapiResource ? '?participant_id=' + participant.id: ''}`,
                                [value]
                            )
                            .subscribe((result: number | Array<IDataObject>) => {
                                if (Array.isArray(result)) {
                                    const resultResource: NoteJsonapiResource = this._recordedService.sessionService.sessionNoteJsonapiService.new();

                                    Converter.build(result[0], resultResource);

                                    const currentValue: Array<NoteJsonapiResource> = this._list$.value;

                                    if (currentValue.findIndex((r: NoteJsonapiResource) => r.id === resultResource.id) === -1) {
                                        currentValue.push(resultResource);
                                        this._list$.next(currentValue);
                                    }

                                    observer.next(resultResource);
                                    observer.complete();
                                } else {
                                    observer.next(result);
                                }
                            }, (error: DataError) => observer.error(error));
                    });
                })
            );
    }

    remove(entity: NoteJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity.customCall({
                method: 'DELETE'
            }).then(() => {
                const currentValue: Array<NoteJsonapiResource> = this._list$.value;
                const currentIndex: number = currentValue.findIndex((r: NoteJsonapiResource) => r.id === entity.id);

                if (currentIndex !== -1) {
                    currentValue.splice(currentIndex, 1);
                    this._list$.next(currentValue);
                }

                observer.next(true);
                observer.complete();
            }, (error: DataError) => {
                observer.error(error);
            });
        });
    }


    reboot(soft: boolean = false): Observable<Array<NoteJsonapiResource>> {
        return this._recordedService
            .entityLoaded
            .pipe(
                switchMap((session: SessionJsonapiResource) => {
                    if (!soft) {
                        this._entity$.next(null);
                    }

                    this._list$.next(session.notes);

                    return of(this._list$.value);
                })
            );
    }
}
