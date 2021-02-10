export enum EFilterTypes {
    select = 'select',
    datepicker = 'datepicker',
}

export interface IFilter {
    attribute: string,
    type: EFilterTypes,
    values: Array<{ label: string, value: string }>,
    label: string,
    weight?: number
}
