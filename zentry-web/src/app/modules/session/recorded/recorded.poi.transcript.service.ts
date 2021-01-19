import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, forkJoin, Observable, Observer, of, throwError } from 'rxjs';
import { catchError, filter, map, switchMap } from 'rxjs/operators';
import { RecordedService } from './recorded.service';
import { TranscriptJsonapiResource } from '../../../resources/transcript/transcript.jsonapi.service';
import { RecordedPoiService } from './recorded.poi.service';
import firstLoadedCollection from '../../../shared/operators/first-loaded-collection';
import { ICollection } from '../../../../vendor/vp-ngx-jsonapi/interfaces';
import { DataError } from '../../../shared/classes/data-error';
import { Base } from '../../../../vendor/vp-ngx-jsonapi/services/base';
import { IDataCollection } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-collection';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../resources/session/poi/poi.jsonapi.service';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';

export interface ITranscript {
    poi: SessionPoiJsonapiResource,
    transcript: TranscriptJsonapiResource,
}

@Injectable()
export class RecordedPoiTranscriptService implements OnDestroy {
    private _transcripts$: BehaviorSubject<Array<ITranscript>> = new BehaviorSubject<Array<ITranscript>>([]);

    private _loaded: boolean = false;
    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _recordedService: RecordedService,
        private _recordedPoiService: RecordedPoiService,
    ) {
        this._recordedService
            .entityLoaded
            .pipe(
                switchMap((resource: SessionJsonapiResource) => {
                    return new Observable<ICollection<SessionPoiJsonapiResource>>((observer: Observer<ICollection<SessionPoiJsonapiResource>>) => {
                        this._recordedService
                            .sessionService
                            .sessionPoiJsonapiService
                            .all({
                                beforepath: `${resource.path}/relationships`,
                                afterpath: this._recordedService.transcriptJsonapiService.path,
                                include: ['transcript'],
                            }, (data: IDataCollection) => {
                                const collection: ICollection<SessionPoiJsonapiResource> = Base.newCollection();

                                Converter.build(data, collection);

                                observer.next(collection);
                                observer.complete();
                            }, (error: DataError) => observer.error(error))
                    }).pipe(
                        catchError((error: DataError) => {
                            switch (error.status) {
                                case 403:
                                    return forkJoin(resource.pois.map((r: SessionPoiJsonapiResource) => {
                                        return new Observable<ICollection<SessionPoiJsonapiResource>>((observer: Observer<ICollection<SessionPoiJsonapiResource>>) => {
                                            r.customCall({
                                                method: 'GET',
                                                postfixPath: `relationships/${this._recordedService.transcriptJsonapiService.path}`,
                                                params: {
                                                    include: ['transcript']
                                                }
                                            }).then((data: IDataCollection) => {
                                                const collection: ICollection<SessionPoiJsonapiResource> = Base.newCollection();

                                                Converter.build(data, collection);

                                                observer.next(collection);
                                                observer.complete();
                                            }, (error: DataError) => observer.error(error));
                                        });
                                    })).pipe(map((response: Array<ICollection<SessionPoiJsonapiResource>>) => {
                                        const collection: ICollection<SessionPoiJsonapiResource> = Base.newCollection();

                                        response.reduce((result: Array<SessionPoiJsonapiResource>, current: ICollection<SessionPoiJsonapiResource>) => {
                                            return result.concat(current.$length ? current.$toArray : []);
                                        }, []).forEach((r: SessionPoiJsonapiResource) => {
                                            collection[r.id] = r;
                                        });

                                        return collection;
                                    }));
                                default:
                                    console.error(error);
                                    return of(Base.newCollection());
                            }
                        })
                    );
                }),
                firstLoadedCollection()
            )
            .subscribe((data: ICollection<SessionPoiJsonapiResource>) => {
                this._loaded = true;
                const result: Array<SessionPoiJsonapiResource> = data.$length ? data.$toArray : [];

                this._transcripts$.next(result.map((r: SessionPoiJsonapiResource) => {
                    return {
                        poi: r,
                        transcript: r.transcript
                    }
                }));
            });
    }

    ngOnDestroy(): void {
        this._transcripts$.complete();

        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get transcripts(): Observable<Array<ITranscript>> {
        return this._transcripts$.asObservable().pipe(filter(() => this._loaded));
    }

    transcript(entity: SessionPoiJsonapiResource): TranscriptJsonapiResource | null {
        const r: ITranscript | undefined = this._transcripts$.value.find((r: ITranscript) => r.poi.id === entity.id);
        return !!r ? r.transcript : null;
    }
}
