import { Injectable, Injector, OnDestroy } from '@angular/core';
import { Subject } from 'rxjs/internal/Subject';
import { Observable, Observer } from 'rxjs';
import { map, switchMap, take } from 'rxjs/operators';
import { SessionJsonapiResource } from '../../../resources/session/session.jsonapi.service';
import { DataError } from '../../../shared/classes/data-error';
import { IDataObject } from '../../../../vendor/vp-ngx-jsonapi/interfaces/data-object';
import { Converter } from '../../../../vendor/vp-ngx-jsonapi/services/converter';
import { ParticipantJsonapiResource } from '../../../resources/user/participant/participant.jsonapi.service';
import { GoalJsonapiResource as ParticipantGoalJsonapiResource } from '../../../resources/user/participant/goal/goal.jsonapi.service';
import { BehaviorSubject } from 'rxjs/internal/BehaviorSubject';
import { of } from 'rxjs/internal/observable/of';
import { SoapJsonapiResource } from '../../../resources/session/soap/soap.jsonapi.service';
import { IAcknowledgeResponse } from '../../../shared/interfaces/acknowledge-response.interface';
import { HttpClient } from '@angular/common/http';
import { RecordedService } from './recorded.service';

@Injectable()
export class RecordedSoapService implements OnDestroy {
    private _data$: BehaviorSubject<Array<SoapJsonapiResource>> = new BehaviorSubject<Array<SoapJsonapiResource>>([]);
    private _http: HttpClient = this._injector.get(HttpClient);

    private readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    constructor(
        private _injector: Injector,
        private _recordedService: RecordedService
    ) {
        this._recordedService
            .entityLoaded
            .subscribe((resource: SessionJsonapiResource) => this._data$.next(resource.soaps));
    }

    ngOnDestroy(): void {
        this._destroy$.next(true);
        this._destroy$.complete();
    }

    get list(): Observable<Array<SoapJsonapiResource>> {
        return this._data$.asObservable();
    }

    make(): SoapJsonapiResource {
        const entity: SoapJsonapiResource = this._recordedService.sessionService.sessionSoapJsonapiService.new();

        entity.present = true;
        entity.activity = '';
        entity.note = '';
        entity.plan = '';

        return entity;
    }

    add(
        entity: SoapJsonapiResource,
        participant: ParticipantJsonapiResource,
        goal: ParticipantGoalJsonapiResource
    ): Observable<SoapJsonapiResource> {
        entity.addRelationship(participant, 'participant');
        entity.addRelationship(goal, 'goal');

        return this._recordedService
            .entityLoaded
            .pipe(
                take(1),
                switchMap((session: SessionJsonapiResource) => {
                    return new Observable<SoapJsonapiResource>((observer: Observer<SoapJsonapiResource>) => {
                        entity.save({
                            beforepath: `${session.path}/relationships`,
                            preserveRelationships: true
                        }, (response: IDataObject) => {
                            const resource: SoapJsonapiResource = this._recordedService.sessionService.sessionSoapJsonapiService.new();
                            Converter.build(response, resource);

                            const currentValue: Array<SoapJsonapiResource> = this._data$.value;

                            if (currentValue.findIndex((r: SoapJsonapiResource) => r.id === resource.id) === -1) {
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

    bulk(entities: Array<SoapJsonapiResource>): Observable<boolean> {
        return this._recordedService
            .entityLoaded
            .pipe(
                take(1),
                switchMap((session: SessionJsonapiResource) => {
                    return this._http.post<IAcknowledgeResponse>(`${session.path}/relationships/${this._recordedService.sessionService.sessionSoapJsonapiService.path}/bulk`, {
                        data: entities.map((r: SoapJsonapiResource) => r.toObject().data)
                    });
                }),
                map((response: IAcknowledgeResponse) => response.acknowledge)
            );
    }

    save(entity: SoapJsonapiResource): Observable<SoapJsonapiResource> {
        return new Observable<SoapJsonapiResource>((observer: Observer<SoapJsonapiResource>) => {
            entity.save({
                preserveRelationships: true
            }, (response: IDataObject) => {
                const resource: SoapJsonapiResource = this._recordedService.sessionService.sessionSoapJsonapiService.new();
                Converter.build(response, resource);

                const currentValue: Array<SoapJsonapiResource> = this._data$.value;

                if (currentValue.findIndex((r: SoapJsonapiResource) => r.id === resource.id) === -1) {
                    currentValue.push(resource);
                    this._data$.next(currentValue);
                }

                observer.next(resource);
                observer.complete();
            }, (error: DataError) => observer.error(error));
        });
    }

    reboot(): Observable<Array<SoapJsonapiResource>> {
        this._data$.next([]);

        return of([]);
    }
}
