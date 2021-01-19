import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export enum EDeviceModels {
    iphone_5 = 'iPhone 5',
    iphone_5s = 'iPhone 5S',
    iphone_se = 'iPhone SE',
    iphone_6 = 'iPhone 6',
    iphone_6_plus = 'iPhone 6 Plus',
    iphone_6s = 'iPhone 6S',
    iphone_6s_plus = 'iPhone 6S Plus',
    iphone_7 = 'iPhone 7',
    iphone_7_plus = 'iPhone 7 Plus',
    iphone_8 = 'iPhone 8',
    iphone_8_plus = 'iPhone 8 Plus',
    iphone_10 = 'iPhone X',
    iphone_10_max = 'iPhone X MAX',
    iphone_10s = 'iPhone XS',
    iphone_10s_max = 'iPhone XS MAX',
    iphone_10r = 'iPhone XR',
    iphone_11 = 'iPhone 11',
    iphone_11_pro = 'iPhone 11 Pro',
    iphone_11_pro_max = 'iPhone 11 Pro MAX',
}

export class DeviceJsonapiService extends Service<DeviceJsonapiResource> {
    type = 'users_devices';
    path = 'devices';
    resource = DeviceJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class DeviceJsonapiResource extends Resource {
    attributes: {
        type: string,
        model: EDeviceModels,
        reference: string,
        device_token: string,
        created_at: string,
        updated_at: string,
    };

    get deviceType(): string {
        return this.attributes.type;
    }

    get model(): EDeviceModels {
        return this.attributes.model;
    }

    get reference(): String {
        return this.attributes.reference;
    }

    get deviceToken(): string {
        return this.attributes.device_token;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
