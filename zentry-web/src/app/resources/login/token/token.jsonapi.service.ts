import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class TokenJsonapiService extends Service<TokenJsonapiResource> {
    type = 'login_tokens';
    path = 'login/token';
    resource = TokenJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class TokenJsonapiResource extends Resource {
    attributes: {
        created_at: string,
    };

    get createdAt(): string {
        return this.attributes.created_at;
    }
}
