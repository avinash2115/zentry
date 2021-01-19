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
import {
    ParticipantJsonapiResource,
    ParticipantJsonapiService
} from '../../../resources/user/participant/participant.jsonapi.service';
import { IAcknowledgeResponse } from '../../../shared/interfaces/acknowledge-response.interface';
import { TherapyJsonapiService } from '../../../resources/user/participant/therapy/therapy.jsonapi.service';
import { GoalJsonapiService } from '../../../resources/user/participant/goal/goal.jsonapi.service';
import { IepJsonapiService } from '../../../resources/user/participant/iep/iep.jsonapi.service';
import { TrackerJsonapiService } from '../../../resources/user/participant/goal/tracker/tracker.jsonapi.service';

@Injectable()
export class ParticipantService implements OnDestroy {
    private entity$: BehaviorSubject<ParticipantJsonapiResource | null> = new BehaviorSubject<ParticipantJsonapiResource | null>(null);

    private readonly destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        public participantJsonapiService: ParticipantJsonapiService,
        public participantTherapyJsonapiService: TherapyJsonapiService,
        public participantGoalJsonapiService: GoalJsonapiService,
        public participantIepJsonapiService: IepJsonapiService,
        public participantGoalTrackerJsonapiService: TrackerJsonapiService,
    ) {
    }

    get entity(): Observable<ParticipantJsonapiResource | null> {
        return this.entity$.asObservable();
    }

    ngOnDestroy(): void {
        this.entity$.complete();

        this.destroy$.next(true);
        this.destroy$.complete();
    }

    list(filterBy: object = {}, sortBy: object = {}): Observable<Array<ParticipantJsonapiResource>> {
        return this.participantJsonapiService
            .all({
                include: ['*'],
                remotefilter: filterBy,
                sortBy
            })
            .pipe(
                firstLoadedCollection(),
                map((data: ICollection<ParticipantJsonapiResource>) => {
                    return data.$toArray
                })
            );
    }

    get(id: string, includes: Array<string> = ['*']): Observable<ParticipantJsonapiResource> {
        return this.participantJsonapiService
            .get(id, {include: includes})
            .pipe(
                firstLoadedResource(),
                map((resource: ParticipantJsonapiResource) => {
                    this.entity$.next(resource);

                    return resource;
                })
            );
    }

    make(): Observable<ParticipantJsonapiResource> {
        return new Observable<ParticipantJsonapiResource>((observer: Observer<ParticipantJsonapiResource>) => {
            const resource: ParticipantJsonapiResource = this.participantJsonapiService.new();

            resource.firstName = '';
            resource.lastName = '';
            resource.email = '';

            this.entity$.next(resource);

            observer.next(this.entity$.value);
            observer.complete();
        })
    }

    save(): Observable<ParticipantJsonapiResource> {
        if (!this.entity$.value.dirty) {
            return of(this.entity$.value);
        }

        return new Observable<ParticipantJsonapiResource>((observer: Observer<ParticipantJsonapiResource>) => {
            this.entity$
                .value
                .save({
                    preserveRelationships: true
                })
                .then((response: IDataObject) => {
                    const resource: ParticipantJsonapiResource = this.participantJsonapiService.new();

                    Converter.build(response, resource);

                    this.entity$.next(resource);

                    observer.next(this.entity$.value);
                    observer.complete();
                }, (error: DataError) => observer.error(error));
        });
    }

    remove(entity: ParticipantJsonapiResource): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity
                .customCall({
                    method: 'DELETE',
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
                .subscribe((entity: ParticipantJsonapiResource) => {
                    this.entity$.next(entity);
                });
        }
    }
}
