import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../../shared/shared.module';
import { ReactiveFormsModule } from '@angular/forms';
import { FilterService } from './filter.service';
import { FilterComponent } from './filter.component';
import { ButtonComponent } from './controls/button/button.component';
import { DatepickerComponent } from './controls/datepicker/datepicker.component';
import { SelectComponent } from './controls/select/select.component';
import { ToolbarComponent } from './toolbar/toolbar.component';
import { NgSelectModule } from '@ng-select/ng-select';

@NgModule({
    imports: [
        CommonModule,
        SharedModule,
        ReactiveFormsModule,
        NgSelectModule,
    ],
    providers: [
        FilterService
    ],
    declarations: [
        FilterComponent,
        ButtonComponent,
        DatepickerComponent,
        SelectComponent,
        ToolbarComponent,
    ],
    exports: [
        FilterComponent,
        ButtonComponent,
        ToolbarComponent
    ]
})
export class FilterModule {
}
