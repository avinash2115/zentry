import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component, EventEmitter,
    Input,
    OnChanges,
    OnInit, Output,
    SimpleChange
} from '@angular/core';
import { BaseDetachedComponent } from '../../../classes/abstracts/component/base-detached-component';
import { filter, take } from 'rxjs/operators';
import { UtilsService } from '../../../services/utils.service';

@Component({
    selector: 'app-date-timer',
    templateUrl: './timer.component.html',
    styleUrls: ['./timer.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class TimerComponent extends BaseDetachedComponent implements OnInit, OnChanges {
    @Input() hours: boolean = false;
    @Input() from: number = 0;
    @Output() durationChanged: EventEmitter<number> = new EventEmitter<number>();

    private duration: number = 0;
    private startedAt: Date = new Date();
    private timer: any;

    constructor(
        cdr: ChangeDetectorRef
    ) {
        super(cdr);
        cdr.detach();
    }

    get durationHuman(): string {
        return UtilsService.msHuman(this.duration, this.hours);
    }

    ngOnInit(): void {
        if (this.from) {
            this.startedAt.setTime(this.from);
            this.duration = (new Date()).getTime() - this.startedAt.getTime();
            this.durationChanged.emit(this.duration);
        }

        this.detectChanges();

        this._destroy$
            .pipe(filter((value: boolean) => value), take(1))
            .subscribe(() => {
                if (!!this.timer) {
                    clearInterval(this.timer);
                    this.durationChanged.complete();
                }
            });

        this.timer = setInterval(() => {
            this.duration = (new Date()).getTime() - this.startedAt.getTime();
            this.durationChanged.emit(this.duration);
            this.detectChanges();
        }, 1000);
    }

    ngOnChanges({from}: { from: SimpleChange }): void {
        if (from && from.currentValue) {
            this.startedAt.setTime(from.currentValue);
            this.duration = (new Date()).getTime() - this.startedAt.getTime();
            this.detectChanges();
        }
    }
}
