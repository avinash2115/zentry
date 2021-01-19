export interface IDataError {
    id: string,
    status: number,
    title: string,
    detail: string,
    meta: IDataErrorMeta,
}

export interface IDataErrorMeta {
    trace: string,
    source: IDataErrorMetaSource
}

export interface IDataErrorMetaSource {
    parameters: Array<any>,
    body: Array<any>
}

export class DataError extends Error {
    public id: string;
    public status: number;
    public title: string;
    public detail: string;
    public meta: IDataErrorMeta;

    constructor(id: string, status: number, title: string, detail: string, meta?: IDataErrorMeta) {
        super();
        this.message = title;
        this.id = id;
        this.status = status;
        this.title = title;
        this.detail = detail;
        this.meta = meta;
    }
}
