import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import { EDriver } from './driver/driver.jsonapi.service';

export class StorageJsonapiService extends Service<StorageJsonapiResource> {
    type = 'users_storages';
    path = 'storages';
    resource = StorageJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class StorageJsonapiResource extends Resource {
    attributes: {
        driver: EDriver,
        name: string,
        enabled: boolean,
        available: boolean,
        used: number,
        capacity: number,
        created_at: string,
        updated_at: string,
    };

    get isDefault(): boolean {
        return this.driver === EDriver.default;
    }

    get driver(): EDriver {
        return this.attributes.driver;
    }

    get name(): string {
        return this.attributes.name;
    }

    get enabled(): boolean {
        return this.attributes.enabled;
    }

    get available(): boolean {
        return this.attributes.available;
    }

    get used(): number {
        return this.attributes.used;
    }

    get capacity(): number {
        return this.attributes.capacity;
    }

    get isCapacityUnlimited(): boolean {
        return this.capacity === 0;
    }

    get usage(): number {
        if (this.isCapacityUnlimited) {
            return 100;
        }

        return this.used / this.capacity * 100;
    }

    get isUsageThresholdReached(): boolean {
        if (this.isCapacityUnlimited) {
            return false;
        }

        return this.usage > 80;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
