import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class PhraseJsonapiService extends Service<PhraseJsonapiResource> {
    type = 'transcripts_phrases';
    resource = PhraseJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class PhraseJsonapiResource extends Resource {
    attributes: {
        phrase: string,
    };

    get phrase(): string {
        return this.attributes.phrase;
    }
}
