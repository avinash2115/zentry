import { ICollection, ISchema, Resource } from '../../../../vendor/vp-ngx-jsonapi';
import { SchoolJsonapiResource } from './school/school.jsonapi.service';
import { ParticipantJsonapiResource } from '../participant/participant.jsonapi.service';
import { SourceJsonapiResource } from '../../crm/source/source.jsonapi.service';
import { BaseList } from '../../../modules/assistant/list/abstractions/base.abstract';
import {
    EAttributeType,
    EDirection,
    ISortableAttribute,
    ISortableRelation
} from '../../../modules/assistant/sorting/abstractions/base.abstract';

export class TeamJsonapiService extends BaseList<TeamJsonapiResource> {
    type = 'users_teams';
    path = 'teams';
    resource = TeamJsonapiResource;
    schema: ISchema = {
        relationships: {
            schools: {
                hasMany: true,
                alias: 'users_teams_schools'
            },
            participants: {
                hasMany: true,
                alias: 'users_participants'
            },
            sources: {
                hasMany: true,
                alias: 'crms_sources'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }

    getSortableNamespace(): string {
        return 'users_teams';
    }

    getSortableAttributes(): Array<ISortableAttribute> {
        return [
            {
                label: 'Created At',
                column: 'created_at',
                type: EAttributeType.date,
                defaultDirection: EDirection.DESC
            },
            {
                label: 'Name',
                column: 'name',
                type: EAttributeType.string
            },
            {
                label: 'Description',
                column: 'description',
                type: EAttributeType.string
            }
        ];
    }

    getSortableRelations(): Array<ISortableRelation> {
        return [];
    }

    getSortableDefault(): ISortableAttribute {
        return this.getSortableAttributes()[0];
    }
}

export class TeamJsonapiResource extends Resource {
    attributes: {
        name: string,
        description: string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
        schools: {
            data: ICollection<SchoolJsonapiResource>,
            content: 'collection'
        },
        participants: {
            data: ICollection<ParticipantJsonapiResource>,
            content: 'collection'
        },
        sources: {
            data: ICollection<SourceJsonapiResource>,
            content: 'collection'
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

    get schools(): Array<SchoolJsonapiResource> {
        return this.relationships.schools.data.$toArray;
    }

    get participants(): Array<ParticipantJsonapiResource> {
        return this.relationships.participants.data.$toArray;
    }

    get hasSources(): boolean {
        return this.relationships.sources.data.$length > 0;
    }
}
