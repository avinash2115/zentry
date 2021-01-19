import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';

export enum EDriver {
    therapylog = 'therapylog'
}

export class DriverJsonapiService extends Service<DriverJsonapiResource> {
    type = 'users_crms_drivers';
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
        return Object.keys(this.attributes.config);
    }
}
