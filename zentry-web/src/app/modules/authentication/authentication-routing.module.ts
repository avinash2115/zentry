import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { TokenComponent } from './login/token/token.component';
import { StandardCustomComponent } from './login/standard/standard.custom.component';
import { RegistrationCustomComponent } from './registration/registration.custom.component';
import { LayoutCustomComponent } from './layout/layout.custom.component';

const routes: Routes = [
    {
        path: '',
        component: LayoutCustomComponent,
        children: [
            {
                path: 'login',
                component: StandardCustomComponent
            },
            {
                path: 'login/token/:id',
                component: TokenComponent
            },
            {
                path: 'registration',
                component: RegistrationCustomComponent
            },
            {
                path: 'forgot',
                component: ForgotPasswordComponent
            },
            {
                path: 'forgot/:token',
                component: ResetPasswordComponent
            }
        ]
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class AuthenticationRoutingModule {
}
