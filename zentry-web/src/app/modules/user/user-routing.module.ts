import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ListComponent as DeviceListComponent } from './device/list/list.component';
import { ProfileComponent } from './profile/profile.component';
import { ListComponent as StorageListComponent } from './storage/list/list.component';
import { ListComponent as ParticipantListComponent } from './participant/list/list.component';
import { ViewComponent as ParticipantViewComponent } from './participant/view/view.component';
import { ListComponent as TeamListComponent } from './team/list/list.component';
import { ListCustomComponent } from './participant/list/list.custom.component';
import { CreateComponent as ParticipantCreateComponent } from './participant/create/create.component';
import { ViewCustomComponent } from './participant/view/view.custom.component';
import { CreateComponent as TeamSchoolCreateComponent } from './team/school/create/create.component';
import { ViewComponent as TeamSchoolViewComponent } from './team/school/view/view.component';
import { CreateComponent as TeamCreateComponent } from './team/create/create.component';
import { ViewComponent as TeamViewComponent } from './team/view/view.component';
import { ProfileCustomComponent } from './profile/profile.custom.component';

const routes: Routes = [
    {
        path: '',
        pathMatch: 'full',
        redirectTo: '/profile'
    },
    {
        path: 'profile',
        component: ProfileCustomComponent,
    },
    {
        path: 'devices',
        component: DeviceListComponent
    },
    {
        path: 'storage',
        component: StorageListComponent
    },
    {
        path: 'participants',
        children: [
            {
                path: '',
                component: ParticipantListComponent,
            },
            {
                path: ':participantId',
                component: ParticipantViewComponent,
            },
        ]
    },
    {
        path: 'students',
        children: [
            {
                path: '',
                component: ListCustomComponent,
            },
            {
                path: 'create',
                component: ParticipantCreateComponent,
            },
            {
                path: ':studentId',
                component: ViewCustomComponent,
            },
        ]
    },
    {
        path: 'districts',
        children: [
            {
                path: '',
                component: TeamListComponent,
            },
            {
                path: 'create',
                component: TeamCreateComponent,
            },
            {
                path: 'schools',
                children: [
                    {
                        path: 'create',
                        component: TeamSchoolCreateComponent,
                    },
                ]
            },
            {
                path: ':districtId',
                children: [
                    {
                        path: '',
                        component: TeamViewComponent,
                    },
                    {
                        path: 'schools',
                        children: [
                            {
                                path: 'create',
                                component: TeamSchoolCreateComponent,
                            },
                            {
                                path: ':schoolId',
                                component: TeamSchoolViewComponent,
                            }
                        ]
                    },
                ]
            },
        ]
    },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class UserRoutingModule {
}
