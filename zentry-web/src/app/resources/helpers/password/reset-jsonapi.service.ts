import { Injectable } from '@angular/core';
import { Resource, Service } from '../../../../vendor/vp-ngx-jsonapi';

@Injectable()
export class PasswordResetJsonapiService extends Service<PasswordResetJsonapiResource> {
    type = 'password_resets';
    path = 'password_reset';
    resource = PasswordResetJsonapiResource;

    constructor() {
        super();
        this.register();
    }
}

export class PasswordResetJsonapiResource extends Resource {
    attributes: {
        email: string,
        password: string,
        password_repeat: string,
        created_at: string
    };

    get email(): string {
        return this.attributes.email;
    }

    set email(email: string) {
        this.attributes.email = email;
    }

    get password(): string {
        return this.attributes.password;
    }

    set password(password: string) {
        this.attributes.password = password;
    }

    get passwordRepeat(): string {
        return this.attributes.password_repeat;
    }

    set passwordRepeat(password: string) {
        this.attributes.password_repeat = password;
    }

    get createdAt(): string {
        return this.attributes.created_at;
    }
}
