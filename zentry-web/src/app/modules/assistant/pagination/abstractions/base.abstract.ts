export enum EResponseAction {
    reload = 'reload',
    prepend = 'prepend',
    append = 'append'
}

export enum EScrollActions {
    saveScrollPosition = 'save_scroll_position',
    restoreScrollPosition = 'restore_scroll_position'
}

export interface IBagState {
    limit: number,
    page: number,
    pages: Array<number>
}

export interface IMetaState {
    page: number
}

export interface IPageState {
    page: number,
    emit: boolean,
    silent: boolean,
    action: EResponseAction
}
