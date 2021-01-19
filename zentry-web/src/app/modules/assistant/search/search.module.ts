import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { LocalComponent } from './local/local.component';
import { SearchService } from './search.service';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    declarations: [
        LocalComponent
    ],
    providers: [
        SearchService
    ],
    imports: [
        FormsModule,
        CommonModule,
        RouterModule,
        SharedModule
    ],
    exports: [
        LocalComponent
    ]
})

export class SearchModule {
}
