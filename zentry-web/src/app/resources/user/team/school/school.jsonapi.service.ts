import { ICollection, ISchema, Resource } from '../../../../../vendor/vp-ngx-jsonapi';
import { ParticipantJsonapiResource } from '../../participant/participant.jsonapi.service';
import { SourceJsonapiResource } from '../../../crm/source/source.jsonapi.service';
import { IState, STATES } from '../../../../shared/consts/states';
import { TeamJsonapiResource } from '../team.jsonapi.service';
import { BaseList } from '../../../../modules/assistant/list/abstractions/base.abstract';
import {
    EAttributeType,
    EDirection,
    ISortableAttribute,
    ISortableRelation
} from '../../../../modules/assistant/sorting/abstractions/base.abstract';

export class SchoolJsonapiService extends BaseList<SchoolJsonapiResource> {
    type = 'users_teams_schools';
    path = 'schools';
    resource = SchoolJsonapiResource;
    schema: ISchema = {
        relationships: {
            participants: {
                hasMany: true,
                alias: 'users_participants'
            },
            sources: {
                hasMany: true,
                alias: 'crms_sources'
            },
            target_team: {
                hasMany: false,
                alias: 'users_teams'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }

    getSortableNamespace(): string {
        return 'users_teams_schools';
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

export class SchoolJsonapiResource extends Resource {
    attributes: {
        name: string,
        available: number,
        street_address: string,
        city: string,
        state: string,
        zip: string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
        participants: {
            data: ICollection<ParticipantJsonapiResource>,
            content: 'collection'
        },
        sources: {
            data: ICollection<SourceJsonapiResource>,
            content: 'collection'
        },
        target_team: {
            data: TeamJsonapiResource,
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

    get available(): number {
        return this.attributes.available;
    }

    set available(value: number) {
        this.attributes.available = value;
        this.forceDirty();
    }

    get streetAddress(): string {
        return this.attributes.street_address;
    }

    set streetAddress(value: string) {
        this.attributes.street_address = value;
        this.forceDirty();
    }

    get city(): string {
        return this.attributes.city;
    }

    set city(value: string) {
        this.attributes.city = value;
        this.forceDirty();
    }

    get state(): string {
        return this.attributes.state;
    }

    set state(value: string) {
        this.attributes.state = value;
        this.forceDirty();
    }

    get stateLabel(): string {
        return STATES.find((r: IState) => r.value === this.state).label;
    }

    get zip(): string {
        return this.attributes.zip;
    }

    set zip(value: string) {
        this.attributes.zip = value;
        this.forceDirty();
    }

    get generalAddress(): string {
        return [
            this.city,
            this.state,
            this.zip
        ].filter((r: string) => r.trim().length > 0).join(', ');
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }

    get participants(): Array<ParticipantJsonapiResource> {
        return this.relationships.participants.data.$toArray;
    }

    get hasSources(): boolean {
        return this.relationships.sources.data.$length > 0;
    }
}
