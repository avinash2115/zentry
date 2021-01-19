import { ICollection, ISchema, Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';
import * as moment from 'moment';
import { Moment } from 'moment';
import { UtilsService } from '../../../shared/services/utils.service';
import { SessionJsonapiResource } from '../session.jsonapi.service';
import { ITag } from '../../../shared/interfaces/tag/tag.interface';
import { ParticipantJsonapiResource } from './participant/participant.jsonapi.service';
import { TranscriptJsonapiResource } from '../../transcript/transcript.jsonapi.service';

export enum EType {
    backtrack = 'backtrack',
    poi = 'poi',
    stopwatch = 'stopwatch'
}

export class PoiJsonapiService extends Service<PoiJsonapiResource> {
    type = 'sessions_pois';
    path = 'pois';
    resource = PoiJsonapiResource;
    schema: ISchema = {
        relationships: {
            participants: {
                hasMany: true,
                alias: 'sessions_pois_participants'
            },
            transcript: {
                hasMany: false,
                alias: 'transcripts'
            },
        }
    };

    constructor() {
        super();
        this.register();
    }
}

export class PoiJsonapiResource extends Resource {
    attributes: {
        type: EType,
        name: string,
        tags: Array<ITag>,
        thumbnail_url: string,
        duration: number,
        is_shared: boolean,
        started_at: string,
        ended_at: string,
        created_at: string,
    };

    relationships: {
        participants: {
            data: ICollection<ParticipantJsonapiResource>,
            content: 'collection'
        },
        transcript: {
            data: TranscriptJsonapiResource,
            content: 'resource'
        },
    };

    private _dirty: boolean = false;
    private _readonly: boolean = false;

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

    clean(): void {
        this._dirty = false;
    }

    get readonly(): boolean {
        return this._readonly;
    }

    set readonly(value: boolean) {
        this._readonly = value;
    }

    get poiType(): EType {
        return this.attributes.type;
    }

    set poiType(value: EType) {
        this.attributes.type = value;
    }

    isPoiType(type: EType): boolean {
        return this.poiType === type;
    }

    get name(): string {
        return this.attributes.name;
    }

    set name(value: string) {
        this.attributePreserve('name');

        this.attributes.name = value;

        this.forceDirty();
    }

    get tags(): Array<ITag> {
        return this.attributes.tags;
    }

    set tags(value: Array<ITag>) {
        this.attributePreserve('tags');

        this.attributes.tags = value;

        this.forceDirty();
    }

    get thumbnailUrl(): string {
        return this.attributes.thumbnail_url;
    }

    get duration(): number {
        return this.attributes.duration;
    }

    get durationHuman(): string {
        return UtilsService.msHuman(this.duration * 1000, true);
    }

    get isShared(): boolean {
        return this.attributes.is_shared;
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

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get participants(): Array<ParticipantJsonapiResource> {
        return this.relationships.participants.data.$toArray;
    }

    get transcript(): TranscriptJsonapiResource {
        return this.relationships.transcript.data;
    }
}
