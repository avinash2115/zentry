import { ISchema, Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import * as moment from 'moment';
import { Moment } from 'moment';
import { SessionJsonapiResource } from '../session.jsonapi.service';
import { ParticipantJsonapiResource } from '../../user/participant/participant.jsonapi.service';
import { GoalJsonapiResource as ParticipantGoalJsonapiResource } from '../../user/participant/goal/goal.jsonapi.service';
import { TrackerJsonapiResource as ParticipantGoalTrackerJsonapiResource } from '../../user/participant/goal/tracker/tracker.jsonapi.service';
import { PoiJsonapiResource } from '../poi/poi.jsonapi.service';

export class ProgressJsonapiService extends Service<ProgressJsonapiResource> {
    type = 'sessions_progress';
    path = 'progress';
    resource = ProgressJsonapiResource;
    schema: ISchema = {
        relationships: {
            participant: {
                hasMany: false,
                alias: 'users_participants'
            },
            goal: {
                hasMany: false,
                alias: 'users_participants_goals'
            },
            tracker: {
                hasMany: false,
                alias: 'users_participants_goals_trackers'
            },
            poi: {
                hasMany: false,
                alias: 'sessions_pois'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }
}

export class ProgressJsonapiResource extends Resource {
    attributes: {
        datetime: string,
    };

    relationships: {
        participant: {
            data: ParticipantJsonapiResource,
            content: 'resource'
        },
        goal: {
            data: ParticipantGoalJsonapiResource,
            content: 'resource'
        },
        tracker: {
            data: ParticipantGoalTrackerJsonapiResource,
            content: 'resource'
        },
        poi: {
            data: PoiJsonapiResource,
            content: 'resource'
        },
    };

    private _dirty: boolean = false;

    private _date: Moment;
    private _human: string;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get datetime(): string {
        return this.attributes.datetime;
    }

    set datetime(value: string) {
        this.attributes.datetime = value;
    }

    get date(): Moment {
        if (!this._date) {
            this._date = moment(this.datetime);
        }

        return this._date;
    }

    startedAtHuman(session: SessionJsonapiResource): string {
        if (!this._date) {
            const pointer: Moment = moment().startOf('day');
            const diff: number = this.date.diff(session.startedAtDate, 's');
            this._human = pointer.set({
                seconds: diff
            }).format('HH:mm:ss');
        }

        return this._human;
    }

    get participant(): ParticipantJsonapiResource {
        return this.relationships.participant.data;
    }

    get goal(): ParticipantGoalJsonapiResource {
        return this.relationships.goal.data;
    }

    get tracker(): ParticipantGoalTrackerJsonapiResource {
        return this.relationships.tracker.data;
    }

    get poi(): PoiJsonapiResource {
        return this.relationships.poi.data;
    }
}
