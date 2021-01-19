import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import { EDrivers } from './driver/driver.jsonapi.service';

export enum EStatuses {
    disabled = 0,
    enabled = 10,
    authError = 20
}

export class DataProviderJsonapiService extends Service<DataProviderJsonapiResource> {
    type = 'users_data_providers';
    path = 'data_providers';
    resource = DataProviderJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class DataProviderJsonapiResource extends Resource {
    attributes: {
        driver: EDrivers,
        name: string,
        status: EStatuses,
        config: Array<{ type: string, value: string, [key: string]: any }>,
    };

    get driver(): EDrivers {
        return this.attributes.driver;
    }

    get name(): string {
        return this.attributes.name;
    }

    get status(): EStatuses {
        return this.attributes.status;
    }

    set status(value: EStatuses) {
        this.attributes.status = value;
    }

    get isStatusFailed(): boolean {
        return this.status === EStatuses.authError;
    }

    get config(): Array<{ type: string, value: string, [key: string]: any }> {
        return this.attributes.config;
    }

    configByType(value: string): { type: string, value: string, [key: string]: any } {
        return this.config.find((r: { type: string, value: string, [key: string]: any }) => r.type === value);
    }
}
