import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class BacktrackJsonapiService extends Service<BacktrackJsonapiResource> {
    type = 'users_backtrack';
    path = 'backtrack';
    resource = BacktrackJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class BacktrackJsonapiResource extends Resource {
    attributes: {
        backward: number,
        created_at: string,
        updated_at: string,
    };

    get backward(): number {
        return this.attributes.backward;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
