import { Injectable, OnDestroy } from '@angular/core';
import { BehaviorSubject, combineLatest, Observable, of, Subject } from 'rxjs';
import { Observer } from 'rxjs/internal/types';
import { DesktopCapturer } from 'electron';
import { catchError, filter, map, switchMap, take, takeUntil } from 'rxjs/operators';
import DesktopCapturerSource = Electron.DesktopCapturerSource;

@Injectable()
export class MediaService implements OnDestroy {
    private navigator: any;
    private desktopCapturer: DesktopCapturer | undefined | null = null;
    private desktopOptions$: Subject<Array<DesktopCapturerSource>> = new Subject<Array<DesktopCapturerSource>>();
    private desktopSelection$: Subject<DesktopCapturerSource> = new Subject<DesktopCapturerSource>();

    private combinedStream: MediaStream;
    private combinedRecorder: MediaRecorder;
    private combinedRecorderChunk$: BehaviorSubject<Blob> = new BehaviorSubject<Blob>(null);
    private combinedRecorderChunks$: BehaviorSubject<Array<Blob>> = new BehaviorSubject<Array<Blob>>([]);
    private combinedRecorderChunksCache: Array<Blob> = [];

    private active$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);

    private audioSelected: boolean = true;
    private desktopSelected: boolean = true;

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor() {
        this.navigator = navigator;

        if ((window as any).require) {
            try {
                this.desktopCapturer = (window as any).require('electron').desktopCapturer;
            } catch (e) {
                throw e;
            }
        } else {
            console.warn('Electron IPC was not loaded');
        }
    }

    ngOnDestroy(): void {
        this.desktopOptions$.complete();
        this.desktopSelection$.complete();
        this.combinedRecorderChunk$.complete();
        this.combinedRecorderChunks$.complete();

        this.active$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    get desktopOptions(): Observable<Array<DesktopCapturerSource>> {
        return this.desktopOptions$.asObservable();
    }

    get desktopSelection(): Observable<DesktopCapturerSource | null> {
        return this.desktopSelection$.asObservable();
    }

    get isManualDesktopSelection(): boolean {
        return window.config.native && !!this.desktopCapturer;
    }

    get isAudioActive(): boolean {
        return this.combinedStream instanceof MediaStream && this.combinedStream.getAudioTracks().some((t: MediaStreamTrack) => t.enabled);
    }

    get isDesktopActive(): boolean {
        return this.combinedStream instanceof MediaStream && this.combinedStream.getVideoTracks().some((t: MediaStreamTrack) => t.enabled);
    }

    get combinedRecorderChunk(): Observable<Blob> {
        return this.combinedRecorderChunk$.asObservable().pipe(filter((v: Blob | null) => v instanceof Blob));
    }

    get combinedRecorderChunks(): Observable<Array<Blob>> {
        return this.combinedRecorderChunks$.asObservable();
    }

    get active(): Observable<boolean> {
        return this.active$.asObservable();
    }

    selectDesktopOption(option: DesktopCapturerSource | null): void {
        this.desktopSelection$.next(option);
        this.desktopOptions$.next([]);
    }

    toggleInitialSelection(audio: boolean = true, desktop: boolean = true): void {
        this.audioSelected = audio;
        this.desktopSelected = desktop;
    }

    toggleAudio(): void {
        if (this.combinedStream instanceof MediaStream) {
            this.combinedStream.getAudioTracks().forEach((t: MediaStreamTrack) => t.enabled = !t.enabled);
        }
    }

    toggleDesktop(): void {
        if (this.combinedStream instanceof MediaStream) {
            this.combinedStream.getVideoTracks().forEach((t: MediaStreamTrack) => t.enabled = !t.enabled);
        }
    }

    initialize(): Observable<MediaStream> {
        return combineLatest([
            this.desktop(),
            this.audio(),
        ])
            .pipe(
                takeUntil(this.destroy$),
                switchMap(([desktopStream, audioStream]: [MediaStream, MediaStream]) => {
                    this.combinedStream = new MediaStream([...desktopStream.getTracks(), ...audioStream.getTracks()]);

                    this.combinedRecorder = new MediaRecorder(this.combinedStream, {
                        mimeType: 'video/webm;codecs=vp9,opus'
                    });

                    this.combinedRecorder.ondataavailable = (event: BlobEvent) => {
                        if (event.data && event.data.size > 0) {
                            this.combinedRecorderChunk$.next(event.data);

                            const current: Array<Blob> = this.combinedRecorderChunks$.getValue();
                            current.push(event.data);

                            this.combinedRecorderChunks$.next(current);
                        }
                    };

                    if (!this.audioSelected) {
                        this.toggleAudio();
                    }

                    if (!this.desktopSelected) {
                        this.toggleDesktop();
                    }

                    this.active$.next(true);

                    return of(this.combinedStream);
                })
            );
    }

    start(): void {
        if (this.combinedRecorder instanceof MediaRecorder) {
            this.combinedRecorder.start(10000);
        }
    }

    stop(): Observable<boolean> {
        if (!(this.combinedRecorder instanceof MediaRecorder)) {
            if (this.combinedStream instanceof MediaStream) {
                this.combinedStream.getTracks().forEach((track: MediaStreamTrack) => {
                    track.stop();
                });

                this.combinedStream = null;
            }

            return of(true);
        }

        const callback = () => {
            this.combinedRecorder.stream.getTracks().forEach((track: MediaStreamTrack) => {
                track.stop();
            });

            this.combinedStream.getTracks().forEach((track: MediaStreamTrack) => {
                track.stop();
            });

            this.combinedStream = null;
        }

        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.combinedRecorder.onstop = () => {
                callback();

                observer.next(true);
                observer.complete();
            }

            this.combinedRecorder.onerror = () => {
                callback();

                observer.next(true);
                observer.complete();
            }

            this.combinedRecorder.stop();
        }).pipe(
            map((value: boolean) => {
                this.combinedRecorderChunksCache = this.combinedRecorderChunks$.getValue();

                this.combinedRecorderChunk$.next(null);
                this.combinedRecorderChunk$.complete();
                this.combinedRecorderChunk$ = new BehaviorSubject<Blob>(null);

                this.combinedRecorderChunks$.next([]);
                this.combinedRecorderChunks$.complete();
                this.combinedRecorderChunks$ = new BehaviorSubject<Array<Blob>>([]);

                this.active$.next(false);

                return value;
            }),
            catchError(() => {
                callback();

                this.active$.next(false);

                return of(true);
            })
        );
    }

    export(): Observable<Blob> {
        return this
            .stop()
            .pipe(
                switchMap(() => {
                    const result: Blob = new Blob(this.combinedRecorderChunksCache, {type: 'video/webm;codecs=vp9,opus'})

                    this.combinedRecorderChunksCache = [];

                    return of(result);
                })
            );
    }

    exportChunks(clean: boolean = false): Array<Blob> {
        const result: Array<Blob> = this.combinedRecorderChunksCache;

        if (clean) {
            this.combinedRecorderChunksCache = [];
        }

        return result;
    }

    private desktop(): Observable<MediaStream> {
        return new Observable((observer: Observer<MediaStream>) => {
            const closure: Function = (stream: MediaStream) => {
                observer.next(stream);
                observer.complete;
            };

            if (this.isManualDesktopSelection) {
                this.desktopCapturer
                    .getSources({types: ['screen']})
                    .then((sources: Array<DesktopCapturerSource>) => {
                        this.desktopOptions$.next(sources);
                    }, (error: Error) => {
                        observer.error(error);
                    });

                this.desktopSelection
                    .pipe(take(1))
                    .subscribe((selection: DesktopCapturerSource | null) => {
                        if (selection === null) {
                            observer.error(new Error('Screen was not selected'));
                        } else {
                            this.navigator
                                .mediaDevices
                                .getUserMedia({
                                    video: {
                                        mandatory: {
                                            chromeMediaSource: 'screen',
                                            chromeMediaSourceId: selection.id,
                                            maxWidth: 1920,
                                            maxHeight: 1080,
                                        }
                                    }
                                })
                                .then(closure, (error: Error) => {
                                    observer.error(error);
                                });
                        }
                    });
            } else {
                this.navigator
                    .mediaDevices
                    .getDisplayMedia({
                        video: {
                            width: {ideal: 1280, max: 1920},
                            height: {ideal: 720, max: 1080},
                        }
                    })
                    .then(closure, (error: Error) => {
                        console.error(error);
                        observer.error(error);
                    });
            }
        });
    }

    private audio(): Observable<MediaStream> {
        return new Observable((observer: Observer<MediaStream>) => {
            this.navigator
                .mediaDevices
                .getUserMedia({
                    video: false,
                    audio: {
                        optional: [{
                            echoCancellation: false
                        }]
                    },
                })
                .then((stream: MediaStream) => {
                    observer.next(stream);
                    observer.complete;
                }, (error: Error) => {
                    console.error(error);
                    observer.error(error);
                });
        });
    }
}
