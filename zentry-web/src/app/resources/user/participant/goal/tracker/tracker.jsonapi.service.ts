import { Resource, Service } from '../../../../../../vendor/vp-ngx-jsonapi';
import { v4 as uuidv4 } from 'uuid';

export enum ETypes {
    positive = 'positive',
    negative = 'negative',
    neutral = 'neutral'
}

export enum EIcons {
    yes = 'check-circle',
    no = 'times-circle',
    assist = 'life-ring'
}

export enum EColors {
    positive = '#28a745',
    negative = '#dc3545',
    neutral = '#ffc107'
}

export interface ITypes {
    value: ETypes,
    label: string,
}

export const Types: Array<ITypes> = [
    {
        value: ETypes.positive,
        label: 'Positive'
    },
    {
        value: ETypes.negative,
        label: 'Negative'
    },
    {
        value: ETypes.neutral,
        label: 'Neutral'
    }
]

export class TrackerJsonapiService extends Service<TrackerJsonapiResource> {
    type = 'users_participants_goals_trackers';
    path = 'trackers';
    resource = TrackerJsonapiResource;

    constructor() {
        super();
        this.register();
    }

    get default(): Array<TrackerJsonapiResource> {
        const a: TrackerJsonapiResource = this.new();

        a.id = uuidv4();
        a.name = 'Yes';
        a.trackerType = ETypes.positive;
        a.icon = EIcons.yes;
        a.color = EColors.positive;

        const b: TrackerJsonapiResource = this.new();
        b.id = uuidv4();
        b.name = 'No';
        b.trackerType = ETypes.negative;
        b.icon = EIcons.no;
        b.color = EColors.negative;

        const c: TrackerJsonapiResource = this.new();
        c.id = uuidv4();
        c.name = 'Assist';
        c.trackerType = ETypes.neutral;
        c.icon = EIcons.assist;
        c.color = EColors.neutral;

        return [a, b, c];
    }
}

export class TrackerJsonapiResource extends Resource {
    attributes: {
        name: string,
        type: ETypes,
        icon: EIcons,
        color: EColors,
        created_at: string,
        updated_at: string,
    };

    private _dirty: boolean = false;

    get dirty(): boolean {
        return this._dirty;
    }

    forceDirty(): void {
        this._dirty = true;
    }

    get name(): string {
        return this.attributes.name;
    }

    set name(value: string) {
        this.attributes.name = value;
        this.forceDirty();
    }

    get trackerType(): ETypes {
        return this.attributes.type;
    }

    set trackerType(value: ETypes) {
        this.attributes.type = value;
        this.forceDirty();
    }

    get isTrackerTypePositive(): boolean {
        return this.trackerType === ETypes.positive;
    }

    get icon(): EIcons {
        return this.attributes.icon;
    }

    set icon(value: EIcons) {
        this.attributes.icon = value;
        this.forceDirty();

        switch (this.attributes.icon) {
            case EIcons.yes:
                this.trackerType = ETypes.positive;
                this.color = EColors.positive;
                break;
            case EIcons.no:
                this.trackerType = ETypes.negative;
                this.color = EColors.negative;
                break;
            case EIcons.assist:
                this.color = EColors.neutral;
                break;
        }
    }

    get color(): EColors {
        return this.attributes.color;
    }

    set color(value: EColors) {
        this.attributes.color = value;
        this.forceDirty();
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
