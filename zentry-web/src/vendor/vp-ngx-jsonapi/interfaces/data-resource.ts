import { IAttributes } from './attributes';
import { ILinks } from './links';

export interface IDataResource {
    type: string;
    id?: string;
    attributes?: IAttributes;
    relationships?: object;
    links?: ILinks;
    meta?: object;
    path?: string;
}
