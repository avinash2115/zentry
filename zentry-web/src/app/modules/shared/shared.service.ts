import { Injectable } from '@angular/core';
import { EType, SharedJsonapiResource, SharedJsonapiService } from '../../resources/shared/shared.jsonapi.service';
import { BehaviorSubject, Observable, Observer } from 'rxjs';
import firstLoadedResource from '../../shared/operators/first-loaded-resource';
import { Router } from '@angular/router';

@Injectable({
    providedIn: 'root'
})
export class SharedService {
    private entity$: BehaviorSubject<SharedJsonapiResource | null> = new BehaviorSubject<SharedJsonapiResource | null>(null);

    constructor(
        private router: Router,
        public sharedJsonapiService: SharedJsonapiService,
    ) {
    }

    get entity(): Observable<SharedJsonapiResource | null> {
        return this.entity$.asObservable();
    }

    get identity(): string {
        if (!this.isSharing) {
            throw new Error('No shared resource');
        }

        return this.entity$.value.id;
    }

    get isSharing(): boolean {
        return !!this.entity$.value && !!this.entity$.value.id;
    }

    isAllowed(url: string): boolean {
        if (!this.isSharing) {
            return false;
        }

        switch (this.entity$.value.sharedType) {
            case EType.recorded:
                return url.includes(`/session/recorded/${this.entity$.value.payload.parameters['sessionId']}`);
            case EType.recordedPoi:
                return url.includes(`/session/recorded/${this.entity$.value.payload.parameters['sessionId']}/${this.entity$.value.payload.parameters['poiId']}`);
            default:
                return false;
        }
    }

    fetch(id: string): Observable<SharedJsonapiResource> {
        return this.sharedJsonapiService
            .get(id)
            .pipe(
                firstLoadedResource()
            );
    }

    use(shared: SharedJsonapiResource): Observable<{ redirectTo: string }> {
        return new Observable<{ redirectTo: string }>((observer: Observer<{ redirectTo: string }>) => {
            this.entity$.next(shared);

            switch (shared.sharedType) {
                case EType.recorded:
                    observer.next({
                        redirectTo: `/session/recorded/${shared.payload.parameters['sessionId']}`
                    });
                    observer.complete();
                    break;
                case EType.recordedPoi:
                    observer.next({
                        redirectTo: `/session/recorded/${shared.payload.parameters['sessionId']}/${shared.payload.parameters['poiId']}`
                    });
                    observer.complete();
                    break;
                default:
                    observer.error(new Error('This shared type is not supported'));
                    break;
            }
        });
    }

    release(): void {
        this.entity$.next(null);
    }

    build(shared: SharedJsonapiResource): string {
        return window.location.origin + this.router.serializeUrl(this.router.createUrlTree(['/shared', shared.id]));
    }
}
