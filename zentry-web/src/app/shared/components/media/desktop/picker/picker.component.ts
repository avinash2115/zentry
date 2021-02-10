import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component, EventEmitter,
    OnChanges,
    OnInit,
    Output,
    SimpleChanges
} from '@angular/core';
import { BaseDetachedComponent } from '../../../../classes/abstracts/component/base-detached-component';
import { MediaService } from '../../../../services/media/media.service';
import { takeUntil } from 'rxjs/operators';
import DesktopCapturerSource = Electron.DesktopCapturerSource;

enum EOptions {
    none= 'none',
    audio = 'audio',
    all = 'all'
}

@Component({
    selector: 'app-media-desktop-picker',
    templateUrl: './picker.component.html',
    styleUrls: ['./picker.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class PickerComponent extends BaseDetachedComponent implements OnInit, OnChanges {
    @Output() isSelecting: EventEmitter<boolean> = new EventEmitter<boolean>(false);

    public sources: Array<DesktopCapturerSource> = [];

    public optionActive: EOptions = EOptions.all;
    public readonly optionsAvailable: typeof EOptions = EOptions;

    constructor(
        private _mediaService: MediaService,
        protected cdr: ChangeDetectorRef,
    ) {
        super(cdr);

        cdr.detach();
    }

    get hasSources(): boolean {
        return this.sources.length > 0;
    }

    ngOnInit(): void {
        const history: string | undefined = localStorage.getItem('picker_option_active');

        if (!!history) {
            this.optionSwitch(history as EOptions);
        }

        this._mediaService
            .desktopOptions
            .pipe(takeUntil(this._destroy$))
            .subscribe((options: Array<DesktopCapturerSource>) => {
                this.sources = options;

                this.isSelecting.emit(this.hasSources);

                this.detectChanges();
            });
    }

    ngOnChanges(changes: SimpleChanges): void {
        this.detectChanges();
    }

    select(option: DesktopCapturerSource): void {
        this._mediaService.selectDesktopOption(option);
    }

    cancel(): void {
        this._mediaService.selectDesktopOption(null);
    }

    optionIsActive(option: EOptions): boolean {
        return this.optionActive === option;
    }

    optionSwitch(option: EOptions): void {
        if (this.optionActive === option) {
            return;
        }

        this.optionActive = option;

        localStorage.setItem('picker_option_active', this.optionActive);

        switch (this.optionActive) {
            case EOptions.all:
                this._mediaService.toggleInitialSelection(true, true);
                break;
            case EOptions.audio:
                this._mediaService.toggleInitialSelection(true, false);
                break;
            case EOptions.none:
                this._mediaService.toggleInitialSelection(false, false);
                break;
        }

        this.detectChanges();
    }
}
