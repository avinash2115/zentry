import { Injectable, OnDestroy } from '@angular/core';
import { BehaviorSubject, Observable, Observer, Subject } from 'rxjs';
import { HeaderService } from '../header.service';
import Resumable from 'resumablejs';
import { SessionService } from '../session.service';
import { finalize, first, map, switchMap, take } from 'rxjs/operators';
import ResumableFile = Resumable.ResumableFile;

interface IQueueItem {
    id: string,
    observable: Observable<number>,
}

@Injectable()
export class UploadService implements OnDestroy {
    private resumable: Resumable;

    private queue: Array<IQueueItem> = [];
    private current$: BehaviorSubject<IQueueItem> = new BehaviorSubject<IQueueItem>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private headerService: HeaderService,
        private sessionService: SessionService
    ) {
    }

    get uploading(): Observable<boolean> {
        return this.current$.asObservable().pipe(map((v: IQueueItem) => v !== null));
    }

    ngOnDestroy(): void {
        this.current$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    upload(url: string, files: Array<File>, test: boolean = false): Observable<number> {
        return this.fire(url, files, test);
    }

    private fire(url: string, files: Array<File>, test: boolean = false): Observable<number> {
        const observable: Observable<number> = new Observable<number>((observer: Observer<number>) => {
            const opts: any = {
                simultaneousUploads: 1,
                maxFiles: 5,
                testChunks: test,
                forceChunkSize: true,
                chunkSize: 1024 * 1024,
                maxChunkRetries: 1,
                headers: this.headerService.uploadHeaders(),
            }

            this.resumable = new Resumable(opts);

            this.resumable.on('filesAdded', () => {
                this.resumable.upload();
            });

            this.resumable.on('pause', () => {
                this.sessionService
                    .uncontrollable()
                    .subscribe(() => {
                        this.resumable.opts.headers = this.headerService.uploadHeaders();

                        setTimeout(() => {
                            this.resumable.upload();
                        });
                    });
            });

            this.resumable.on('progress', () => {
                if (this.resumable.progress() * 100 !== 100) {
                    observer.next(this.resumable.progress() * 100);
                }

                if (this.sessionService.isRefreshNeeded()) {
                    this.resumable.pause();
                }
            });

            this.resumable.on('complete', () => {
                if (!observer.closed) {
                    observer.next(100);
                    observer.complete();
                }
            });

            this.resumable.on('error', (message: string) => {
                observer.error(new Error(message));
            });

            this.resumable.opts.target = url;
            this.resumable.addFiles(files);
        });

        const unique: string = Math.floor(Math.random() * Number.MAX_VALUE).toString();

        this.queue.push({
            id: unique,
            observable: observable,
        });

        if (!this.current$.getValue()) {
            this.current$.next(this.queue.shift());
        }

        return this
            .current$
            .pipe(
                first((current: IQueueItem | null) => current !== null && current.id === unique),
                switchMap((current: IQueueItem) => {
                    return current.observable;
                }),
                finalize(() => {
                    if (this.queue.length) {
                        this.current$.next(this.queue.shift());
                    } else {
                        this.current$.next(null);
                    }
                })
            );
    }
}
