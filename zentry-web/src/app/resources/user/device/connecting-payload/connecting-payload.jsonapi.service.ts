import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';
import { EDeviceModels } from '../device.jsonapi.service';

export class ConnectingPayloadJsonapiService extends Service<ConnectingPayloadJsonapiResource> {
    type = 'users_devices_connecting_payload';
    path = 'devices';
    resource = ConnectingPayloadJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class ConnectingPayloadJsonapiResource extends Resource {
    attributes: {
        type: string,
        model: EDeviceModels,
        reference: string,
        device_token: string,
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
}
