import { Injectable } from '@angular/core';
import { IpcMessageEvent, IpcRenderer } from 'electron';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class IpcService {
    private _ipc: IpcRenderer | undefined = void 0;
    private _deepLink$: BehaviorSubject<string | null> = new BehaviorSubject<string | null>(null);

    constructor() {
    }

    get deepLink(): Observable<string | null> {
        return this._deepLink$.asObservable();
    }

    init(): void {
        if (window.require) {
            try {
                this._ipc = window.require('electron').ipcRenderer;

                this.on('deeplink', (event: IpcMessageEvent, message: string) => {
                    console.log(message);
                    this._deepLink$.next(message);
                });
            } catch (e) {
                throw e;
            }
        } else {
            console.warn('Electron\'s IPC was not loaded');
        }
    }

    deepLinkHandled(value: string): void {
        if (this._deepLink$.value === value) {
            this._deepLink$.next(null);
        }
    }

    on(channel: string, listener: any): void {
        if (!this._ipc) {
            return;
        }

        this._ipc.on(channel, listener);
    }

    send(channel: string, ...args): void {
        if (!this._ipc) {
            return;
        }

        this._ipc.send(channel, ...args);
    }
}
