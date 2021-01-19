import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../shared/classes/abstracts/component/base-detached-component';
import { ActivatedRoute, Router } from '@angular/router';
import { SharedService } from '../shared.service';
import { DataError } from '../../../shared/classes/data-error';
import { SwalService } from '../../../shared/services/swal.service';
import { SharedJsonapiResource } from '../../../resources/shared/shared.jsonapi.service';
import { switchMap, takeUntil } from 'rxjs/operators';

@Component({
    selector: 'app-shared-link',
    templateUrl: './link.component.html',
    styleUrls: ['./link.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class LinkComponent extends BaseDetachedComponent implements OnInit {
    constructor(
        private cdr: ChangeDetectorRef,
        private router: Router,
        private activatedRoute: ActivatedRoute,
        private sharedService: SharedService,
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.sharedService
            .fetch(this.activatedRoute.snapshot.params.sharedId)
            .pipe(
                takeUntil(this._destroy$),
                switchMap((shared: SharedJsonapiResource) => this.sharedService.use(shared))
            )
            .subscribe(({redirectTo}: { redirectTo: string }) => {
                this.router.navigate([redirectTo]);
            }, (error: DataError) => {
                console.error(error);

                switch (error.status) {
                    case 404:
                        SwalService.error({
                            title: 'Not found',
                            text: 'This link is no longer valid',
                            heightAuto: false
                        }).then(() => {
                            this.router.navigate(['/']);
                        });

                        break;
                    default:
                        SwalService.error({
                            title: 'Something went wrong',
                            text: error.message,
                            heightAuto: false
                        }).then(() => {
                            this.router.navigate(['/']);
                        });
                        break;
                }
            });
    }
}
