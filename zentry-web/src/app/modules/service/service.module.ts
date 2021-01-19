import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../shared/shared.module';
import { ServiceRoutingModule } from './service-routing.module';
import { ServiceService } from './service.service';
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
        ServiceRoutingModule,
        ReactiveFormsModule,
        NgSelectModule,
        AssistantModule
    ],
    providers: [
        ServiceService
    ]
})
export class ServiceModule {
}
