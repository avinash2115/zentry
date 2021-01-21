import { Injectable, OnDestroy } from '@angular/core';
import { Observable } from 'rxjs/internal/Observable';
import { map, takeUntil } from 'rxjs/operators';
import firstLoadedCollection from '../../shared/operators/first-loaded-collection';
import { ICollection } from '../../../vendor/vp-ngx-jsonapi/interfaces';
import firstLoadedResource from '../../shared/operators/first-loaded-resource';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observer, of } from 'rxjs';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { DataError } from '../../shared/classes/data-error';
import { IAcknowledgeResponse } from '../../shared/interfaces/acknowledge-response.interface';
import { ProviderJsonapiResource, ProviderJsonapiService } from '../../resources/provider/provider.jsonapi.service';

@Injectable()
export class ProviderService implements OnDestroy {
    private entity$: BehaviorSubject<ProviderJsonapiResource | null> = new BehaviorSubject<ProviderJsonapiResource | null>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        public providerJsonapiService: ProviderJsonapiService
    ) {
    }

    get entity(): Observable<ProviderJsonapiResource | null> {
        return this.entity$.asObservable();
    }

    ngOnDestroy(): void {
        this.entity$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<ProviderJsonapiResource>> {
        return this.providerJsonapiService
            .all({
                include: ['*'],
                remotefilter: filterBy,
                sortBy
            })
            .pipe(
                firstLoadedCollection(),
                map((data: ICollection<ProviderJsonapiResource>) => {
                    return data.$toArray;
                })
            );
    }

    get(id: string, includes: Array<string> = ['*']): Observable<ProviderJsonapiResource> {
        return this.providerJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                map((resource: ProviderJsonapiResource) => {
                    this.entity$.next(resource);

                    return resource;
                })
            );
    }

    make(): Observable<ProviderJsonapiResource> {
        return new Observable<ProviderJsonapiResource>((observer: Observer<ProviderJsonapiResource>) => {
            const resource: ProviderJsonapiResource = this.providerJsonapiService.new();

            resource.name = '';

            this.entity$.next(resource);

            observer.next(this.entity$.value);
            observer.complete();
        });
    }

    save(): Observable<ProviderJsonapiResource> {
        if (!this.entity$.value.dirty) {
            return of(this.entity$.value);
        }

        return new Observable<ProviderJsonapiResource>((observer: Observer<ProviderJsonapiResource>) => {
            this.entity$
                .value
                .save({
                    preserveRelationships: true
                })
                .then((response: IDataObject) => {
                    const resource: ProviderJsonapiResource = this.providerJsonapiService.new();

                    Converter.build(response, resource);

                    this.entity$.next(resource);

                    observer.next(this.entity$.value);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
        });
    }

    remove(entity: ProviderJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity
                .customCall({
                    method: 'DELETE'
                })
                .then(({acknowledge}: IAcknowledgeResponse) => {
                    if (this.entity$.value && this.entity$.value.id === entity.id) {
                        this.entity$.next(null);
                    }

                    observer.next(acknowledge);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
        });
    }

    refresh(): void {
        if (this.entity$.value.dirty) {
            this
                .get(this.entity$.value.id)
                .pipe(takeUntil(this.destroy$))
                .subscribe((entity: ProviderJsonapiResource) => {
                    this.entity$.next(entity);
                });
        }
    }
}
