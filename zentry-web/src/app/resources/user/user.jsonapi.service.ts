import { ISchema, Resource, Service } from '../../../vendor/vp-ngx-jsonapi';
import { PoiJsonapiResource } from './poi/poi.jsonapi.service';
import { BacktrackJsonapiResource } from './backtrack/backtrack.jsonapi.service';
import { ProfileJsonapiResource } from './profile/profile.jsonapi.service';

export class UserJsonapiService extends Service<UserJsonapiResource> {
    type = 'users';
    path = 'users';
    resource = UserJsonapiResource;
    schema: ISchema = {
        relationships: {
            profile: {
                hasMany: false,
                alias: 'users_profile'
            },
            poi: {
                hasMany: false,
                alias: 'users_poi'
            },
            backtrack: {
                hasMany: false,
                alias: 'users_backtrack'
            }
        }
    };

    constructor() {
        super();
        this.register();
    }
}

export class UserJsonapiResource extends Resource {
    attributes: {
        email: string,
        factory_password: boolean,
        password?: string,
        password_repeat?: string
    };

    relationships: {
        profile: {
            data: ProfileJsonapiResource,
            content: 'resource'
        },
        poi: {
            data: PoiJsonapiResource,
            content: 'resource'
        },
        backtrack: {
            data: BacktrackJsonapiResource,
            content: 'resource'
        }
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
        this._dirty = true;
    }

    get isFactoryPassword(): boolean {
        return this.attributes.factory_password;
    }

    set isFactoryPassword(factoryPassword: boolean) {
        this.attributes.factory_password = factoryPassword;
    }

    set password(password: string) {
        this.attributes.password = password;
        this._dirty = true;
    }

    set passwordRepeat(passwordRepeat: string) {
        this.attributes.password_repeat = passwordRepeat;
    }

    get initials(): string {
        return this.profile.initials;
    }

    get profile(): ProfileJsonapiResource {
        return this.relationships.profile.data;
    }

    get poi(): PoiJsonapiResource {
        return this.relationships.poi.data;
    }

    get backtrack(): BacktrackJsonapiResource {
        return this.relationships.backtrack.data;
    }
}
