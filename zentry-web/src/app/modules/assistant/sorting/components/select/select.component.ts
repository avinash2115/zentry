import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    Input,
    OnChanges,
    OnInit,
    SimpleChange
} from '@angular/core';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { UtilsService } from '../../../../../shared/services/utils.service';
import { NgSelectComponent } from '@ng-select/ng-select';
import { throttleable } from '../../../../../shared/decorators/throttleable.decorator';
import { SortingService } from '../../sorting.service';
import {
    EAttributeType,
    EDirection,
    ISortable,
    ISortableAttribute,
    ISortableRelation,
    ISortBy
} from '../../abstractions/base.abstract';

@Component({
    selector: 'app-assistant-sorting-select',
    templateUrl: './select.component.html',
    styleUrls: ['./select.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class SelectComponent extends BaseDetachedComponent implements OnInit, OnChanges {
    @Input() sortable: ISortable
    @Input() default: ISortableAttribute | ISortableRelation;
    @Input() disabled: boolean = false;

    public sortables: Array<ISortBy>;
    public sortBy: ISortBy;
    private readonly typeLabels = {
        [EAttributeType.string]: {
            direction: {
                [EDirection.ASC]: 'A-Z',
                [EDirection.DESC]: 'Z-A'
            }
        },
        [EAttributeType.number]: {
            direction: {
                [EDirection.ASC]: 'From Oldest',
                [EDirection.DESC]: 'From Newest'
            }
        },
        [EAttributeType.date]: {
            direction: {
                [EDirection.ASC]: 'From Oldest',
                [EDirection.DESC]: 'From Newest'
            }
        }
    };

    constructor(
        private cdr: ChangeDetectorRef,
        private sortingService: SortingService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        const temporaryArray: Array<ISortBy> = [];
        this.sortable.getSortableAttributes().forEach((sortableAttribute: ISortableAttribute) => {
            this.directionsByType(sortableAttribute.type).forEach((direction: EDirection) => {
                temporaryArray.push({
                    sortable: sortableAttribute,
                    sortableDirection: direction
                });
            });
        });

        this.sortable.getSortableRelations().forEach((sortableRelation: ISortableRelation) => {
            this.directionsByType(sortableRelation.type).forEach((direction: EDirection) => {
                temporaryArray.push({
                    sortable: sortableRelation,
                    sortableDirection: direction
                });
            });
        });

        this.sortables = [...temporaryArray];

        if (this.sortingService.initialValue) {
            this.sortBy = this.sortables.find((item: ISortBy) => {
                let data: object;

                if (item.sortable.hasOwnProperty('path')) {
                    data = {
                        [this.sortable.getSortableNamespace()]: this.getSortableRelationObject(item)
                    };
                } else {
                    if (Array.isArray((item.sortable as ISortableAttribute).column)) {

                        data = {
                            [this.sortable.getSortableNamespace()]: ((item.sortable as ISortableAttribute).column as Array<string>).map((column: string) => {
                                return `${item.sortableDirection === EDirection.DESC ? '-' : ''}${column}`;
                            })
                        };
                    } else {
                        data = {
                            [this.sortable.getSortableNamespace()]: [
                                `${item.sortableDirection === EDirection.DESC ? '-' : ''}${(item.sortable as ISortableAttribute).column}`
                            ]
                        };
                    }
                }

                return UtilsService.deepObjectsCompare(this.sortingService.initialValue, data);
            });

            this.detectChanges();
        } else {
            if (this.default !== undefined) {
                const sortByValue: ISortBy = this.sortables.find((sortBy: ISortBy) => {
                    return sortBy.sortable.label === this.default.label &&
                        (this.default.defaultDirection ?
                                sortBy.sortableDirection === this.default.defaultDirection :
                                sortBy.sortableDirection === EDirection.ASC
                        );
                });

                this.sort(sortByValue);
            }
        }
    }

    ngOnChanges({disabled}: { disabled: SimpleChange }): void {
        if (disabled) {
            this.detectChanges();
        }
    }

    sort(value: ISortBy, ngSelectComponent?: NgSelectComponent): void {
        if (ngSelectComponent) {
            ngSelectComponent.close();
        }

        this.sortBy = value;
        this.detectChanges();

        if (this.sortBy.sortable.hasOwnProperty('path')) {
            this.sortingService.sortingChanged({
                [this.sortable.getSortableNamespace()]: this.getSortableRelationObject(this.sortBy)
            });
        } else {
            const sortable: ISortableAttribute = this.sortBy.sortable as ISortableAttribute;

            if (Array.isArray(sortable.column)) {
                this.sortingService.sortingChanged({
                    [this.sortable.getSortableNamespace()]: (sortable.column as Array<string>).map((column: string) => {
                        return `${this.sortBy.sortableDirection === EDirection.DESC ? '-' : ''}${column}`;
                    })
                });
            } else {
                this.sortingService.sortingChanged({
                    [this.sortable.getSortableNamespace()]: [
                        `${this.sortBy.sortableDirection === EDirection.DESC ? '-' : ''}${sortable.column}`
                    ]
                });
            }
        }
    }

    directionsByType(type: EAttributeType): Array<number> {
        return Object
            .keys(this.typeLabels[type].direction)
            .map((value: string) => parseInt(value, 0));
    }

    title(sortBy: ISortBy): string {
        return `${sortBy.sortable.label}: ${this.typeLabels[sortBy.sortable.type].direction[sortBy.sortableDirection]}`;
    }

    @throttleable(150)
    mouseover(): void {
        this.detectChanges();
    }

    private getSortableRelationObject(item: ISortBy): object {
        let sortablePath = (item.sortable as ISortableRelation).path;

        const event = {
            [sortablePath.relation]: {}
        };

        let current = event[sortablePath.relation];

        do {
            if (sortablePath.attributes) {
                current[0] = sortablePath.attributes.map((attribute: ISortableAttribute) => {
                    return `${item.sortableDirection === EDirection.DESC ? '-' : ''}${attribute.column}`;
                });
            }

            if (sortablePath.hasOwnProperty('path')) {
                if (sortablePath.path.relation) {
                    current[sortablePath.path.relation] = {};
                    current = current[sortablePath.path.relation];
                }

                sortablePath = sortablePath.path;
            } else {
                sortablePath = undefined;
            }
        } while (sortablePath !== undefined);

        return event;
    }
}
