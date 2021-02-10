import { ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../classes/abstracts/component/base-detached-component';
import { filter, take } from 'rxjs/operators';

@Component({
    selector: 'app-date-today',
    templateUrl: './today.component.html',
    styleUrls: ['./today.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class TodayComponent extends BaseDetachedComponent implements OnInit {
    @Input() format: string = 'h:mm a EEEE, MMM d';

    public date: Date = new Date();
    private timer: any;

    constructor(
        cdr: ChangeDetectorRef
    ) {
        super(cdr);

        cdr.detach();
    }

    ngOnInit(): void {
        this.detectChanges();

        this._destroy$
            .pipe(filter((value: boolean) => value), take(1))
            .subscribe(() => {
                if (!!this.timer) {
                    clearInterval(this.timer);
                }
            });

        this.timer = setInterval(() => {
            this.date = new Date();
            this.detectChanges();
        }, 1000);
    }
}
