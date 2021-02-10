import { Resource } from '../resource';
import { IPage } from './page';
import { IDataResource } from './data-resource';
import { IFilter } from './IFilter';

export interface ICollection<R extends Resource = Resource> extends Array<Resource> {
    $length: number;
    $toArray: Array<R>;
    $is_loading: boolean;
    $source: 'new' | 'memory' | 'store' | 'server';
    $cache_last_update: number;
    meta: {
        filters?: Array<IFilter>,
        pagination?: {
            total: number
        }
    },
    filters?: Array<IFilter>;
    data: Array<IDataResource>; // this need disapear is for datacollection
    pagination: IPage;
}
