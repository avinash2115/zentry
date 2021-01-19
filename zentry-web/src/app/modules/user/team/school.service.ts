import { Injectable, OnDestroy } from '@angular/core';
import { Observable } from 'rxjs/internal/Observable';
import { map, takeUntil } from 'rxjs/operators';
import firstLoadedCollection from '../../../shared/operators/first-loaded-collection';
import { ICollection } from '../../../../vendor/vp-ngx-jsonapi/interfaces';
import firstLoadedResource from '../../../shared/operators/first-loaded-resource';
import { Subject } from 'rxjs/internal/Subject';
import { BehaviorSubject, Observer, of } from 'rxjs';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { DataError } from '../../../shared/classes/data-error';
import { IAcknowledgeResponse } from '../../../shared/interfaces/acknowledge-response.interface';
import { SchoolJsonapiResource, SchoolJsonapiService } from '../../../resources/user/team/school/school.jsonapi.service';
import { IParams } from '../../../../vendor/vp-ngx-jsonapi/interfaces/params';

@Injectable()
export class SchoolService implements OnDestroy {
    private entity$: BehaviorSubject<SchoolJsonapiResource | null> = new BehaviorSubject<SchoolJsonapiResource | null>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        public schoolJsonapiService: SchoolJsonapiService
    ) {
    }

    get entity(): Observable<SchoolJsonapiResource | null> {
        return this.entity$.asObservable();
    }

    ngOnDestroy(): void {
        this.entity$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<SchoolJsonapiResource>> {
        return this.schoolJsonapiService
            .all({
                include: ['*'],
                remotefilter: filterBy,
                sortBy
            })
            .pipe(
                firstLoadedCollection(),
                map((data: ICollection<SchoolJsonapiResource>) => {
                    return data.$toArray;
                })
            );
    }


    get(id: string, includes: Array<string> = ['*'], params: IParams = {}): Observable<SchoolJsonapiResource> {
        return this.schoolJsonapiService
            .get(id, {
                ...params,
                include: includes
            })
            .pipe(
                firstLoadedResource(),
                map((resource: SchoolJsonapiResource) => {
                    this.entity$.next(resource);

                    return resource;
                })
            );
    }

    make(): Observable<SchoolJsonapiResource> {
        return new Observable<SchoolJsonapiResource>((observer: Observer<SchoolJsonapiResource>) => {
            const resource: SchoolJsonapiResource = this.schoolJsonapiService.new();

            resource.name = '';

            this.entity$.next(resource);

            observer.next(this.entity$.value);
            observer.complete();
        });
    }

    save(): Observable<SchoolJsonapiResource> {
        if (!this.entity$.value.dirty) {
            return of(this.entity$.value);
        }

        return new Observable<SchoolJsonapiResource>((observer: Observer<SchoolJsonapiResource>) => {
            this.entity$
                .value
                .save({
                    preserveRelationships: true
                })
                .then((response: IDataObject) => {
                    const resource: SchoolJsonapiResource = this.schoolJsonapiService.new();

                    Converter.build(response, resource);

                    this.entity$.next(resource);

                    observer.next(this.entity$.value);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
        });
    }

    remove(entity: SchoolJsonapiResource): Observable<boolean> {
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
                .subscribe((entity: SchoolJsonapiResource) => {
                    this.entity$.next(entity);
                });
        }
    }
}
