import { Resource, Service } from '../../../../../vendor/vp-ngx-jsonapi';
import { EResponseAction } from '../../pagination/abstractions/base.abstract';
import { ISortable, ISortableAttribute, ISortableRelation, ISorting } from '../../sorting/abstractions/base.abstract';

export enum EStates {
    hasItems = 'has_items',
    blank = 'blank',
    blankBySearch = 'blank_by_search',
    blankByFilters = 'blank_by_filters',
    blankByFiltersSearch = 'blank_by_filters_search'
}

export interface IServicePath {
    beforepath?: string,
    afterpath?: string
}

export interface IParameters {
    action: EResponseAction,
    silent: boolean,
    filterBy: {
        emit: boolean,
        data: object,
    },
    sortBy: {
        emit: boolean,
        data: ISorting
    },
    term: {
        emit: boolean,
        data: string,
    },
    page: {
        emit: boolean,
        data: number
    }
}

export interface IToolbarParameters {
    sortBy: {
        emit: boolean,
        data: ISorting
    }
}

export abstract class BaseList<R extends Resource = Resource> extends Service<R> implements ISortable {
    getSortableNamespace(): string {
        return '';
    }

    getSortableAttributes(): Array<ISortableAttribute> {
        return undefined;
    }

    getSortableRelations(): Array<ISortableRelation> {
        return undefined;
    }

    getSortableDefault(): ISortableAttribute | ISortableRelation {
        return undefined;
    }
}
