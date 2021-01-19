import { enableProdMode } from '@angular/core';
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { AppModule } from './app/app.module';

window.config.native = /electron/i.test(navigator.userAgent);

window.helpers.interval = function (callback: Function, delay: number) {
    const requestAnimationFrame = window.requestAnimationFrame;
    let start: number = Date.now();
    let stop: number;
    const intervalFunc = () => {
        Date.now() - start < delay || (start += delay, callback());
        stop || requestAnimationFrame(intervalFunc)
    }
    requestAnimationFrame(intervalFunc);
    return {
        clear: function () {
            stop = 1
        }
    }
}

if (window.config.production) {
    enableProdMode();
}

platformBrowserDynamic()
    .bootstrapModule(AppModule)
    .catch(error => console.error(error));
