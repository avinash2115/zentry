import { BrowserModule } from '@angular/platform-browser';
import { APP_INITIALIZER, NgModule } from '@angular/core';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { ResourcesModule } from './resources/resources.module';
import { NgxJsonapiModule } from '../vendor/vp-ngx-jsonapi';
import { JsonapiInterceptor } from './shared/interceptors/jsonapi.interceptor';
import { LayoutComponent } from './components/layout/layout.component';
import { HeaderComponent } from './components/header/header.component';
import { PresentationComponent } from './components/presentation/presentation.component';
import { UserModule } from './modules/user/user.module';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { SharedModule } from './shared/shared.module';
import { AgmCoreModule } from '@agm/core';
import { GoogleLoginProvider, SocialAuthServiceConfig, SocialLoginModule } from 'angularx-social-login';
import { HeaderCustomComponent } from './components/header/header.custom.component';
import { LayoutCustomComponent } from './components/layout/layout.custom.component';
import { PresentationCustomComponent } from './components/presentation/presentation.custom.component';
import { CalendarDateFormatter, CalendarModule, CalendarMomentDateFormatter, DateAdapter, MOMENT } from 'angular-calendar';
import { adapterFactory } from 'angular-calendar/date-adapters/moment';
import moment from 'moment';
import { NgCircleProgressModule } from 'ng-circle-progress';
import { ServiceModule } from './modules/service/service.module';
import { AssistantModule } from './modules/assistant/assistant.module';
import { TagInputModule } from 'ngx-chips';
import { SessionModule } from './modules/session/session.module';
import { IpcService } from './shared/services/ipc.service';
import { FormsModule } from '@angular/forms';
import { AuthenticationModule } from './modules/authentication/authentication.module';

declare global {
    interface Window {
        require: NodeRequire;
        helpers: {
            interval: (callback: Function, delay: number) => { clear: () => void },
        },
        application: {
            name: string,
            theme: string,
        },
        endpoints: {
            api: string,
            echo: string,
        },
        config: {
            production: boolean,
            native: boolean,
            services: {
                oAuth: {
                    google: {
                        clientId: string
                    }
                },
                agm: {
                    apiKey: string
                },
                kloudless: {
                    appId: string
                }
            },
        },
        assets: {
            widgets: {
                macOS: string,
                windows: string,
            }
        }
    }
}

export function momentAdapterFactory() {
    return adapterFactory(moment);
}

export function ipcInit(ipcService: IpcService) {
    return () => {
        return new Promise((resolve) => {
            ipcService.init();

            resolve();
        });
    }
}

@NgModule({
    declarations: [
        AppComponent,
        LayoutComponent,
        LayoutCustomComponent,
        HeaderComponent,
        HeaderCustomComponent,
        PresentationComponent,
        PresentationCustomComponent
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        HttpClientModule,
        BrowserAnimationsModule,
        NgxJsonapiModule.forRoot({
            url: window.endpoints.api
        }),
        AgmCoreModule.forRoot({
            apiKey: window.config.services.agm.apiKey,
            libraries: ['places']
        }),
        CalendarModule.forRoot(
            {
                provide: DateAdapter,
                useFactory: momentAdapterFactory
            },
            {
                dateFormatter: {
                    provide: CalendarDateFormatter,
                    useClass: CalendarMomentDateFormatter
                }
            }
        ),
        NgCircleProgressModule.forRoot({
            radius: 100,
            outerStrokeWidth: 4,
            outerStrokeColor: "#CDCEDE",
            animationDuration: 300,
        }),
        TagInputModule,
        SocialLoginModule,
        ResourcesModule,
        AuthenticationModule,
        AssistantModule,
        UserModule,
        SessionModule,
        ServiceModule,
        SharedModule,
        FormsModule,
    ],
    providers: [
        {
            provide: MOMENT,
            useValue: moment,
        },
        {
            provide: HTTP_INTERCEPTORS,
            useClass: JsonapiInterceptor,
            multi: true
        },
        {
            provide: 'SocialAuthServiceConfig',
            useValue: {
                autoLogin: false,
                providers: [
                    {
                        id: GoogleLoginProvider.PROVIDER_ID,
                        provider: new GoogleLoginProvider(
                            window.config.services.oAuth.google.clientId
                        )
                    }
                ]
            } as SocialAuthServiceConfig
        },
        {
            provide: APP_INITIALIZER,
            deps: [
                IpcService
            ],
            useFactory: ipcInit,
            multi: true,
        }
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}

