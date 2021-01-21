import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export enum EType {
    team = 'team',
    participant = 'participant',
    participantGoal = 'participant_goal',
    school = 'school',
    session = 'session',
    service = 'service',
    provider = 'provider',
}

export class SyncLogJsonapiService extends Service<SyncLogJsonapiResource> {
    type = 'crms_sync_logs';
    path = 'sync/log';
    resource = SyncLogJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class SyncLogJsonapiResource extends Resource {
    attributes: {
        type: EType,
        created_at: string,
    };

    get syncLogType(): EType {
        return this.attributes.type;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }
}
