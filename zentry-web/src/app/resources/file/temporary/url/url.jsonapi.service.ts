import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';

export class UrlJsonapiService extends Service<UrlJsonapiResource> {
    type = 'files_temporary_urls';
    resource = UrlJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class UrlJsonapiResource extends Resource {
    attributes: {
        name: string,
        url: string
    };

    get name(): string {
        return this.attributes.name;
    }

    get url(): string {
        return this.attributes.url;
    }
}
