import { NgModule } from '@angular/core';
import { SortingModule } from './sorting/sorting.module';
import { SearchModule } from './search/search.module';
import { PaginationModule } from './pagination/pagination.module';
import { FilterModule } from './filter/filter.module';
import { ListModule } from './list/list.module';

@NgModule({
    imports: [
        PaginationModule,
        SearchModule,
        SortingModule,
        FilterModule,
        ListModule,
    ],
    providers: [],
    declarations: [],
    exports: [
        PaginationModule,
        SearchModule,
        SortingModule,
        FilterModule,
        ListModule,
    ]
})
export class AssistantModule {
}
