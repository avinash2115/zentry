import {
    ChangeDetectionStrategy,
    ChangeDetectorRef,
    Component,
    ElementRef,
    NgZone,
    OnDestroy,
    OnInit,
    ViewChild
} from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../../shared/services/layout.service';
import { ActivatedRoute, Router } from '@angular/router';
import { RecordedService } from '../recorded.service';
import { RecordedSubscriptionService } from '../recorded.subscription.service';
import { filter, takeUntil } from 'rxjs/operators';
import { DataError } from '../../../../shared/classes/data-error';
import {
    EType as EStreamType,
    StreamJsonapiResource as SessionStreamJsonapiResource
} from '../../../../resources/session/stream/stream.jsonapi.service';
import { UrlJsonapiResource as FileTemporaryUrlJsonapiResource } from '../../../../resources/file/temporary/url/url.jsonapi.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { FileService } from '../../../../shared/services/file.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { IGeo } from '../../../../shared/interfaces/geo/geo.interface';
import { MapsAPILoader } from '@agm/core';
import { Observable, Observer } from 'rxjs';
import { SwalService } from '../../../../shared/services/swal.service';
import { UtilsService } from '../../../../shared/services/utils.service';
import { RecordedParticipantService } from '../recorded.participant.service';
import { RecordedPoiService } from '../recorded.poi.service';
import { RecordedPoiTranscriptService } from '../recorded.poi.transcript.service';
import { SessionJsonapiResource } from '../../../../resources/session/session.jsonapi.service';
import { PoiJsonapiResource as SessionPoiJsonapiResource } from '../../../../resources/session/poi/poi.jsonapi.service';
import { SharedService } from '../../../shared/shared.service';
import { TagInputComponent } from 'ngx-chips';

@Component({
    selector: 'app-session-recorded-view',
    templateUrl: './view.component.html',
    styleUrls: ['./view.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        RecordedService,
        RecordedSubscriptionService,
        RecordedParticipantService,
        RecordedPoiService,
        RecordedPoiTranscriptService,
    ]
})
export class ViewComponent extends BaseDetachedComponent implements OnInit, OnDestroy {
    @ViewChild('videoPlayer', {static: false}) public videoPlayer: ElementRef;
    @ViewChild('shareModal', {static: false}) public shareModal: ElementRef;
    @ViewChild('name', {static: false}) public name: ElementRef;
    @ViewChild('tags', {static: false}) public tags: TagInputComponent;

    public entity: SessionJsonapiResource | null;
    public videoURL: string;

    public streamTypes: typeof EStreamType = EStreamType;

    public editMode: {
        name: boolean,
        tags: boolean
    } = {
        name: false,
        tags: false
    }

    private _locationAllowed: boolean = true;
    private _currentLocation: IGeo;

    private _locationCoder: any

    private _sharedURL: string;

    constructor(
        protected cdr: ChangeDetectorRef,
        protected router: Router,
        protected activatedRoute: ActivatedRoute,
        protected layoutService: LayoutService,
        protected loaderService: LoaderService,
        protected fileService: FileService,
        protected modalService: NgbModal,
        protected mapsAPILoader: MapsAPILoader,
        protected ngZone: NgZone,
        protected sharedService: SharedService,
        protected recordedService: RecordedService,
        protected recordedSubscriptionService: RecordedSubscriptionService,
    ) {
        super(cdr);
    }

    get service(): RecordedService {
        return this.recordedService;
    }

    get geo(): IGeo {
        if (!this.entity.geo) {
            return this._currentLocation;
        }

        return this.entity.geo
    }

    set geo(value: IGeo) {
        this.entity.geo = value;

        this.ngZone.run(() => {
            this.detectChanges();
        });
    }

    get sharedURL(): string {
        return this._sharedURL;
    }

    ngOnInit(): void {
        this.loadingTrigger();

        this.layoutService.changeTitle('Sessions | ...');

        this.mapsAPILoader.load().then(() => {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition((position: Position) => {
                    this._currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        place: '',
                    };
                }, (error: any) => {
                    console.error(error);

                    if (error.code && error.code === 1) {
                        this._locationAllowed = false;
                    }
                });
            }

            this._locationCoder = new google.maps.Geocoder;
        });

        this.service
            .entity
            .pipe(
                takeUntil(this._destroy$),
                filter((entity: SessionJsonapiResource | null) => entity instanceof SessionJsonapiResource)
            )
            .subscribe((entity: SessionJsonapiResource) => {
                if (!this.entity || this.entity.id !== entity.id || !this.videoURL) {
                    const stream: SessionStreamJsonapiResource | undefined = entity.streamByType(EStreamType.combined);

                    this.entity = entity;

                    if (stream instanceof SessionStreamJsonapiResource && stream.isConverted) {
                        this.service
                            .streamVideoURL(entity.streamByType(EStreamType.combined))
                            .pipe(takeUntil(this._destroy$))
                            .subscribe((url: string) => {
                                this.videoURL = url;

                                this.detectChanges();
                            }, (error: DataError) => {
                                this.fallback(error);
                            });
                    }

                    this.layoutService.changeTitle(`Sessions | ${entity.name}`);
                }

                this.entity = entity;

                this.loadingCompleted();

                setTimeout(() => this.detectChanges());
            });

        this.service
            .get(this.activatedRoute.snapshot.params.recordedId, ['*'], this.activatedRoute.snapshot.params.poiId)
            .pipe(takeUntil(this._destroy$))
            .subscribe(() => {
            }, (error: DataError) => {
                this.fallback(error, 'Something went wrong', () => {
                    this.router.navigate(['/session/recorded']);
                });
            });
    }

    ngOnDestroy(): void {
        super.ngOnDestroy();

        this.sharedService.release();
    }

    onClipSeeked(time: number): void {
        if (!this.videoPlayer || time < 0 || time > (this.videoPlayer.nativeElement as HTMLVideoElement).duration) {
            return;
        }

        (this.videoPlayer.nativeElement as HTMLVideoElement).currentTime = time;
        (this.videoPlayer.nativeElement as HTMLVideoElement).play();

        this.detectChanges();
    }

    onClipReplaced(url: string): void {
        this.videoURL = url;

        this.detectChanges();
    }

    onClipShared(entity: SessionPoiJsonapiResource): void {
        this.share(entity)
    }

    share(poi?: SessionPoiJsonapiResource): void {
        this.loaderService.show();

        this.service
            .share(poi)
            .pipe(takeUntil(this._destroy$))
            .subscribe((url: string) => {
                this._sharedURL = url;

                this.loaderService.hide();

                this.modalService.open(this.shareModal, {
                    size: 'lg'
                }).result.then((result: number) => {
                    switch (result) {
                        case 10:
                            this.shareCopy();
                            break;
                        case 20:
                            this.loaderService.show();
                            this.service
                                .share(poi, true)
                                .pipe(takeUntil(this._destroy$))
                                .subscribe(() => {
                                    this.loaderService.hide();
                                }, (error: DataError) => {
                                    this.loaderService.hide();
                                    console.error(error);
                                })
                            break;
                    }

                    this._sharedURL = null;
                }, () => {
                    this.loaderService.hide();
                    this._sharedURL = null;
                });
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            })
    }

    shareCopy(): void {
        UtilsService.toClipboard(this._sharedURL);

        SwalService.toastSuccess({
            title: 'Link has been copied to clipboard'
        });
    }

    download(): void {
        this.loaderService.show();

        this.service
            .streamDownloadURL(this.entity.streamByType(EStreamType.combined))
            .pipe(takeUntil(this._destroy$))
            .subscribe((resource: FileTemporaryUrlJsonapiResource) => {
                this.loaderService.hide();
                this.fileService.download(resource.url, `${this.entity.name}.${resource.name.split('.').pop()}`);
            }, (error: DataError) => {
                this.loaderService.hide();
                this.fallback(error);
            });
    }

    onEditMode(type: string): void {
        this.editMode[type] = true;

        this.detectChanges();

        setTimeout(() => {
            switch (type) {
                case 'name':
                    (this.name.nativeElement as HTMLInputElement).setSelectionRange(0, 0);
                    (this.name.nativeElement as HTMLInputElement).focus();
                    break;
                case 'tags':
                    ((this.tags.inputForm.input as ElementRef).nativeElement as HTMLInputElement).focus();
                    break;
            }

            this.detectChanges();
        }, 300);
    }

    offEditMode(mode: string): void {
        this.editMode[mode] = false;

        this.detectChanges();
    }

    isEditMode(type: string): boolean {
        return this.editMode[type];
    }

    locationModalOpen(modal: any): void {
        if (!this._locationAllowed) {
            SwalService.error({
                title: 'Access to the location is restricted',
                text: 'Please allow the location tracking in the browser settings'
            });

            return;
        }

        this.modalService
            .open(modal)
            .result
            .then((result: boolean) => {
                if (result) {
                    this.loaderService.show();

                    this.service
                        .save()
                        .pipe(takeUntil(this._destroy$))
                        .subscribe(() => {
                            this.loaderService.hide();
                        }, (error: DataError) => {
                            this.loaderService.hide();
                            this.fallback(error);
                        });
                } else {
                    this.entity.attributeRestore('location');
                }
            }, () => this.entity.attributeRestore('location'));

        const autocomplete: any = new google.maps.places.Autocomplete(<HTMLInputElement>document.getElementById('searchLocation'));

        autocomplete.addListener('place_changed', () => {
            this.ngZone.run(() => {
                const location: google.maps.places.PlaceResult = autocomplete.getPlace();

                if (location.geometry === undefined || location.geometry === null) {
                    return;
                }

                this
                    .locationMarkerAddress(location.geometry.location.lat(), location.geometry.location.lng())
                    .pipe(takeUntil(this._destroy$))
                    .subscribe((place: string) => {
                        this.geo = {
                            lat: location.geometry.location.lat(),
                            lng: location.geometry.location.lng(),
                            place: place,
                        }
                    }, (error: DataError) => {
                        console.error(error);
                        this.fallback(error);
                    });
            });
        });

        this.detectChanges();
    }

    locationMarkerDragEnd($event: any): void {
        this
            .locationMarkerAddress($event.coords.lat, $event.coords.lng)
            .pipe(takeUntil(this._destroy$))
            .subscribe((place: string) => {
                this.geo = {
                    lat: $event.coords.lat,
                    lng: $event.coords.lng,
                    place: place,
                }
            }, (error: DataError) => {
                console.error(error);
                this.fallback(error);
            });
    }

    locationMarkerAddress(lat: number, lng: number): Observable<string> {
        return new Observable<string>((observer: Observer<string>) => {
            this._locationCoder
                .geocode(
                    {
                        'location': {
                            lat: lat,
                            lng: lng
                        }
                    },
                    (results: Array<{ [key: string]: any }>, status: string) => {
                        switch (status) {
                            case 'OK':
                                break;
                            case 'ZERO_RESULTS':
                                observer.error(new DataError('', 500, 'Nothing found, please specify your search term', ''));
                                break;
                            default:
                                observer.error(new DataError('', 500, status, ''));
                                break;
                        }

                        observer.next(results[0].formatted_address);
                        observer.complete();
                    });
        });
    }

    save(mode: string, event?: FocusEvent): void {
        if (event && event.relatedTarget instanceof EventTarget && (event.relatedTarget as HTMLElement).id === 'cancel') {
            return
        }

        setTimeout(() => {
            this.offEditMode(mode);

            this.service
                .save()
                .pipe(takeUntil(this._destroy$))
                .subscribe(
                    () => {},
                    (error: DataError) => this.fallback(error)
                );
        }, 150);
    }

    cancel(mode: string): void {
        this.entity.attributeRestore(mode);
        this.offEditMode(mode);
    }

    fallback(error: DataError, title: string = 'Something went wrong', callback?: Function): void {
        switch (error.status) {
            case 404:
                title = 'Session was not found :('
                break;
            case 403:
                title = 'You don\'t have an access to this session :('
                break;
        }

        super.fallback(error, title, callback);
    }
}
