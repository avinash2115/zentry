import { ICollection, ISchema, Resource } from '../../../vendor/vp-ngx-jsonapi';
import * as moment from 'moment';
import { Moment } from 'moment';
import { PoiJsonapiResource } from './poi/poi.jsonapi.service';
import { ParticipantJsonapiResource as PoiParticipantJsonapiResource } from './poi/participant/participant.jsonapi.service';
import { EType, StreamJsonapiResource } from './stream/stream.jsonapi.service';
import { IGeo } from '../../shared/interfaces/geo/geo.interface';
import { ITag } from '../../shared/interfaces/tag/tag.interface';
import { IHighlight } from '../../shared/components/media/audio/waveform/waveform.component';
import { ParticipantJsonapiResource as UserParticipantJsonapiResource } from '../user/participant/participant.jsonapi.service';
import { UserJsonapiResource } from '../user/user.jsonapi.service';
import { ProgressJsonapiResource } from './progress/progress.jsonapi.service';
import { SourceJsonapiResource } from '../crm/source/source.jsonapi.service';
import { SoapJsonapiResource } from './soap/soap.jsonapi.service';
import { SchoolJsonapiResource } from '../user/team/school/school.jsonapi.service';
import { ServiceJsonapiResource } from '../service/service.jsonapi.service';
import { NoteJsonapiResource } from './note/note.jsonapi.service';
import { TrackerJsonapiResource } from '../user/participant/goal/tracker/tracker.jsonapi.service';
import { GoalJsonapiResource } from '../user/participant/goal/goal.jsonapi.service';
import { BaseList } from '../../modules/assistant/list/abstractions/base.abstract';
import {
    EAttributeType,
    EDirection,
    ISortableAttribute,
    ISortableRelation
} from '../../modules/assistant/sorting/abstractions/base.abstract';

export enum EStatus {
    new = 0,
    started = 10,
    ended = 20,
    wrapped = 30
}

export interface IParticipantProgressCalculation {
    goal: GoalJsonapiResource,
    tracker: TrackerJsonapiResource,
    amount: number,
    percents: number
}

export class SessionJsonapiService extends BaseList<SessionJsonapiResource> {
    type = 'sessions';
    path = 'sessions';
    resource = SessionJsonapiResource;
    schema: ISchema = {
        relationships: {
            user: {
                hasMany: false,
                alias: 'users'
            },
            service: {
                hasMany: false,
                alias: 'services'
            },
            school: {
                hasMany: false,
                alias: 'users_teams_schools'
            },
            pois: {
                hasMany: true,
                alias: 'sessions_pois'
            },
            streams: {
                hasMany: true,
                alias: 'sessions_streams'
            },
            participants: {
                hasMany: true,
                alias: 'users_participants'
            },
            notes: {
                hasMany: true,
                alias: 'sessions_notes'
            },
            progress: {
                hasMany: true,
                alias: 'sessions_progress'
            },
            soaps: {
                hasMany: true,
                alias: 'sessions_soaps'
            },
            sources: {
                hasMany: true,
                alias: 'crms_sources'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }

    getSortableNamespace(): string {
        return 'sessions';
    }

    getSortableAttributes(): Array<ISortableAttribute> {
        return [
            {
                label: 'Started At',
                column: 'started_at',
                type: EAttributeType.date,
                defaultDirection: EDirection.DESC
            },
            {
                label: 'Ended At',
                column: 'ended_at',
                type: EAttributeType.date,
                defaultDirection: EDirection.DESC
            },
            {
                label: 'Name',
                column: 'name',
                type: EAttributeType.string
            },
        ];
    }

    getSortableRelations(): Array<ISortableRelation> {
        return [];
    }

    getSortableDefault(): ISortableAttribute {
        return this.getSortableAttributes()[0];
    }
}

export class SessionJsonapiResource extends Resource {
    attributes: {
        name: string,
        description: string,
        type: string,
        sign: string,
        status: EStatus,
        geo: IGeo,
        tags: Array<ITag>,
        thumbnail_url: string
        excluded_goals: Array<string>,
        is_shared: boolean,
        started_at: string | null,
        ended_at: string | null,
        scheduled_on: string | null,
        scheduled_to: string | null,
        created_at: string,
    };

    relationships: {
        user: {
            data: UserJsonapiResource,
            content: 'resource'
        },
        service: {
            data: ServiceJsonapiResource,
            content: 'resource'
        },
        school: {
            data: SchoolJsonapiResource,
            content: 'resource'
        },
        pois: {
            data: ICollection<PoiJsonapiResource>,
            content: 'collection'
        },
        streams: {
            data: ICollection<StreamJsonapiResource>,
            content: 'collection'
        },
        participants: {
            data: ICollection<UserParticipantJsonapiResource>,
            content: 'collection'
        },
        notes: {
            data: ICollection<NoteJsonapiResource>,
            content: 'collection'
        },
        progress: {
            data: ICollection<ProgressJsonapiResource>,
            content: 'collection'
        },
        soaps: {
            data: ICollection<SoapJsonapiResource>,
            content: 'collection'
        },
        sources: {
            data: ICollection<SourceJsonapiResource>,
            content: 'collection'
        },
    };

    private _dirty: boolean = false;
    private _readonly: boolean = false;

    private _startedAtDate: Moment;
    private _endedAtDate: Moment;
    private _scheduledOnDate: Moment;
    private _scheduledToDate: Moment;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get readonly(): boolean {
        return this._readonly;
    }

    forceReadonly(): void {
        this._readonly = true;
    }

    get name(): string {
        return this.attributes.name;
    }

    set name(value: string) {
        this.attributePreserve('name');

        this.attributes.name = value;

        this.forceDirty();
    }

    get sign(): string {
        return this.attributes.sign;
    }

    set sign(value: string) {
        this.attributes.sign = value;

        this.forceDirty();
    }

    get isLocked(): boolean {
        return !!this.sign;
    }

    get description(): string {
        return this.attributes.description;
    }

    set description(value: string) {
        this.attributes.description = value;
        this.forceDirty();
    }

    get sessionType(): string {
        return this.attributes.type;
    }

    set sessionType(value: string) {
        this.attributes.type = value;
        this.forceDirty();
    }

    get excludedGoals(): Array<string> {
        return this.attributes.excluded_goals;
    }

    set excludedGoals(value: Array<string>) {
        this.attributes.excluded_goals = value;
        this.forceDirty();
    }

    isSessionType(value: string): boolean {
        return this.sessionType === value;
    }

    get status(): EStatus {
        return this.attributes.status;
    }

    set status(value: EStatus) {
        this.attributePreserve('status');

        this.attributes.status = value;

        this.forceDirty();
    }

    isStatus(value: EStatus): boolean {
        return this.status === value;
    }

    get geo(): IGeo {
        return this.attributes.geo;
    }

    set geo(value: IGeo) {
        this.attributePreserve('geo');

        this.attributes.geo = value;

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

    get isShared(): boolean {
        return this.attributes.is_shared;
    }

    get startedAt(): string | null {
        return this.attributes.started_at;
    }

    get startedAtDate(): Moment {
        if (!this._startedAtDate) {
            this._startedAtDate = moment(this.startedAt);
        }

        return this._startedAtDate;
    }

    get endedAt(): string | null {
        return this.attributes.ended_at;
    }

    set endedAt(value: string | null) {
        this.attributes.ended_at = value;
    }

    get endedAtDate(): Moment {
        if (!this._endedAtDate) {
            this._endedAtDate = moment(this.endedAt);
        }

        return this._endedAtDate;
    }

    get scheduledOn(): string | null {
        return this.attributes.scheduled_on;
    }

    set scheduledOn(value: string | null) {
        this.attributes.scheduled_on = value;
    }

    get scheduledOnDate(): Moment {
        if (!this._scheduledOnDate) {
            this._scheduledOnDate = moment(this.scheduledOn);
        }

        return this._scheduledOnDate;
    }

    get scheduledTo(): string | null {
        return this.attributes.scheduled_to;
    }

    set scheduledTo(value: string | null) {
        this.attributes.scheduled_to = value;
    }

    get scheduledToDate(): Moment {
        if (!this._scheduledToDate) {
            this._scheduledToDate = moment(this.scheduledTo);
        }

        return this._scheduledToDate;
    }

    get isScheduled(): boolean {
        return !!this.scheduledOn;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get isNew(): boolean {
        return this.isStatus(EStatus.new);
    }

    get isStarted(): boolean {
        return this.isStatus(EStatus.started) && !!this.startedAt;
    }

    get isActive(): boolean {
        return this.isStarted && !this.endedAt;
    }

    get isEnded(): boolean {
        return (this.isStatus(EStatus.ended) || this.isStatus(EStatus.wrapped)) && !!this.endedAt;
    }

    get isWrapped(): boolean {
        return this.isStatus(EStatus.wrapped) && this.isEnded;
    }

    get isFinished(): boolean {
        return !!this.startedAt && !!this.endedAt;
    }

    get isWrapReady(): boolean {
        return this.isFinished && Object.values(EType).filter((type: EType) => {
            return !(this.streamByType(type) instanceof StreamJsonapiResource);
        }).length === 0;
    }

    get user(): UserJsonapiResource {
        return this.relationships.user.data;
    }

    get service(): ServiceJsonapiResource {
        return this.relationships.service.data;
    }

    get school(): SchoolJsonapiResource {
        return this.relationships.school.data;
    }

    get pois(): Array<PoiJsonapiResource> {
        return this.relationships.pois.data.$toArray;
    }

    get poisSorted(): Array<PoiJsonapiResource> {
        return this.pois.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());
    }

    get poisAsHighlights(): Array<IHighlight> {
        return this.poisSorted.map((p: PoiJsonapiResource) => {
            return {
                startedAtUnix: p.startedAtDate.unix(),
                endedAtUnix: p.endedAtDate.unix(),
            }
        });
    }

    poiById(id: string): PoiJsonapiResource | undefined {
        return this.pois.find((p: PoiJsonapiResource) => p.id === id);
    }

    get streams(): Array<StreamJsonapiResource> {
        return this.relationships.streams.data.$toArray;
    }

    streamByType(type: EType): StreamJsonapiResource | undefined {
        return this.streams.find((s: StreamJsonapiResource) => s.isType(type));
    }

    get isEntirelyRecorded(): boolean {
        return this.streamByType(EType.combined) instanceof StreamJsonapiResource;
    }

    get participants(): Array<UserParticipantJsonapiResource> {
        return this.relationships.participants.data.$toArray;
    }

    participantsProgress(entity: UserParticipantJsonapiResource): Array<ProgressJsonapiResource> {
        return this.progress.filter((r: ProgressJsonapiResource) => r.participant.id === entity.id);
    }

    participantsProgressCalculation(entity: UserParticipantJsonapiResource): Array<IParticipantProgressCalculation> {
        const progress: Array<ProgressJsonapiResource> = this.participantsProgress(entity);
        const result: Array<IParticipantProgressCalculation> = [];

        progress.forEach((r: ProgressJsonapiResource) => {
            const existingIndex: number = result.findIndex((p: IParticipantProgressCalculation) => p.tracker.id === r.tracker.id);

            if (existingIndex === -1) {
                result.push({
                    goal: r.goal,
                    tracker: r.tracker,
                    amount: 1,
                    percents: (100 / progress.length)
                });
            } else {
                ++result[existingIndex].amount;
                result[existingIndex].percents = (100 / progress.length) * result[existingIndex].amount;
            }
        });

        result.sort((a: IParticipantProgressCalculation, b: IParticipantProgressCalculation) => a.amount - b.amount);

        return result;
    }

    participantsClips(entity: UserParticipantJsonapiResource): Array<PoiJsonapiResource> {
        return this.pois.filter((r: PoiJsonapiResource) => r.participants.findIndex((p: PoiParticipantJsonapiResource) => p.raw.id === entity.id) !== -1);
    }

    participantsSoaps(entity: UserParticipantJsonapiResource): Array<SoapJsonapiResource> {
        return this.soaps.filter((r: SoapJsonapiResource) => r.participant.id === entity.id);
    }

    participantsNotes(entity: UserParticipantJsonapiResource): Array<NoteJsonapiResource> {
        return this.notes.filter((r: NoteJsonapiResource) => r.participant instanceof UserParticipantJsonapiResource && r.participant.id === entity.id);
    }

    get notes(): Array<NoteJsonapiResource> {
        return this.relationships.notes.data.$toArray;
    }

    get progress(): Array<ProgressJsonapiResource> {
        return this.relationships.progress.data.$toArray;
    }

    get soaps(): Array<SoapJsonapiResource> {
        return this.relationships.soaps.data.$toArray;
    }

    get hasSources(): boolean {
        return this.relationships.sources.data.$length > 0;
    }
}
