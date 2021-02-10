import { NgModule } from '@angular/core';
import { SelectComponent } from './components/select/select.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgSelectModule } from '@ng-select/ng-select';
import { SortingService } from './sorting.service';

@NgModule({
    imports: [
        FormsModule,
        ReactiveFormsModule,
        NgSelectModule,
    ],
    providers: [
        SortingService,
    ],
    declarations: [
        SelectComponent
    ],
    exports: [
        SelectComponent
    ]
})
export class SortingModule {
}
