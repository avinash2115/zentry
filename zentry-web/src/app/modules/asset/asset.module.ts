import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../shared/shared.module';
import { AssetRoutingModule } from './asset-routing.module';
import { WidgetComponent } from './widget/widget.component';
import { WidgetCustomComponent } from './widget/widget.custom.component';

@NgModule({
    declarations: [
        WidgetComponent,
        WidgetCustomComponent
    ],
    imports: [
        CommonModule,
        SharedModule,
        AssetRoutingModule
    ]
})
export class AssetModule {
}
