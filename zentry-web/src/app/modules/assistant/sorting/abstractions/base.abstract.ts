export enum EAttributeType {
    string,
    number,
    date
}

export enum EDirection {
    ASC,
    DESC
}

export interface ISorting {
    [value: string]: Object | Array<string>;
}

export interface ISortable {
    getSortableNamespace(): string;

    getSortableAttributes(): Array<ISortableAttribute>;

    getSortableRelations(): Array<ISortableRelation>;

    getSortableDefault(): ISortableAttribute | ISortableRelation;
}

export interface ISortableAttribute {
    label: string,
    column: string | Array<string>,
    type: EAttributeType,
    defaultDirection?: EDirection,
}

export interface ISortableRelation {
    label: string,
    type: EAttributeType
    path: ISortableRelationPath,
    defaultDirection?: EDirection,
}

export interface ISortableRelationPath {
    relation?: string,
    attributes?: Array<ISortableAttribute>,
    path?: ISortableRelationPath
}

export interface ISortBy {
    sortable: ISortableAttribute | ISortableRelation,
    sortableDirection: EDirection
}
