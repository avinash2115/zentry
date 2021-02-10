import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class LoaderService {
    private state$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);

    get state(): Observable<boolean> {
        return this.state$.asObservable();
    }

    show(): void {
        this.state$.next(true);
    }

    hide(): void {
        this.state$.next(false);
    }
}
