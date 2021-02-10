import * as moment from 'moment';
import { ICollection, ISchema, Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';
import { SourceJsonapiResource } from '../../../crm/source/source.jsonapi.service';

export class IepJsonapiService extends Service<IepJsonapiResource> {
    type = 'users_participants_ieps';
    path = 'ieps';
    resource = IepJsonapiResource;
    schema: ISchema = {
        relationships: {
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
}

export class IepJsonapiResource extends Resource {
    attributes: {
        date_actual: string,
        date_reeval: string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
        sources: {
            data: ICollection<SourceJsonapiResource>,
            content: 'collection'
        },
    }

    private _dirty: boolean = false;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get dateActual(): string {
        return moment(this.attributes.date_actual).format('YYYY-MM-DD')
    }

    set dateActual(value: string) {
        this.attributes.date_actual = value;
        this.forceDirty();
    }

    get dateActualHuman(): string {
        return moment(this.attributes.date_actual).format('MM/DD/YYYY')
    }

    get dateReeval(): string {
        return moment(this.attributes.date_reeval).format('YYYY-MM-DD')
    }

    set dateReeval(value: string) {
        this.attributes.date_reeval = value;
        this.forceDirty();
    }

    get dateReevalHuman(): string {
        return moment(this.attributes.date_reeval).format('MM/DD/YYYY')
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }

    get hasSources(): boolean {
        return this.relationships.sources.data.$length > 0;
    }
}
