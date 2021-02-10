import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ListComponent } from './list/list.component';
import { CreateComponent } from './create/create.component';
import { ViewComponent } from './view/view.component';

const routes: Routes = [
    {
        path: '',
        children: [
            {
                path: '',
                component: ListComponent
            },
            {
                path: 'create',
                component: CreateComponent
            },
            {
                path: ':providerId',
                component: ViewComponent
            }
        ]
    },
    

];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class ProviderRoutingModule {
}
