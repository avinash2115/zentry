import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import { EDriver } from './driver/driver.jsonapi.service';

export interface IConfig {
    email: string,
    password: string,
}

export class CrmJsonapiService extends Service<CrmJsonapiResource> {
    type = 'users_crms';
    path = 'crms';
    resource = CrmJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class CrmJsonapiResource extends Resource {
    attributes: {
        driver: EDriver,
        active: boolean,
        notified: boolean,
        config: IConfig
        created_at: string,
        updated_at: string,
    };

    get driver(): EDriver {
        return this.attributes.driver;
    }

    set driver(value: EDriver) {
        this.attributes.driver = value;
    }

    get active(): boolean {
        return this.attributes.active;
    }

    get notified(): boolean {
        return this.attributes.notified;
    }

    set config(value: IConfig) {
        this.attributes.config = value;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
