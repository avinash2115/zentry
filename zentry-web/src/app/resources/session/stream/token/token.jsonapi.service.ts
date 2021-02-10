import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';

export class TokenJsonapiService extends Service<TokenJsonapiResource> {
    type = 'sessions_streams_token';
    resource = TokenJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class TokenJsonapiResource extends Resource {
    attributes: {
        token: string,
    };

    get token(): string {
        return this.attributes.token;
    }
}
