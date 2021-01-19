import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LinkComponent } from './link/link.component';
import { SharedModule as GlobalSharedModule } from '../../shared/shared.module';
import { SharedRoutingModule } from './shared-routing.module';

@NgModule({
    declarations: [
        LinkComponent
    ],
    imports: [
        CommonModule,
        GlobalSharedModule,
        SharedRoutingModule
    ]
})
export class SharedModule {
}
