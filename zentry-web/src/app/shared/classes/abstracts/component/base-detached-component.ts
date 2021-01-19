import { ChangeDetectorRef } from '@angular/core';
import { BaseDestroyableComponent } from './base-destroyable-component';
import { SwalService } from '../../../services/swal.service';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';

export class BaseDetachedComponent extends BaseDestroyableComponent {
    private _loading: boolean = false;
    private _sending: boolean = false;

    constructor(
        protected cdrRef: ChangeDetectorRef,
    ) {
        super();
    }

    get applicationName(): string {
        return window.application.name;
    }

    terms(subject: string, upper: boolean = true): string {
        if (window.application.theme === 'zentry') {
            switch (subject) {
                case 'participant':
                    return upper ? 'Student' : 'student';
                case 'participants':
                    return upper ? 'Students' : 'students';
                default:
                    return '';
            }
        }

        switch (subject) {
            case 'participant':
                return upper ? 'Participant' : 'participant';
            case 'participants':
                return upper ? 'Participants' : 'participant';
            default:
                return '';
        }
    }

    get isLoading(): boolean {
        return this._loading;
    }

    get isSending(): boolean {
        return this._sending;
    }

    detectChanges(): void {
        this.cdrRef.detectChanges();
    }

    loadingTrigger(): void {
        if (!this._loading) {
            this._loading = true;
        }

        this.detectChanges();
    }

    loadingCompleted(): void {
        if (this._loading) {
            this._loading = false;
        }

        this.detectChanges();
    }

    sendingTrigger(): void {
        this._sending = true;
        this.detectChanges();
    }

    sendingCompleted(): void {
        this._sending = false;
        this.detectChanges();
    }

    quickStart(session?: SessionJsonapiResource): void {
        window.location.href = "zentrywidgetapp://" + (session instanceof SessionJsonapiResource ? 'quickstart_id='+session.id: '');
    }

    fallback(error: Error, title: string = 'Something went wrong', callback?: Function): void {
        this.detectChanges();

        SwalService
            .error({
                title: title,
                text: error.message
            })
            .then(() => {
                console.error(error);

                if (callback) {
                    callback();
                }
            });
    }

    log(e: any): void {
        console.log(e);
    }
}
