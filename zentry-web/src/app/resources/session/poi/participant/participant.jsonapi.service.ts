import { ISchema, Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';
import * as moment from 'moment';
import { Moment } from 'moment';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../../../user/participant/participant.jsonapi.service';
import { SessionJsonapiResource } from '../../session.jsonapi.service';

export class ParticipantJsonapiService extends Service<ParticipantJsonapiResource> {
    type = 'sessions_pois_participants';
    path = 'participants';
    resource = ParticipantJsonapiResource;
    schema: ISchema = {
        relationships: {
            raw: {
                hasMany: false,
                alias: 'users_participants'
            },
        }
    };

    constructor() {
        super();
        this.register();
    }
}

export class ParticipantJsonapiResource extends Resource {
    attributes: {
        email: string,
        first_name: string,
        last_name: string,
        started_at: string,
        ended_at: string,
    };

    relationships: {
        raw: {
            data: UserParticipantJsonapiResource,
            content: 'resource'
        }
    };

    private _dirty: boolean = false;

    private _startedAtDate: Moment;
    private _startedAtHuman: string;

    private _endedAtDate: Moment;
    private _endedAtHuman: string;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get email(): string {
        return this.attributes.email;
    }

    set email(email: string) {
        this.attributes.email = email;
        this._dirty = true;
    }

    get firstName(): string {
        return this.attributes.first_name || '';
    }

    set firstName(value: string) {
        this.attributes.first_name = value;
    }

    get lastName(): string {
        return this.attributes.last_name || '';
    }

    set lastName(value: string) {
        this.attributes.last_name = value;
    }

    get fullname(): string {
        return `${this.firstName} ${this.lastName}`.trim();
    }

    get initials(): string {
        return (this.firstName && this.lastName) ? this.firstName.substring(0, 1) + this.lastName.substring(0, 1) : this.email.substring(0, 2);
    }

    get startedAt(): string {
        return this.attributes.started_at;
    }

    set startedAt(value: string) {
        this.attributes.started_at = value;
    }

    get startedAtDate(): Moment {
        if (!this._startedAtDate) {
            this._startedAtDate = moment(this.startedAt);
        }

        return this._startedAtDate;
    }

    startedAtHuman(session: SessionJsonapiResource): string {
        if (!this._startedAtHuman) {
            const pointer: Moment = moment().startOf('day');
            const diff: number = this.startedAtDate.diff(session.startedAtDate, 's');
            this._startedAtHuman = pointer.set({
                seconds: diff
            }).format('HH:mm:ss');
        }

        return this._startedAtHuman;
    }

    get endedAt(): string {
        return this.attributes.ended_at;
    }

    set endedAt(value: string) {
        this.attributes.ended_at = value;
    }

    get endedAtDate(): Moment {
        if (!this._endedAtDate) {
            this._endedAtDate = moment(this.endedAt);
        }

        return this._endedAtDate;
    }

    endedAtHuman(session: SessionJsonapiResource): string {
        if (!this._endedAtHuman) {
            const pointer: Moment = moment().startOf('day');
            const diff: number = this.endedAtDate.diff(session.startedAtDate, 's');
            this._endedAtHuman = pointer.set({
                seconds: diff
            }).format('HH:mm:ss');
        }

        return this._endedAtHuman;
    }

    get raw(): UserParticipantJsonapiResource {
        return this.relationships.raw.data;
    }
}
