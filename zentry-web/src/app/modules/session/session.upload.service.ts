import { Injectable, OnDestroy } from '@angular/core';
import { SessionJsonapiResource } from '../../resources/session/session.jsonapi.service';
import { BehaviorSubject, EMPTY, Observable, Observer, of } from 'rxjs';
import { Subject } from 'rxjs/internal/Subject';
import { UploadService as UploadService } from '../../shared/services/media/upload.service';
import { catchError, delay, expand, filter, flatMap, last, switchMap, take } from 'rxjs/operators';
import { UtilsService } from '../../shared/services/utils.service';
import { DbService } from '../../shared/services/db/db.service';
import Dexie from 'dexie';
import { v4 as uuidv4 } from 'uuid';
import { SessionService } from './session.service';
import { EType } from '../../resources/session/stream/stream.jsonapi.service';

interface ISessionItemChunk {
    blob: Blob
    uploaded: boolean,
}

interface IDBSessionItemChunk {
    id: number;
    identity: string;
    session_id: string;
    chunk: ISessionItemChunk
}

const DBSessionItemChunkTableName: string = 'sessions_items_chunks';

class DBSessionsItemChunkDatabase extends Dexie {
    public sessions: Dexie.Table<IDBSessionItemChunk, number>;

    public constructor() {
        super('DBSessionsItemChunkDatabase');

        this.version(1).stores({
            [DBSessionItemChunkTableName]: '++id,identity,session_id,chunk'
        });

        this.sessions = this.table(DBSessionItemChunkTableName);
    }
}

@Injectable()
export class SessionUploadService implements OnDestroy {
    private _current$: BehaviorSubject<SessionJsonapiResource | null> = new BehaviorSubject<SessionJsonapiResource>(null);
    private _currentChunk$: BehaviorSubject<IDBSessionItemChunk | null> = new BehaviorSubject<IDBSessionItemChunk | null>(null);
    private _currentProgress$: BehaviorSubject<number> = new BehaviorSubject<number>(0);
    private _list$: BehaviorSubject<Array<SessionJsonapiResource>> = new BehaviorSubject<Array<SessionJsonapiResource>>([]);
    private _paused$: BehaviorSubject<Array<SessionJsonapiResource>> = new BehaviorSubject<Array<SessionJsonapiResource>>([]);

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        protected dbService: DbService,
        protected uploadService: UploadService,
        protected sessionService: SessionService,
    ) {
        this.init();

        this.dbService.connect(new DBSessionsItemChunkDatabase());
    }

    get current(): Observable<SessionJsonapiResource | null> {
        return this._current$.asObservable();
    }

    get currentChunk(): Observable<IDBSessionItemChunk | null> {
        return this._currentChunk$.asObservable();
    }

    get currentProgress(): Observable<number> {
        return this._currentProgress$.asObservable();
    }

    get list(): Observable<Array<SessionJsonapiResource>> {
        return this._list$.asObservable();
    }

    get paused(): Observable<Array<SessionJsonapiResource>> {
        return this._paused$.asObservable();
    }

    ngOnDestroy(): void {
        this._current$.complete();
        this._currentChunk$.complete();
        this._currentProgress$.complete();
        this._list$.complete();
        this._paused$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    register(session: SessionJsonapiResource, chunks: Array<Blob>): void {
        this.listUpdate(session, false, false);

        if (!(this._current$.value instanceof SessionJsonapiResource)) {
            this._current$.next(session);
        } else {
            if (this._paused$.value.findIndex((r: SessionJsonapiResource) => r.id === this._current$.value.id) !== -1) {
                this.pause(session).subscribe();
            }
        }

        chunks.map((b: Blob) => {
            return <ISessionItemChunk>{
                blob: b,
                uploaded: false
            }
        }).forEach((r: ISessionItemChunk) => {
            this.dbService
                .create(DBSessionItemChunkTableName, <IDBSessionItemChunk>{
                    identity: uuidv4(),
                    session_id: session.id,
                    chunk: r
                })
                .subscribe(
                    () => console.info(`${session.id}, chunk registered!`),
                    (error: Error) => console.error(error),
                );
        });
    }

    pause(session: SessionJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            const existingValue: Array<SessionJsonapiResource> = this._paused$.value;
            const existingIndex: number = existingValue.findIndex((r: SessionJsonapiResource) => r.id === session.id);

            if (existingIndex === -1) {
                existingValue.push(session);
                this._paused$.next(existingValue);
            }

            this._currentChunk$
                .asObservable()
                .pipe(
                    filter((value: IDBSessionItemChunk | null) => value === null || value.session_id !== session.id),
                    take(1)
                )
                .subscribe(() => {
                    observer.next(true);
                    observer.complete();
                }, (error: Error) => {
                    observer.error(error);
                });
        });
    }

    resume(session: SessionJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            const existingValue: Array<SessionJsonapiResource> = this._paused$.value;
            const existingIndex: number = existingValue.findIndex((r: SessionJsonapiResource) => r.id === session.id);

            if (existingIndex !== -1) {
                existingValue.splice(existingIndex, 1);
                this._paused$.next(existingValue);
            }

            if (this._currentChunk$.value && this._currentChunk$.value.session_id !== session.id) {
                const dummy: SessionJsonapiResource = this.sessionService.sessionJsonapiService.new();
                dummy.id = this._currentChunk$.value.session_id;

                this.pause(dummy)
                    .pipe(
                        switchMap(() => {
                            this._current$.next(session);

                            return this._currentChunk$.asObservable();
                        }),
                        filter((value: IDBSessionItemChunk | null) => value && value.session_id === session.id),
                        take(1)
                    )
                    .subscribe(() => {
                        observer.next(true);
                        observer.complete();
                    }, (error: Error) => {
                        observer.error(error);
                    });
            } else {
                this._current$.next(session);

                this._currentChunk$
                    .asObservable()
                    .pipe(
                        filter((value: IDBSessionItemChunk | null) => value && value.session_id === session.id),
                        take(1)
                    )
                    .subscribe(() => {
                        observer.next(true);
                        observer.complete();
                    }, (error: Error) => {
                        observer.error(error);
                    });
            }
        });
    }

    restore(): Observable<Array<SessionJsonapiResource>> {
        return new Observable<Array<SessionJsonapiResource>>((observer: Observer<Array<SessionJsonapiResource>>) => {
            this.dbService
                .list(DBSessionItemChunkTableName)
                .pipe(
                    switchMap((items: Array<IDBSessionItemChunk>) => {
                        const identities: Array<string> = items
                            .map((r: IDBSessionItemChunk) => r.session_id)
                            .filter((r: string, i: number, a: Array<string>) => a.indexOf(r) === i);

                        if (identities.length) {
                            return this.sessionService.list({
                                ids: {
                                    collection: identities
                                }
                            }).pipe(
                                switchMap((sessions: Array<SessionJsonapiResource>) => {
                                    const idsRemove: Array<number> = items
                                        .filter((r: IDBSessionItemChunk) => sessions.findIndex((session: SessionJsonapiResource) => session.id === r.session_id) === -1 || sessions.findIndex((session: SessionJsonapiResource) => !session.isFinished && session.id === r.session_id) !== -1)
                                        .map((r: IDBSessionItemChunk) => r.id);

                                    return this
                                        .dbService
                                        .deleteBulk(DBSessionItemChunkTableName, idsRemove)
                                        .pipe(
                                            switchMap(() => of(sessions.filter((r: SessionJsonapiResource) => r.isFinished)))
                                        );
                                })
                            )
                        } else {
                            return of([]);
                        }
                    }),
                )
                .subscribe((items: Array<SessionJsonapiResource>) => {
                    const currentList: Array<SessionJsonapiResource> = this._list$.value;

                    items.forEach((r: SessionJsonapiResource) => {
                        if (currentList.findIndex((s: SessionJsonapiResource) => s.id === r.id) === -1) {
                            currentList.push(r);
                        }
                    });

                    currentList.sort((a: SessionJsonapiResource, b: SessionJsonapiResource) => b.startedAtDate.unix() - a.startedAtDate.unix());

                    this._list$.next(currentList);

                    if (currentList.length > 0) {
                        this._current$.next(currentList[0]);
                    }

                    observer.next(currentList);
                    observer.complete();
                }, (error: Error) => {
                    observer.error(error);
                });
        });
    }

    validate(session: SessionJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.dbService
                .list(DBSessionItemChunkTableName, {
                    session_id: session.id,
                })
                .pipe(
                    expand((data: Array<IDBSessionItemChunk>) => {
                        if (data.filter((r: IDBSessionItemChunk) => !r.chunk.uploaded).length) {
                            return this.dbService.list(DBSessionItemChunkTableName, {session_id: session.id}).pipe(delay(2000));
                        } else {
                            return EMPTY;
                        }
                    }),
                    last()
                )
                .subscribe(
                    () => {
                        this.sessionService
                            .merge(session, EType.combined)
                            .pipe(
                                switchMap(() => this.sessionService.wrap(session))
                            )
                            .subscribe(
                                () => {
                                    console.info(`Session ${session.id} merged and wrapped`);

                                    observer.next(true);
                                    observer.complete();
                                },
                                (error: Error) => observer.error(error)
                            )
                    },
                    (error: Error) => observer.error(error)
                );
        });
    }

    listUpdate(session: SessionJsonapiResource, splice: boolean = false, withCurrent: boolean = true): void {
        const existingValue: Array<SessionJsonapiResource> = this._list$.value;
        const existingIndex: number = existingValue.findIndex((r: SessionJsonapiResource) => r.id === session.id);

        if (splice) {
            if (existingIndex !== -1) {
                existingValue.splice(existingIndex, 1);
            }
        } else {
            if (existingIndex !== -1) {
                existingValue[existingIndex] = session;
            } else {
                existingValue.push(session);
            }
        }

        existingValue.sort((a: SessionJsonapiResource, b: SessionJsonapiResource) => b.startedAtDate.unix() - a.startedAtDate.unix());

        this._list$.next(existingValue);

        if (withCurrent) {
            if (this._current$.value instanceof SessionJsonapiResource && this._current$.value.id === session.id) {
                this._current$.next(session);
            }
        }
    }

    private unprocessed(): Observable<IDBSessionItemChunk | null> {
        if (!(this._current$.value instanceof SessionJsonapiResource) || this._paused$.value.findIndex((r: SessionJsonapiResource) => r.id === this._current$.value.id) !== -1) {
            return of(null);
        }

        return this.dbService
            .list(DBSessionItemChunkTableName, {
                session_id: this._current$.value.id
            })
            .pipe(
                switchMap((data: Array<IDBSessionItemChunk>) => {
                    data = data.filter((r: IDBSessionItemChunk) => !r.chunk.uploaded)
                        .sort((a: IDBSessionItemChunk, b: IDBSessionItemChunk) => a.id - b.id);

                    if (data.length > 0) {
                        return of(data[0]);
                    }

                    return of(null);
                })
            );
    }

    private init(): void {
        this.current
            .pipe(
                filter((value: SessionJsonapiResource | null) => value instanceof SessionJsonapiResource)
            )
            .subscribe((value: SessionJsonapiResource) => {
                this.listUpdate(value, false, false);

                this._currentProgress$.next(0);

                this.dbService
                    .list(DBSessionItemChunkTableName, {session_id: value.id})
                    .subscribe(
                        (data: Array<IDBSessionItemChunk>) => this._currentProgress$.next(data.filter((c: IDBSessionItemChunk) => c.chunk.uploaded).length / data.length * 100),
                        (error: Error) => console.error(error)
                    );

                if (value.isEnded) {
                    this.validate(value)
                        .pipe(
                            switchMap(() => this.dbService.list(DBSessionItemChunkTableName, {session_id: value.id})),
                            switchMap((data: Array<IDBSessionItemChunk>) => this.dbService.deleteBulk(DBSessionItemChunkTableName, data.map((r: IDBSessionItemChunk) => r.id)))
                        )
                        .subscribe(
                            () => {
                                this.listUpdate(value, true, false);

                                this._current$.next(null);
                                this._currentProgress$.next(0);
                            },
                            (error: Error) => console.error(error)
                        );
                }
            });

        this.currentChunk
            .pipe(
                filter((value: IDBSessionItemChunk | null) => value === null),
                delay(2000),
                switchMap(() => this.unprocessed()),
            )
            .subscribe((value: IDBSessionItemChunk | null) => this._currentChunk$.next(value));

        this.currentChunk
            .pipe(
                filter((value: IDBSessionItemChunk | null) => value !== null),
                flatMap((value: IDBSessionItemChunk) => {
                    return this.uploadService
                        .upload(
                            `${window.endpoints.api}${this.sessionService.sessionJsonapiService.path}/${value.session_id}/relationships/streams/partial/upload/combined`,
                            [UtilsService.blobToFile(value.chunk.blob, 'combined.webm')],
                            true
                        )
                        .pipe(
                            last(),
                            switchMap(() => of({
                                chunk: value,
                                retry: false,
                            })),
                            catchError(() => of({
                                chunk: value,
                                retry: true,
                            }))
                        );
                })
            )
            .subscribe((result: { chunk: IDBSessionItemChunk, retry: boolean }) => {
                if (result.retry) {
                    const reader: FileReader = new FileReader();

                    reader.onload = () => {
                        if ((reader.result as ArrayBuffer).byteLength === result.chunk.chunk.blob.size) {
                            setTimeout(() => this._currentChunk$.next(result.chunk), 3000);
                        } else {
                            console.error(reader.error);
                            console.error('Reader found broken blob after load!');

                            this.dbService
                                .delete(DBSessionItemChunkTableName, result.chunk.identity)
                                .subscribe(
                                    () => this._currentChunk$.next(null),
                                    (error: Error) => {
                                        this._currentChunk$.next(null);
                                        console.error(error);
                                    }
                                );
                        }
                    };

                    reader.onerror = () => {
                        console.error(reader.error);
                        console.error('Reader found broken blob!');

                        this.dbService
                            .delete(DBSessionItemChunkTableName, result.chunk.identity)
                            .subscribe(
                                () => this._currentChunk$.next(null),
                                (error: Error) => {
                                    this._currentChunk$.next(null);
                                    console.error(error);
                                }
                            );
                    };

                    reader.readAsArrayBuffer(result.chunk.chunk.blob);
                } else {
                    result.chunk.chunk.uploaded = true;

                    this.dbService
                        .update(DBSessionItemChunkTableName, result.chunk)
                        .pipe(
                            switchMap(() => this.dbService.list(DBSessionItemChunkTableName, {session_id: this._current$.value.id}))
                        )
                        .subscribe(
                            (data: Array<IDBSessionItemChunk>) => {
                                this._currentProgress$.next(data.filter((c: IDBSessionItemChunk) => c.chunk.uploaded).length / data.length * 100);
                                this._currentChunk$.next(null)
                            },
                            (error: Error) => {
                                this._currentChunk$.next(null);
                                console.error(error);
                            }
                        );
                }
            });
    }
}
