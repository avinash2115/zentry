import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export enum EType {
    audio = 'audio',
    combined = 'combined'
}

export class StreamJsonapiService extends Service<StreamJsonapiResource> {
    type = 'sessions_streams';
    path = 'streams';
    resource = StreamJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class StreamJsonapiResource extends Resource {
    attributes: {
        type: EType,
        name: string,
        convert_progress: number,
    };

    get streamType(): EType {
        return this.attributes.type;
    }

    isType(type: EType): boolean {
        return this.streamType === type;
    }

    get name(): string {
        return this.attributes.name;
    }

    get convertProgress(): number {
        return this.attributes.convert_progress;
    }

    get isConverted(): boolean {
        return this.convertProgress === 100;
    }
}
