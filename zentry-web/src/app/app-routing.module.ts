import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './shared/guards/auth.guard';
import { WidgetCustomComponent as SessionWidgetCustomComponent } from './modules/session/widget/widget.custom.component';
import { GuestGuard } from './shared/guards/guest.guard';
import { LayoutCustomComponent } from './components/layout/layout.custom.component';

const routes: Routes = [
    {
        path: '',
        component: LayoutCustomComponent,
        canActivate: [AuthGuard],
        canActivateChild: [AuthGuard],
        children: [
            {
                path: '',
                pathMatch: 'full',
                redirectTo: 'dashboard'
            },
            {
                path: 'assets',
                loadChildren: () => import('./modules/asset/asset.module').then((m) => m.AssetModule)
            },
            {
                path: 'dashboard',
                loadChildren: () => import('./modules/dashboard/dashboard.module').then((m) => m.DashboardModule)
            },
            {
                path: 'user',
                loadChildren: () => import('./modules/user/user.module').then((m) => m.UserModule)
            },
            {
                path: 'session',
                loadChildren: () => import('./modules/session/session.module').then((m) => m.SessionModule)
            },
            {
                path: 'service',
                loadChildren: () => import('./modules/service/service.module').then((m) => m.ServiceModule)
            },
            {
                path: 'provider',
                loadChildren: () => import('./modules/provider/providers.module').then((m) => m.ProviderModule)
            }
        ]
    },
    {
        path: 'session/widget',
        component: SessionWidgetCustomComponent,
        canActivate: [AuthGuard],
        canActivateChild: [AuthGuard]
    },
    {
        path: 'auth',
        canActivate: [GuestGuard],
        canActivateChild: [GuestGuard],
        loadChildren: () => import('./modules/authentication/authentication.module').then((m) => m.AuthenticationModule)
    },
    {
        path: 'shared',
        loadChildren: () => import('./modules/shared/shared.module').then((m) => m.SharedModule)
    },
    {
        path: '**',
        redirectTo: '/dashboard'
    }
];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
