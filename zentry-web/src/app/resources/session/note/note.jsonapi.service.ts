import { ISchema, Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import { ParticipantJsonapiResource } from '../../user/participant/participant.jsonapi.service';
import { PoiJsonapiResource } from '../poi/poi.jsonapi.service';
import { ParticipantJsonapiResource as SessionPoiParticipantJsonapiResource } from '../poi/participant/participant.jsonapi.service';

export class NoteJsonapiService extends Service<NoteJsonapiResource> {
    type = 'sessions_notes';
    path = 'notes';
    resource = NoteJsonapiResource;
    schema: ISchema = {
        relationships: {
            participant: {
                hasMany: false,
                alias: 'users_participants'
            },
            poi: {
                hasMany: false,
                alias: 'sessions_pois'
            },
            poi_participant: {
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

export class NoteJsonapiResource extends Resource {
    attributes: {
        text: string,
        url: string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
        participant: {
            data: ParticipantJsonapiResource,
            content: 'resource'
        },
        poi: {
            data: PoiJsonapiResource,
            content: 'resource'
        },
        poi_participant: {
            data: SessionPoiParticipantJsonapiResource,
            content: 'resource'
        },
    };

    private _dirty: boolean = false;
    private _readonly: boolean = false;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get readonly(): boolean {
        return this._readonly;
    }

    set readonly(value: boolean) {
        this._readonly = value;
    }

    get text(): string {
        return this.attributes.text;
    }

    get isText(): boolean {
        return !!this.text;
    }

    set text(value: string) {
        this.attributes.text = value;
    }

    get url(): string {
        return this.attributes.url;
    }

    get isUrl(): boolean {
        return !!this.url;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }

    get participant(): ParticipantJsonapiResource | undefined {
        return this.relationships.participant.content === 'resource' ? this.relationships.participant.data : undefined;
    }

    get poi(): PoiJsonapiResource | undefined {
        return this.relationships.poi.content === 'resource' ? this.relationships.poi.data : undefined;
    }

    get poiParticipant(): SessionPoiParticipantJsonapiResource | undefined {
        return this.relationships.poi_participant.content === 'resource' ? this.relationships.poi_participant.data : undefined;
    }
}
