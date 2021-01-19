import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';

export enum EDrivers {
    googleCalendar = 'google_calendar',
}

export class DriverJsonapiService extends Service<DriverJsonapiResource> {
    type = 'users_data_providers_drivers';
    path = 'drivers';
    resource = DriverJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class DriverJsonapiResource extends Resource {
    attributes: {
        type: EDrivers,
        title: string,
        config: Array<string>,
    };

    get driverType(): EDrivers {
        return this.attributes.type;
    }

    get title(): string {
        return this.attributes.title;
    }

    get config(): Array<string> {
        return this.attributes.config;
    }
}
