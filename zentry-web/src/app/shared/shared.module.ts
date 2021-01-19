import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ControlErrorComponent } from './components/form/control/error.component';
import { SignaturePadComponent } from './components/form/signature-pad/signature-pad.component';
import { ControlErrorDirective } from './directives/form/control/error.directive';
import { ControlErrorContainerDirective } from './directives/form/control/error-container.directive';
import { FormSubmitDirective } from './directives/form/submit.directive';
import { LoaderComponent } from './components/loader/loader.component';
import { FaIconComponent, FaIconLibrary, FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { MediaService } from './services/media/media.service';
import { UploadService } from './services/media/upload.service';
import { fab } from '@fortawesome/free-brands-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { TodayComponent as DateTodayComponent } from './components/date/today/today.component';
import { TimerComponent as DateTimerComponent } from './components/date/timer/timer.component';
import { WaveformComponent as MediaAudioWaveformComponent } from './components/media/audio/waveform/waveform.component';
import { PickerComponent as MediaDesktopPickerComponent } from './components/media/desktop/picker/picker.component';
import { NgbDropdownModule, NgbModalModule, NgbTooltipModule } from '@ng-bootstrap/ng-bootstrap';
import { QueryParamsService } from './services/query-params.service';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { NgxMaskModule } from 'ngx-mask';
import { ButtonComponent } from './components/crm/button/button.component';

@NgModule({
    declarations: [
        // region Components
        LoaderComponent,
        ControlErrorComponent,
        DateTodayComponent,
        DateTimerComponent,
        MediaAudioWaveformComponent,
        MediaDesktopPickerComponent,
        ButtonComponent,
        SignaturePadComponent,
        // endregion

        // region Directives
        ControlErrorDirective,
        ControlErrorContainerDirective,
        FormSubmitDirective,
        // endregion
    ],
    exports: [
        // region Components
        FaIconComponent,
        LoaderComponent,
        DateTodayComponent,
        DateTimerComponent,
        MediaAudioWaveformComponent,
        MediaDesktopPickerComponent,
        ButtonComponent,
        SignaturePadComponent,
        // endregion

        // region Directives
        ControlErrorDirective,
        ControlErrorContainerDirective,
        FormSubmitDirective,
        // endregion

        // region Modules
        NgbDropdownModule,
        NgbTooltipModule,
        NgbModalModule,
        PerfectScrollbarModule,
        BsDatepickerModule,
        NgxMaskModule,
        // endregion
    ],
    imports: [
        CommonModule,
        FontAwesomeModule,
        NgbDropdownModule,
        NgbTooltipModule,
        NgbModalModule,
        PerfectScrollbarModule,
        BsDatepickerModule.forRoot(),
        NgxMaskModule.forRoot(),
    ],
    providers: [
        QueryParamsService,
        MediaService,
        UploadService,
    ],
    entryComponents: [
        ControlErrorComponent
    ]
})
export class SharedModule {
    constructor(library: FaIconLibrary) {
        library.addIconPacks(fas);
        library.addIconPacks(fab);
        library.addIconPacks(far);
    }
}
