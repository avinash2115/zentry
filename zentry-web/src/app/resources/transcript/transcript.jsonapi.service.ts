import { Resource, Service } from '../../../vendor/vp-ngx-jsonapi';

export class TranscriptJsonapiService extends Service<TranscriptJsonapiResource> {
    type = 'transcripts';
    path = 'transcripts'
    resource = TranscriptJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class TranscriptJsonapiResource extends Resource {
    attributes: {
        transcript: string,
    };

    get transcript(): string {
        return this.attributes.transcript;
    }
}
