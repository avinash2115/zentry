import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';

export enum EDriver {
    default = 'default',
    kloudless_s3 = 'kloudless_s3',
    kloudless_google_drive = 'kloudless_google_drive',
    kloudless_dropbox = 'kloudless_dropbox',
    kloudless_box = 'kloudless_box',
}

export class DriverJsonapiService extends Service<DriverJsonapiResource> {
    type = 'users_storages_drivers';
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
