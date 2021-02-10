import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../shared/shared.module';
import { ProviderRoutingModule } from './providers-routing.module';
import { ProviderService } from './providers.service';
import { ReactiveFormsModule } from '@angular/forms';
import { NgSelectModule } from '@ng-select/ng-select';
import { CreateComponent } from './create/create.component';
import { ListComponent } from './list/list.component';
import { ViewComponent } from './view/view.component';
import { AssistantModule } from '../assistant/assistant.module';

@NgModule({
    declarations: [
        ListComponent,
        CreateComponent,
        ViewComponent
    ],
    imports: [
        CommonModule,
        SharedModule,
        ProviderRoutingModule,
        ReactiveFormsModule,
        NgSelectModule,
        AssistantModule
    ],
    providers: [
        ProviderService
    ]
})
export class ProviderModule {
}
