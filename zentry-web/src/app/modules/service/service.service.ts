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
import { ServiceJsonapiResource, ServiceJsonapiService } from '../../resources/service/service.jsonapi.service';

@Injectable()
export class ServiceService implements OnDestroy {
    private entity$: BehaviorSubject<ServiceJsonapiResource | null> = new BehaviorSubject<ServiceJsonapiResource | null>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        public serviceJsonapiService: ServiceJsonapiService
    ) {
    }

    get entity(): Observable<ServiceJsonapiResource | null> {
        return this.entity$.asObservable();
    }

    ngOnDestroy(): void {
        this.entity$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<ServiceJsonapiResource>> {
        return this.serviceJsonapiService
            .all({
                include: ['*'],
                remotefilter: filterBy,
                sortBy
            })
            .pipe(
                firstLoadedCollection(),
                map((data: ICollection<ServiceJsonapiResource>) => {
                    return data.$toArray;
                })
            );
    }

    get(id: string, includes: Array<string> = ['*']): Observable<ServiceJsonapiResource> {
        return this.serviceJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                map((resource: ServiceJsonapiResource) => {
                    this.entity$.next(resource);

                    return resource;
                })
            );
    }

    make(): Observable<ServiceJsonapiResource> {
        return new Observable<ServiceJsonapiResource>((observer: Observer<ServiceJsonapiResource>) => {
            const resource: ServiceJsonapiResource = this.serviceJsonapiService.new();

            resource.name = '';

            this.entity$.next(resource);

            observer.next(this.entity$.value);
            observer.complete();
        });
    }

    save(): Observable<ServiceJsonapiResource> {
        if (!this.entity$.value.dirty) {
            return of(this.entity$.value);
        }

        return new Observable<ServiceJsonapiResource>((observer: Observer<ServiceJsonapiResource>) => {
            this.entity$
                .value
                .save({
                    preserveRelationships: true
                })
                .then((response: IDataObject) => {
                    const resource: ServiceJsonapiResource = this.serviceJsonapiService.new();

                    Converter.build(response, resource);

                    this.entity$.next(resource);

                    observer.next(this.entity$.value);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
        });
    }

    remove(entity: ServiceJsonapiResource): Observable<boolean> {
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
                .subscribe((entity: ServiceJsonapiResource) => {
                    this.entity$.next(entity);
                });
        }
    }
}
