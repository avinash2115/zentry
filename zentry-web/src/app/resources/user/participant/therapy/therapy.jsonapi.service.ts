import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';

export enum EFrequencies {
    daily = 'daily',
    weekly = 'weekly',
    monthly = 'monthly'
}

export class TherapyJsonapiService extends Service<TherapyJsonapiResource> {
    type = 'users_participants_therapies';
    path = 'therapy';
    resource = TherapyJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class TherapyJsonapiResource extends Resource {
    attributes: {
        diagnosis: string,
        frequency: EFrequencies,
        sessions_amount_planned: number,
        treatment_amount_planned: number,
        notes: string,
        private_notes: string,
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

    get diagnosis(): string {
        return this.attributes.diagnosis;
    }

    set diagnosis(value: string) {
        this.attributes.diagnosis = value;
        this.forceDirty();
    }

    get frequency(): EFrequencies {
        return this.attributes.frequency;
    }

    set frequency(value: EFrequencies) {
        this.attributes.frequency = value;
        this.forceDirty();
    }

    get sessionsAmountPlanned(): number {
        return this.attributes.sessions_amount_planned;
    }

    set sessionsAmountPlanned(value: number) {
        this.attributes.sessions_amount_planned = value;
        this.forceDirty();
    }

    get treatmentAmountPlanned(): number {
        return this.attributes.treatment_amount_planned;
    }

    set treatmentAmountPlanned(value: number) {
        this.attributes.treatment_amount_planned = value;
        this.forceDirty();
    }

    get notes(): string {
        return this.attributes.notes;
    }

    set notes(value: string) {
        this.attributes.notes = value;
        this.forceDirty();
    }

    get privateNotes(): string {
        return this.attributes.private_notes;
    }

    set privateNotes(value: string) {
        this.attributes.private_notes = value;
        this.forceDirty();
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }

    get updatedAt(): string {
        return this.attributes.updated_at;
    }
}
