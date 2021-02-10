import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { Observable, Observer } from 'rxjs';
import { switchMap, take, takeUntil } from 'rxjs/operators';
import { SessionService } from './session.service';
import { SessionJsonapiResource } from '../../resources/session/session.jsonapi.service';
import { ProgressJsonapiResource } from '../../resources/session/progress/progress.jsonapi.service';
import { DataError } from '../../shared/classes/data-error';
import { IDataObject } from '../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../vendor/vp-ngx-jsonapi/services/converter';
import { ParticipantJsonapiResource } from '../../resources/user/participant/participant.jsonapi.service';
import { GoalJsonapiResource as ParticipantGoalJsonapiResource } from '../../resources/user/participant/goal/goal.jsonapi.service';
import { TrackerJsonapiResource as ParticipantGoalTrackerJsonapiResource } from '../../resources/user/participant/goal/tracker/tracker.jsonapi.service';
import { PoiJsonapiResource } from '../../resources/session/poi/poi.jsonapi.service';
import { BehaviorSubject } from 'rxjs/internal/BehaviorSubject';
import { of } from 'rxjs/internal/observable/of';
import { EProgressAction, IProgress, SessionSubscriptionService } from './session.subscription.service';
import { Subscription } from 'rxjs/internal/Subscription';

@Injectable()
export class SessionProgressService implements OnDestroy {
    private _data$: BehaviorSubject<Array<ProgressJsonapiResource>> = new BehaviorSubject<Array<ProgressJsonapiResource>>([]);

    private _subscriptions: Array<Subscription> = [];

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _sessionService: SessionService,
        private _sessionSubscriptionService: SessionSubscriptionService
    ) {
        this._sessionService
            .entityLoaded
            .subscribe((resource: SessionJsonapiResource) => this._data$.next(resource.progress));
    }

    ngOnDestroy(): void {
        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get list(): Observable<Array<ProgressJsonapiResource>> {
        return this._data$.asObservable();
    }

    add(
        datetime: string,
        participant: ParticipantJsonapiResource,
        goal: ParticipantGoalJsonapiResource,
        tracker: ParticipantGoalTrackerJsonapiResource,
        poi?: PoiJsonapiResource
    ): Observable<ProgressJsonapiResource> {
        const entity: ProgressJsonapiResource = this._sessionService.sessionProgressJsonapiService.new();

        entity.datetime = datetime;
        entity.addRelationship(participant, 'participant');
        entity.addRelationship(goal, 'goal');
        entity.addRelationship(tracker, 'tracker');

        if (poi instanceof PoiJsonapiResource) {
            entity.addRelationship(poi, 'poi');
        }

        return this._sessionService
            .entityLoaded
            .pipe(
                take(1),
                switchMap((session: SessionJsonapiResource) => {
                    return new Observable<ProgressJsonapiResource>((observer: Observer<ProgressJsonapiResource>) => {
                        entity.save({
                            beforepath: `${session.path}/relationships`,
                            preserveRelationships: true
                        }, (response: IDataObject) => {
                            const resource: ProgressJsonapiResource = this._sessionService.sessionProgressJsonapiService.new();
                            Converter.build(response, resource);

                            const currentValue: Array<ProgressJsonapiResource> = this._data$.value;

                            if (currentValue.findIndex((r: ProgressJsonapiResource) => r.id === resource.id) === -1) {
                                currentValue.push(resource);
                                this._data$.next(currentValue);
                            }

                            observer.next(resource);
                            observer.complete();
                        }, (error: DataError) => observer.error(error));
                    });
                })
            );
    }

    undo(): Observable<boolean> {
        let currentValue: Array<ProgressJsonapiResource> = this._data$.value
            .sort((
                a: ProgressJsonapiResource, b: ProgressJsonapiResource
            ) => (new Date(b.datetime)).getTime() - (new Date(a.datetime)).getTime());

        if (currentValue.length === 0) {
            return of(true);
        }

        const entity: ProgressJsonapiResource = currentValue[0];

        return new Observable<boolean>((observer: Observer<boolean>) => {
            entity.customCall({
                method: 'DELETE'
            }, () => {
                currentValue = this._data$.value;

                const currentIndex: number = currentValue.findIndex((r: ProgressJsonapiResource) => r.id === entity.id);

                if (currentIndex !== -1) {
                    currentValue.splice(currentIndex, 1);
                    this._data$.next(currentValue);
                }

                observer.next(true);
                observer.complete();
            }, (error: DataError) => observer.error(error));
        });
    }

    subscribe(): void {
        this._subscriptions
            .push(
                this._sessionSubscriptionService
                    .progress
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((event: IProgress) => {
                        if (event.action === EProgressAction.created) {
                            const currentValue: Array<ProgressJsonapiResource> = this._data$.value;

                            if (currentValue.findIndex((r: ProgressJsonapiResource) => r.id === event.resource.id) === -1) {
                                currentValue.push(event.resource);
                                this._data$.next(currentValue);
                            }
                        }

                        if (event.action === EProgressAction.removed) {
                            const currentValue: Array<ProgressJsonapiResource> = this._data$.value;
                            const currentIndex: number = currentValue.findIndex((r: ProgressJsonapiResource) => r.id === event.resource.id);

                            if (currentIndex !== -1) {
                                currentValue.splice(currentIndex, 1);
                                this._data$.next(currentValue);
                            }
                        }
                    })
            );
    }

    unsubscribe(): void {
        this._subscriptions.forEach((s: Subscription) => s.unsubscribe());
    }

    reboot(): Observable<Array<ProgressJsonapiResource>> {
        this._data$.next([]);

        return of([]);
    }
}
