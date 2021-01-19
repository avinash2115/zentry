import { IParams } from './params';

export interface IParamsResource extends IParams {
    id?: string;
    preserveRelationships?: boolean;
    applyPathToResource?: boolean;
    pathWithoutId?: boolean;
    fullPath?: string;
}
