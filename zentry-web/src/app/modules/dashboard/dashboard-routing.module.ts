import { RouterModule, Routes } from '@angular/router';
import { NgModule } from '@angular/core';
import { MainCustomComponent } from './main/main.custom.component';

const routes: Routes = [
    {
        path: '',
        component: MainCustomComponent
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class DashboardRoutingModule {
}
