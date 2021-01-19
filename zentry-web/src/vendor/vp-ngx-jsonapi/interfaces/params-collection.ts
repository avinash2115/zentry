import { IParams } from './params';
import { IPage } from './page';

export interface IParamsCollection extends IParams {
    localfilter?: object;
    remotefilter?: object;
    smartfilter?: object;
    page?: IPage;
    pagination?: {
        page: number,
        limit: number
    }
    storage_ttl?: number;
    sort?: string,
    sortBy?: object,
    sortByReorderKey?: string,
    filterBy?: object,
    search?: string,
    cachehash?: string; // solution for when we have different resources with a same id
}
