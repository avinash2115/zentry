export interface IFilterable {
}

export interface IExcluded {
    getExcludedFilters(): Array<string>
}

export enum EStates {
    open = 'open',
    close = 'close'
}
