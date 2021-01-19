import { ChangeDetectionStrategy, ChangeDetectorRef, Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { BaseDetachedComponent } from '../../../classes/abstracts/component/base-detached-component';
import { switchMap } from 'rxjs/operators';
import { EType, SyncLogJsonapiResource } from '../../../../resources/crm/sync-log/sync-log.jsonapi.service';
import { CrmService } from '../../../services/crm.service';
import { DataError } from '../../../classes/data-error';

@Component({
    selector: 'app-crm-button',
    templateUrl: './button.component.html',
    styleUrls: ['./button.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ButtonComponent extends BaseDetachedComponent implements OnInit {
    @Input() type: EType;
    @Output() synced: EventEmitter<void> = new EventEmitter<void>();

    public loaded: boolean = false;
    public connected: boolean = false;
    public syncing: boolean = false;
    public syncLog: SyncLogJsonapiResource | null = null;

    constructor(
        protected cdrRef: ChangeDetectorRef,
        protected crmService: CrmService
    ) {
        super(cdrRef);
    }

    ngOnInit(): void {
        this.crmService
            .loaded
            .subscribe((value: boolean) => {
                this.loaded = value;
                this.detectChanges();
            });

        this.crmService
            .connected
            .pipe(switchMap((value: boolean) => {
                this.connected = value;
                this.detectChanges();

                return this.crmService.syncLogs(this.type);
            }))
            .subscribe((data: Array<SyncLogJsonapiResource>) => {
                this.syncLog = data.length > 0 ? data[0] : null;
                this.detectChanges();
            });
    }

    sync(): void {
        this.syncing = true;
        this.detectChanges();

        this.crmService
            .sync(this.type)
            .pipe(switchMap(() => this.crmService.syncLogs(this.type)))
            .subscribe((data: Array<SyncLogJsonapiResource>) => {
                this.syncing = false;
                this.syncLog = data[0];
                this.synced.emit();

                this.detectChanges();
            }, (error: DataError) => {
                this.fallback(error);
            });
    }
}
