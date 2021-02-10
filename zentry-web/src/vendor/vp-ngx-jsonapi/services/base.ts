import { ICollection, IParamsResource, ISchema } from '../interfaces';
import { Page } from './page';
import { Resource } from '../resource';
import { IFilter } from '../interfaces/IFilter';

export class Base {
    public static Params: /* IParamsCollection | */ IParamsResource = {
        id: '',
        include: []
    };

    public static Schema: ISchema = {
        relationships: {},
        ttl: 0
    };

    public static newCollection<R extends Resource = Resource>(): ICollection<R> {
        return Object.defineProperties(
            {},
            {
                $length: {
                    get: function () {
                        return Object.keys(this).length;
                    },
                    enumerable: false
                },
                $toArray: {
                    get: function () {
                        return Object.keys(this).map(key => {
                            return this[key];
                        });
                    },
                    enumerable: false
                },
                $is_loading: {
                    value: false,
                    enumerable: false,
                    writable: true
                },
                $source: {value: '', enumerable: false, writable: true},
                $cache_last_update: {
                    value: 0,
                    enumerable: false,
                    writable: true
                },
                meta: {
                    enumerable: false,
                    writable: true
                },
                filters: {
                    get: function (): Array<IFilter> {
                        if (this.hasOwnProperty('meta') && this.meta && this.meta.hasOwnProperty('filters')) {
                            return this.meta.filters || [];
                        }
                        return [];
                    },
                    enumerable: false
                },
                pagination: {value: new Page(), enumerable: false, writable: true}
            }
        );
    }

    public static isObjectLive(ttl: number, last_update: number) {
        return (ttl >= 0 && Date.now() <= (last_update + ttl * 1000));
    }

    public static forEach<T extends { [keyx: string]: any }>(
        collection: T,
        fc: (object: any, key?: string | number) => void
    ): void {
        Object.keys(collection).forEach(key => {
            fc(collection[key], key);
        });
    }
}
