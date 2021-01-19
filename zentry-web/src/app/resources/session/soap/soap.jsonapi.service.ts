import { ISchema, Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import { ParticipantJsonapiResource } from '../../user/participant/participant.jsonapi.service';
import { GoalJsonapiResource as ParticipantGoalJsonapiResource } from '../../user/participant/goal/goal.jsonapi.service';

export enum ERate {
    regression = 'regression',
    noProgress = 'no_progress',
    minimalProgress = 'minimal_progress',
    progress = 'progress',
    goalMet = 'goal_met',
    goalNotTargeted = 'goal_not_targeted',
    maintenance = 'maintenance',
}

export interface IRate {
    value: ERate,
    label: string,
}

export const Rates: Array<IRate> = [
    {
        value: ERate.regression,
        label: 'Regression'
    },
    {
        value: ERate.noProgress,
        label: 'No Progress'
    },
    {
        value: ERate.minimalProgress,
        label: 'Minimal Progress'
    },
    {
        value: ERate.progress,
        label: 'Progress'
    },
    {
        value: ERate.goalMet,
        label: 'Goal Met'
    },
    {
        value: ERate.goalNotTargeted,
        label: 'Goal Not Targeted'
    },
    {
        value: ERate.maintenance,
        label: 'Maintenance'
    }
]

export const RatesDictionary: Record<string, string> = Rates.reduce((acc: Record<string, string>, rate: IRate) => ({
    ...acc,
    [rate.value]: rate.label
}), {})

export class SoapJsonapiService extends Service<SoapJsonapiResource> {
    type = 'sessions_soaps';
    path = 'soaps';
    resource = SoapJsonapiResource;
    schema: ISchema = {
        relationships: {
            participant: {
                hasMany: false,
                alias: 'users_participants'
            },
            goal: {
                hasMany: false,
                alias: 'users_participants_goals'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }
}

export class SoapJsonapiResource extends Resource {
    attributes: {
        present: boolean,
        rate: ERate,
        activity: string,
        note: string,
        plan: string,
        created_at: string,
        updated_at: string,
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
    };

    private _dirty: boolean = false;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get present(): boolean {
        return this.attributes.present;
    }

    set present(value: boolean) {
        this.attributes.present = value;
    }

    get rate(): ERate {
        return this.attributes.rate;
    }

    set rate(value: ERate) {
        this.attributes.rate = value;
    }

    get activity(): string {
        return this.attributes.activity;
    }

    set activity(value: string) {
        this.attributes.activity = value;
    }

    get note(): string {
        return this.attributes.note;
    }

    set note(value: string) {
        this.attributes.note = value;
    }

    get plan(): string {
        return this.attributes.plan;
    }

    set plan(value: string) {
        this.attributes.plan = value;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }

    get participant(): ParticipantJsonapiResource {
        return this.relationships.participant.data;
    }

    get goal(): ParticipantGoalJsonapiResource {
        return this.relationships.goal.data;
    }
}
