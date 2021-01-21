import { ICollection, ISchema, Resource } from '../../../vendor/vp-ngx-jsonapi';
import { SourceJsonapiResource } from '../crm/source/source.jsonapi.service';
import { BaseList } from '../../modules/assistant/list/abstractions/base.abstract';
import { EAttributeType, ISortableAttribute, ISortableRelation } from '../../modules/assistant/sorting/abstractions/base.abstract';

export class ProviderJsonapiService extends BaseList<ProviderJsonapiResource>  {
    type = 'provider';
    path = 'provider';
    resource = ProviderJsonapiResource;
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

    getSortableNamespace(): string {
        return 'provider';
    }

    getSortableAttributes(): Array<ISortableAttribute> {
        return [
            {
                label: 'Name',
                column: 'name',
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

export class ProviderJsonapiResource extends Resource {
    attributes: {
        name: string,
        code:string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
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

    get code(): string {
        return this.attributes.code;
    }

    set code(value: string) {
        this.attributes.code = value;

        this.forceDirty();
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
