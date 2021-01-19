import { ICollection, ISchema, Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';
import { TrackerJsonapiResource } from './tracker/tracker.jsonapi.service';
import { SourceJsonapiResource } from '../../../crm/source/source.jsonapi.service';
import { IepJsonapiResource } from '../iep/iep.jsonapi.service';

export class GoalJsonapiService extends Service<GoalJsonapiResource> {
    type = 'users_participants_goals';
    path = 'goals';
    resource = GoalJsonapiResource;
    schema: ISchema = {
        relationships: {
            trackers: {
                hasMany: true,
                alias: 'users_participants_goals_trackers'
            },
            sources: {
                hasMany: true,
                alias: 'crms_sources'
            },
            iep: {
                hasMany: false,
                alias: 'users_participants_ieps'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }
}

export class GoalJsonapiResource extends Resource {
    attributes: {
        name: string,
        description: string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
        trackers: {
            data: ICollection<TrackerJsonapiResource>,
            content: 'collection'
        },
        sources: {
            data: ICollection<SourceJsonapiResource>,
            content: 'collection'
        },
        iep: {
            data: IepJsonapiResource,
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

    get name(): string {
        return this.attributes.name;
    }

    set name(value: string) {
        this.attributes.name = value;
        this.forceDirty();
    }

    get description(): string {
        return this.attributes.description;
    }

    set description(value: string) {
        this.attributes.description = value;
        this.forceDirty();
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }

    get iep(): IepJsonapiResource | undefined {
        return this.relationships.iep && this.relationships.iep.data;
    }

    get trackers(): Array<TrackerJsonapiResource> {
        return this.relationships.trackers.data.$toArray;
    }

    get hasSources(): boolean {
        return this.relationships.sources.data.$length > 0;
    }
}
