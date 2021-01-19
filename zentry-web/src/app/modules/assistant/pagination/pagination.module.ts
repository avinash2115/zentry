import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PaginationComponent } from './pagination/pagination.component';
import { PaginationService } from './pagination.service';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    declarations: [
        PaginationComponent
    ],
    providers: [
        PaginationService
    ],
    imports: [
        CommonModule,
        SharedModule
    ],
    exports: [
        PaginationComponent
    ]
})
export class PaginationModule {
}
