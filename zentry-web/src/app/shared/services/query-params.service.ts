import { Injectable } from '@angular/core';
import { ActivatedRoute, Params, Router } from '@angular/router';
import { ObjectFlatten } from '../classes/object-flatten';
import { Observable, Observer } from 'rxjs';

const SORTING_KEY: string = 'sorting';

@Injectable()
export class QueryParamsService {

    constructor(
        private router: Router,
        private route: ActivatedRoute
    ) {
    }

    init(): Observable<object> {
        return new Observable((observer: Observer<object>) => {
            const originalQueryParams: Params = Object.assign({}, this.route.snapshot.queryParams);

            const sortingKey: string | undefined = Object.keys(originalQueryParams).find((key: string) => key.indexOf(SORTING_KEY) !== -1);

            if (sortingKey !== undefined && !Array.isArray(originalQueryParams[sortingKey])) {
                originalQueryParams[sortingKey] = [originalQueryParams[sortingKey]];
            }

            const resultingQueryParams: Params = ObjectFlatten.unflatten(originalQueryParams, {object: true, safe: true});

            observer.next(resultingQueryParams);
            observer.complete();
        });
    }

    update(filterBy: object, sortBy: object, term: string, page: number): Promise<boolean> {
        const params: object = ObjectFlatten.flatten({filters: filterBy, [SORTING_KEY]: sortBy, term: term, page: page}, {safe: true});

        return this.router.navigate([], {
            relativeTo: this.route,
            queryParams: params,
            queryParamsHandling: ''
        });
    }
}
