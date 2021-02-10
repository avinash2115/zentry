import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class WordJsonapiService extends Service<WordJsonapiResource> {
    type = 'transcripts_words';
    resource = WordJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class WordJsonapiResource extends Resource {
    attributes: {
        word: string,
    };

    get word(): string {
        return this.attributes.word;
    }
}
