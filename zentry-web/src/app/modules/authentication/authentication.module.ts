import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthenticationRoutingModule } from './authentication-routing.module';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';
import { StandardComponent } from './login/standard/standard.component';
import { RegistrationComponent } from './registration/registration.component';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { LayoutComponent } from './layout/layout.component';
import { TokenComponent } from './login/token/token.component';
import { SocialComponent } from './social/social.component';
import { StandardCustomComponent } from './login/standard/standard.custom.component';
import { RegistrationCustomComponent } from './registration/registration.custom.component';
import { LayoutCustomComponent } from './layout/layout.custom.component';
import { AuthenticationSocialService } from './authentication.social.service';

@NgModule({
    declarations: [
        StandardComponent,
        StandardCustomComponent,
        RegistrationComponent,
        RegistrationCustomComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        LayoutComponent,
        LayoutCustomComponent,
        TokenComponent,
        SocialComponent,
    ],
    imports: [
        CommonModule,
        AuthenticationRoutingModule,
        ReactiveFormsModule,
        SharedModule
    ],
    providers: [
        AuthenticationSocialService
    ]
})
export class AuthenticationModule {
}
