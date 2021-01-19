import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { takeUntil } from 'rxjs/operators';
import { BaseDetachedComponent } from '../../../../../../shared/classes/abstracts/component/base-detached-component';
import { ListService } from '../../../list.service';
import { EStates } from '../../../abstractions/base.abstract';

@Component({
    selector: 'app-assistant-list-states',
    templateUrl: './state.component.html',
    styleUrls: ['./state.component.scss']
})
export class StateComponent extends BaseDetachedComponent implements OnInit {
    public activeState: EStates = EStates.hasItems;
    public readonly states: typeof EStates = EStates;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected listService: ListService
    ) {
        super(cdr);

        this.cdr.detach();
    }

    ngOnInit(): void {
        this.listService
            .state
            .pipe(takeUntil(this._destroy$))
            .subscribe((state: EStates) => {
                this.activeState = state;
                this.detectChanges();
            });
    }

    reset(): void {
        this.listService.resetQueryParams();
    }
}
