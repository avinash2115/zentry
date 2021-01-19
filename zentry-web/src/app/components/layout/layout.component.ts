import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../shared/classes/abstracts/component/base-detached-component';
import { LoaderService } from '../../shared/services/loader.service';
import { takeUntil } from 'rxjs/operators';
import { LayoutService } from '../../shared/services/layout.service';
import { combineLatest } from 'rxjs/internal/observable/combineLatest';

@Component({
    selector: 'app-layout',
    templateUrl: './layout.component.html',
    styleUrls: ['./layout.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class LayoutComponent extends BaseDetachedComponent implements OnInit {
    public showFullscreenLoader: boolean = false;
    public isPresentationVisible: boolean = false;
    public isContentWrapped: boolean = true;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.loaderService
            .state
            .pipe(takeUntil(this._destroy$))
            .subscribe((state: boolean) => {
                if (state) {
                    document.getElementsByTagName('body')[0].classList.add('locked');
                } else {
                    document.getElementsByTagName('body')[0].classList.remove('locked');
                }
                this.showFullscreenLoader = state;
                this.detectChanges();
            });

        combineLatest([
            this.layoutService.isPresentationVisible,
            this.layoutService.isContentWrapped,
        ]).pipe(takeUntil(this._destroy$))
            .subscribe(([presentation, content]: [boolean, boolean]) => {
                if (this.isPresentationVisible !== presentation) {
                    this.isPresentationVisible = presentation;
                    this.detectChanges();
                }

                if (this.isContentWrapped !== content) {
                    this.isContentWrapped = content;
                    this.detectChanges();
                }
            });
    }
}
