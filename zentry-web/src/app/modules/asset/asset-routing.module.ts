import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WidgetCustomComponent } from './widget/widget.custom.component';

const routes: Routes = [
    {
        path: '',
        pathMatch: 'full',
        redirectTo: '/widgets'
    },
    {
        path: 'widgets',
        component: WidgetCustomComponent
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class AssetRoutingModule {
}
