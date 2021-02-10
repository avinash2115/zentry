import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class SourceJsonapiService extends Service<SourceJsonapiResource> {
    type = 'crms_sources';
    path = 'sources';
    resource = SourceJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class SourceJsonapiResource extends Resource {
    attributes: {
        owner: string,
        sourceId: string,
        created_at: string,
        updated_at: string,
    };

    get owner(): string {
        return this.attributes.owner;
    }

    get sourceId(): string {
        return this.attributes.sourceId;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
