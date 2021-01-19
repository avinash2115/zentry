import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export enum EDriver {
    google = 'google',
}

export class DriverJsonapiService extends Service<DriverJsonapiResource> {
    type = 'sso_drivers';
    path = 'drivers';
    resource = DriverJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class DriverJsonapiResource extends Resource {
    attributes: {
        type: EDriver,
        title: string,
        config: Array<string>,
    };

    get driverType(): EDriver {
        return this.attributes.type;
    }

    get title(): string {
        return this.attributes.title;
    }

    get config(): Array<string> {
        return this.attributes.config;
    }
}
