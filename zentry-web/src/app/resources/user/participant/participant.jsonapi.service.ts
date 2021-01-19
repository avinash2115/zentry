import * as moment from 'moment';
import { ICollection, ISchema, Resource } from '../../../../vendor/vp-ngx-jsonapi';
import { SchoolJsonapiResource } from '../team/school/school.jsonapi.service';
import { TeamJsonapiResource } from '../team/team.jsonapi.service';
import { TherapyJsonapiResource } from './therapy/therapy.jsonapi.service';
import { GoalJsonapiResource } from './goal/goal.jsonapi.service';
import { SourceJsonapiResource } from '../../crm/source/source.jsonapi.service';
import { SoapJsonapiResource } from '../../session/soap/soap.jsonapi.service';
import { BaseList } from '../../../modules/assistant/list/abstractions/base.abstract';
import {
    EAttributeType,
    EDirection,
    ISortableAttribute,
    ISortableRelation
} from '../../../modules/assistant/sorting/abstractions/base.abstract';
import { IepJsonapiResource } from './iep/iep.jsonapi.service';

export enum EGenders {
    male = 'male',
    female = 'female'
}

export class ParticipantJsonapiService extends BaseList<ParticipantJsonapiResource> {
    type = 'users_participants';
    path = 'participants';
    resource = ParticipantJsonapiResource;
    schema: ISchema = {
        relationships: {
            therapy: {
                hasMany: false,
                alias: 'users_participants_therapies'
            },
            team: {
                hasMany: false,
                alias: 'users_teams'
            },
            school: {
                hasMany: false,
                alias: 'users_teams_schools'
            },
            goals: {
                hasMany: true,
                alias: 'users_participants_goals'
            },
            ieps: {
                hasMany: true,
                alias: 'users_participants_ieps'
            },
            sources: {
                hasMany: true,
                alias: 'crms_sources'
            },
            soaps: {
                hasMany: true,
                alias: 'sessions_soaps'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }

    getSortableNamespace(): string {
        return 'users_participants';
    }

    getSortableAttributes(): Array<ISortableAttribute> {
        return [
            {
                label: 'Created At',
                column: 'created_at',
                type: EAttributeType.date,
                defaultDirection: EDirection.DESC
            },
            {
                label: 'Full Name',
                column: ['first_name', 'last_name'],
                type: EAttributeType.string
            },
            {
                label: 'Email',
                column: 'email',
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

export class ParticipantJsonapiResource extends Resource {
    attributes: {
        email: string,
        first_name: string,
        last_name: string,
        phone_code: string,
        phone_number: string,
        parent_phone_number: string,
        parent_email: string,
        avatar: string,
        gender: EGenders,
        dob: string,
        created_at: string,
        updated_at: string,
    };

    relationships: {
        therapy: {
            data: TherapyJsonapiResource,
            content: 'resource',
        },
        team: {
            data: TeamJsonapiResource,
            content: 'resource'
        },
        school: {
            data: SchoolJsonapiResource,
            content: 'resource'
        },
        goals: {
            data: ICollection<GoalJsonapiResource>,
            content: 'collection'
        },
        ieps: {
            data: ICollection<IepJsonapiResource>,
            content: 'collection'
        },
        sources: {
            data: ICollection<SourceJsonapiResource>,
            content: 'collection'
        },
        soaps: {
            data: ICollection<SoapJsonapiResource>,
            content: 'collection'
        },
    };

    private _dirty: boolean = false;

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
        this.forceDirty();
    }

    get parentEmail(): string {
        return this.attributes.parent_email;
    }

    set parentEmail(parent_email: string) {
        this.attributes.parent_email = parent_email;
        this.forceDirty();
    }

    get firstName(): string {
        return this.attributes.first_name || '';
    }

    set firstName(value: string) {
        this.attributes.first_name = value;
        this.forceDirty();
    }

    get lastName(): string {
        return this.attributes.last_name || '';
    }

    set lastName(value: string) {
        this.attributes.last_name = value;
        this.forceDirty();
    }

    get fullname(): string {
        return `${this.firstName} ${this.lastName}`.trim();
    }

    get initials(): string {
        return (this.firstName && this.lastName) ? this.firstName.substring(0, 1) + this.lastName.substring(0, 1) : this.email.substring(0, 2);
    }

    get avatar(): string {
        return this.attributes.avatar;
    }

    get phoneCode(): string {
        return this.attributes.phone_code;
    }

    set phoneCode(value: string) {
        this.attributes.phone_code = value;
    }

    get phoneNumber(): string {
        return this.attributes.phone_number;
    }

    set phoneNumber(value: string) {
        this.attributes.phone_number = value;
    }

    get parentPhoneNumber(): string {
        return this.attributes.parent_phone_number;
    }

    set parentPhoneNumber(value: string) {
        this.attributes.parent_phone_number = value;
        this.forceDirty();
    }

    get phone(): string {
        if (this.phoneNumber) {
            return `+${this.phoneCode} ${this.phoneNumber}`;
        }

        return '';
    }

    get gender(): EGenders {
        return this.attributes.gender;
    }

    set gender(value: EGenders) {
        this.attributes.gender = value;
        this.forceDirty();
    }

    get dob(): string {
        return this.attributes.dob;
    }

    set dob(value: string) {
        this.attributes.dob = value;
        this.forceDirty();
    }

    get dobHuman(): string {
        return moment(this.attributes.dob).format('YYYY-MM-DD')
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }

    get therapy(): TherapyJsonapiResource {
        return this.relationships.therapy.data;
    }

    get team(): TeamJsonapiResource | undefined {
        return this.relationships.team.data;
    }

    get school(): SchoolJsonapiResource | undefined {
        return this.relationships.school.data;
    }

    get goals(): Array<GoalJsonapiResource> {
        return this.relationships.goals.data.$toArray;
    }

    get ieps(): Array<IepJsonapiResource> {
        return this.relationships.ieps.data.$toArray
            .sort((
                a: IepJsonapiResource, b: IepJsonapiResource
            ) => (new Date(b.createdAt)).getTime() - (new Date(a.createdAt)).getTime());

    }

    get iepActual(): IepJsonapiResource | null {
        if (this.relationships.ieps.data.$toArray.length > 0) {
            return this.relationships.ieps.data.$toArray.sort((
                a: IepJsonapiResource, b: IepJsonapiResource
            ) => (new Date(b.dateActual)).getTime() - (new Date(a.dateActual)).getTime())[0];
        }
        return null;

    }

    get soaps(): Array<SoapJsonapiResource> {
        return this.relationships.soaps.data.$toArray;
    }

    get goalsSorted(): Array<GoalJsonapiResource> {
        return this.goals.sort((a: GoalJsonapiResource, b: GoalJsonapiResource) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());
    }

    get goalsSortedActual(): Array<GoalJsonapiResource> {
        return this.goalsSorted.filter((goal: GoalJsonapiResource): boolean => {
            return goal.iep.id === this.iepActual.id;
        });
    }

    get hasSources(): boolean {
        return this.relationships.sources.data.$length > 0;
    }
}
