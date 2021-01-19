export class JsonapiConfig {
    public url: string = 'http://yourdomain/api/v1/';
    public params_separator? = '?';
    public unify_concurrency? = true;
    public cache_prerequests? = true;
    public cachestore_support? = true;
    public parameters? = {
        page: {
            number: 'page[number]',
            size: 'page[size]'
        },
        sort: 'sort',
        sortBy: 'sort_by',
        search: 'term',
        filterBy: {
            elastic: 'filter[elastic]'
        },
        pagination: {
            page: 'filter[pagination][page]',
            limit: 'filter[pagination][limit]'
        }
    };
}
