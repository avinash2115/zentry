import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FilterService } from '../../filter.service';
import { EStates } from '../../abstractions/base.abstract';
import { BaseDetachedComponent } from '../../../../../shared/classes/abstracts/component/base-detached-component';
import { takeUntil } from 'rxjs/operators';

@Component({
    selector: 'app-assistant-filter-controls-button',
    templateUrl: './button.component.html',
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ButtonComponent extends BaseDetachedComponent implements OnInit {
    private _state: EStates = EStates.close;

    constructor(
        public cdr: ChangeDetectorRef,
        public filterService: FilterService
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.filterService
            .state
            .pipe(
                takeUntil(this._destroy$)
            )
            .subscribe((value: EStates) => {
                this._state = value;
                this.detectChanges();
            });
    }

    toggle(): void {
        if (this._state === EStates.open) {
            this.filterService.stateChange(EStates.close)
        } else {
            this.filterService.stateChange(EStates.open)
        }
    }
}
