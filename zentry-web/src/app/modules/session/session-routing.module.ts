import { RouterModule, Routes } from '@angular/router';
import { NgModule } from '@angular/core';
import { ViewComponent } from './recorded/view/view.component';
import { ListCustomComponent } from './recorded/list/list.custom.component';
import { CalendarComponent } from './calendar/calendar.component';
import { ViewCustomComponent } from './recorded/view/view.custom.component';

const routes: Routes = [
    {
        path: '',
        children: [
            {
                path: '',
                pathMatch: 'full',
                redirectTo: 'recorded'
            },
            {
                path: 'calendar',
                children: [
                    {
                        path: '',
                        component: CalendarComponent
                    }
                ]
            },
            {
                path: 'recorded',
                children: [
                    {
                        path: '',
                        component: ListCustomComponent
                    },
                    {
                        path: ':recordedId',
                        component: ViewCustomComponent
                    },
                    {
                        path: ':recordedId/:poiId',
                        component: ViewComponent
                    }
                ]
            }
        ]
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class SessionRoutingModule {
}
