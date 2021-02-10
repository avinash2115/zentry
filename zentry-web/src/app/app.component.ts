import { Component, OnInit } from '@angular/core';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {
    ngOnInit(): void {
        if (!!window.application.theme) {
            document.querySelector('body').classList.add(`theme-${window.application.theme}`);
            document.getElementById('favicon').setAttribute('href', `/assets/img/icons/themes/${window.application.theme}/favicon.ico`);
        } else {
            document.getElementById('favicon').setAttribute('href', `/assets/img/icons/favicon.ico`);
        }
    }
}
