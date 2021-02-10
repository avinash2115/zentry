import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class PoiJsonapiService extends Service<PoiJsonapiResource> {
    type = 'users_poi';
    path = 'poi';
    resource = PoiJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class PoiJsonapiResource extends Resource {
    attributes: {
        backward: number,
        forward: number,
        created_at: string,
        updated_at: string,
    };

    get backward(): number {
        return this.attributes.backward;
    }

    get forward(): number {
        return this.attributes.forward;
    }

    get amount(): number {
        return this.backward + this.forward;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
