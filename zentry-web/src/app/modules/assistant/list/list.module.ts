import { NgModule } from '@angular/core';
import { ListComponent } from './components/list/list.component';
import { StateComponent } from './components/list/state/state.component';
import { ListHeaderTemplateDirective } from './directives/list-header-template.directive';
import { ListBodyTemplateDirective } from './directives/list-body-template.directive';
import { ListBodyStatesDirective } from './directives/list-body-states.directive';
import { CommonModule } from '@angular/common';
import { SearchModule } from '../search/search.module';
import { FilterModule } from '../filter/filter.module';
import { SortingModule } from '../sorting/sorting.module';
import { PaginationModule } from '../pagination/pagination.module';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    declarations: [
        ListComponent,
        StateComponent,
        ListHeaderTemplateDirective,
        ListBodyTemplateDirective,
        ListBodyStatesDirective
    ],
    imports: [
        CommonModule,
        PaginationModule,
        SearchModule,
        SortingModule,
        FilterModule,
        SharedModule,
    ],
    exports: [
        ListComponent,
        StateComponent,
        ListHeaderTemplateDirective,
        ListBodyTemplateDirective,
        ListBodyStatesDirective
    ]
})
export class ListModule {
}
