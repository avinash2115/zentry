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
import { TeamJsonapiResource, TeamJsonapiService } from '../../../resources/user/team/team.jsonapi.service';
import { IAcknowledgeResponse } from '../../../shared/interfaces/acknowledge-response.interface';
import { SchoolService } from './school.service';

@Injectable()
export class TeamService implements OnDestroy {
    private entity$: BehaviorSubject<TeamJsonapiResource | null> = new BehaviorSubject<TeamJsonapiResource | null>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        public teamJsonapiService: TeamJsonapiService,
        public schoolService: SchoolService
    ) {
    }

    get entity(): Observable<TeamJsonapiResource | null> {
        return this.entity$.asObservable();
    }

    ngOnDestroy(): void {
        this.entity$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<TeamJsonapiResource>> {
        return this.teamJsonapiService
            .all({
                include: ['*'],
                remotefilter: filterBy,
                sortBy
            })
            .pipe(
                firstLoadedCollection(),
                map((data: ICollection<TeamJsonapiResource>) => {
                    return data.$toArray;
                })
            );
    }


    get(id: string, includes: Array<string> = ['*']): Observable<TeamJsonapiResource> {
        return this.teamJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                map((resource: TeamJsonapiResource) => {
                    this.entity$.next(resource);

                    return resource;
                })
            );
    }

    make(): Observable<TeamJsonapiResource> {
        return new Observable<TeamJsonapiResource>((observer: Observer<TeamJsonapiResource>) => {
            const resource: TeamJsonapiResource = this.teamJsonapiService.new();

            resource.name = '';
            resource.description = '';

            this.entity$.next(resource);

            observer.next(this.entity$.value);
            observer.complete();
        });
    }

    save(): Observable<TeamJsonapiResource> {
        if (!this.entity$.value.dirty) {
            return of(this.entity$.value);
        }

        return new Observable<TeamJsonapiResource>((observer: Observer<TeamJsonapiResource>) => {
            this.entity$
                .value
                .save({
                    preserveRelationships: true
                })
                .then((response: IDataObject) => {
                    const resource: TeamJsonapiResource = this.teamJsonapiService.new();

                    Converter.build(response, resource);

                    this.entity$.next(resource);

                    observer.next(this.entity$.value);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
        });
    }

    remove(entity: TeamJsonapiResource): Observable<boolean> {
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
                .subscribe((entity: TeamJsonapiResource) => {
                    this.entity$.next(entity);
                });
        }
    }
}
