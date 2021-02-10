import { OnDestroy } from '@angular/core';
import { Subject } from 'rxjs';

export class BaseDestroyableComponent implements OnDestroy {
    protected readonly _destroy$: Subject<boolean> = new Subject<boolean>();

    ngOnDestroy(): void {
        this._destroy$.next(true);
        this._destroy$.complete();
    }
}
