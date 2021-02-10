import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

export class ProfileJsonapiService extends Service<ProfileJsonapiResource> {
    type = 'users_profile';
    resource = ProfileJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class ProfileJsonapiResource extends Resource {
    attributes: {
        first_name: string,
        last_name: string,
        phone_code: string,
        phone_number: string,
    };

    get firstName(): string {
        return this.attributes.first_name;
    }

    set firstName(value: string) {
        this.attributes.first_name = value;
    }

    get lastName(): string {
        return this.attributes.last_name;
    }

    set lastName(value: string) {
        this.attributes.last_name = value;
    }

    get fullname(): string {
        return `${this.firstName} ${this.lastName}`;
    }

    get initials(): string {
        return this.firstName.substring(0, 1) + this.lastName.substring(0, 1);
    }

    get phoneCode(): string {
        return this.attributes.phone_code
    }

    set phoneCode(value: string) {
        this.attributes.phone_code = value;
    }

    get phoneNumber(): string {
        return this.attributes.phone_number
    }

    set phoneNumber(value: string) {
        this.attributes.phone_number = value;
    }

    get phone(): string {
        if (this.phoneNumber) {
            return `+${this.phoneCode} ${this.phoneNumber}`;
        }

        return '';
    }
}
