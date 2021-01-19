import { Resource, Service } from '../../../vendor/vp-ngx-jsonapi';

export enum EType {
    recorded = 'sessions',
    recordedPoi = 'sessions_pois'
}

export interface IPayload {
    pattern: string,
    parameters: { [key: string]: string },
    methods: Array<string>
}

export class SharedJsonapiService extends Service<SharedJsonapiResource> {
    type = 'shared';
    path = 'share';
    resource = SharedJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class SharedJsonapiResource extends Resource {
    attributes: {
        type: EType,
        payload: IPayload,
        created_at: string,
        updated_at: string
    };

    get sharedType(): EType {
        return this.attributes.type;
    }

    get payload(): IPayload {
        return this.attributes.payload;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
