import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardRoutingModule } from './dashboard-routing.module';
import { MainComponent } from './main/main.component';
import { SessionModule } from '../session/session.module';
import { SharedModule } from '../../shared/shared.module';
import { MainCustomComponent } from './main/main.custom.component';

@NgModule({
    declarations: [
        MainComponent,
        MainCustomComponent
    ],
    imports: [
        CommonModule,
        DashboardRoutingModule,
        SessionModule,
        SharedModule
    ]
})
export class DashboardModule {
}
