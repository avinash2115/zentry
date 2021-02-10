import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { BaseDetachedComponent } from '../../../../shared/classes/abstracts/component/base-detached-component';
import { LayoutService } from '../../../../shared/services/layout.service';
import { UserService } from '../../user.service';
import { StorageJsonapiResource as UserStorageJsonapiResource } from '../../../../resources/user/storage/storage.jsonapi.service';
import {
    DriverJsonapiResource as UserStorageDriverJsonapiResource,
    EDriver
} from '../../../../resources/user/storage/driver/driver.jsonapi.service';
import { combineLatest, Observable, of } from 'rxjs';
import { switchMap, takeUntil } from 'rxjs/operators';
import kloudless from '@kloudless/kloudless';
import { SwalService } from '../../../../shared/services/swal.service';
import { DataError } from '../../../../shared/classes/data-error';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { UserJsonapiResource } from '../../../../resources/user/user.jsonapi.service';
import { LoaderService } from '../../../../shared/services/loader.service';
import { UtilsService } from '../../../../shared/services/utils.service';

@Component({
    selector: 'app-user-storage-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [
        UserService
    ],
})
export class ListComponent extends BaseDetachedComponent implements OnInit {
    public storages: Array<UserStorageJsonapiResource> = [];
    public storagesOptions: Array<UserStorageDriverJsonapiResource> = [];

    constructor(
        private cdr: ChangeDetectorRef,
        private layoutService: LayoutService,
        private loaderService: LoaderService,
        private authenticationService: AuthenticationService,
        private userService: UserService,
    ) {
        super(cdr);
    }

    ngOnInit(): void {
        this.authenticationService
            .entity
            .pipe(
                takeUntil(this._destroy$),
                switchMap((user: UserJsonapiResource) => this.userService.get(user.id)),
                switchMap(() => this.fetch())
            ).subscribe(() => {
        });

        this.layoutService.changeTitle('My Storage');
    }

    logo(driver: UserStorageDriverJsonapiResource): string {
        switch (driver.driverType) {
            case EDriver.default:
                return 'storage__default';
            case EDriver.kloudless_s3:
                return 'storage__aws';
            case EDriver.kloudless_google_drive:
                return 'storage__googledrive';
            case EDriver.kloudless_dropbox:
                return 'storage__dropbox';
            case EDriver.kloudless_box:
                return 'storage__box';
            default:
                return '';
        }
    }

    isConfigured(driver: UserStorageDriverJsonapiResource): boolean {
        return this.storages.findIndex((r: UserStorageJsonapiResource) => r.driver === driver.driverType) !== -1;
    }

    isDefault(driver: UserStorageDriverJsonapiResource): boolean {
        return this.storages.findIndex((r: UserStorageJsonapiResource) => r.driver === driver.driverType && r.enabled) !== -1;
    }

    storage(driver: UserStorageDriverJsonapiResource): UserStorageJsonapiResource {
        if (!this.isConfigured(driver)) {
            throw Error('Trying to display not configured storage');
        }

        return this.storages.find((r: UserStorageJsonapiResource) => r.driver === driver.driverType);
    }

    configure(driver: UserStorageDriverJsonapiResource): void {
        if (this.isConfigured(driver)) {
            SwalService.success({
                title: `Already configured`,
                text: `${driver.title} is already configured`
            });

            return;
        }

        let scope: string;

        switch (driver.driverType) {
            case EDriver.kloudless_s3:
                scope = 's3';
                break;
            case EDriver.kloudless_google_drive:
                scope = 'gdrive';
                break;
            case EDriver.kloudless_dropbox:
                scope = 'dropbox';
                break;
            case EDriver.kloudless_box:
                scope = 'box';
                break;
            default:
                throw Error('Driver is not supported');
        }

        kloudless.connectAccount({
            scope,
            appId: window.config.services.kloudless.appId,
        }).then((response: { data: object }) => {

            this.loaderService.show();

            const config: { [key: string]: string } = {};

            driver.config.forEach((key: string) => {
                config[key] = response.data[key];
            });

            this.userService
                .createStorage(driver.driverType, config)
                .subscribe(() => {
                    this.loaderService.hide();

                    SwalService.successQuestion({
                        title: 'Well done!',
                        text: `${driver.title} has been configured! Do you want to make it default?`,
                        confirmButtonText: `Make Default`,
                        cancelButtonText: 'Keep Existing'
                    }).then((answer: { value: boolean }) => {
                        this.fetch().subscribe(() => {
                            if (answer.value) {
                                this.activate(driver, true);
                            }
                        });
                    });
                }, (error: DataError) => {
                    this.loaderService.hide();

                    console.error(error);

                    SwalService.error({
                        title: 'Something went wrong',
                        text: error.message
                    });
                });
        }, (error: any) => {
            console.error(error);

            SwalService.error({
                title: 'Something went wrong',
                text: 'Please try again!'
            });
        });
    }

    activate(driver: UserStorageDriverJsonapiResource, silent: boolean = false): void {
        if (!this.isConfigured(driver)) {
            SwalService.error({
                title: `Is not configured`,
                text: `${driver.title} must be configured before using it as default`
            });

            return;
        }

        if (this.isDefault(driver)) {
            SwalService.success({
                title: `Already active`,
                text: `${driver.title} already selected as default`
            });

            return;
        }

        const callback = () => {
            this.loaderService.show();

            this.userService
                .enableStorage(this.storage(driver))
                .subscribe(() => {
                    this.loaderService.hide();

                    this.fetch().subscribe();

                    SwalService.toastSuccess({title: `${driver.title} has been selected as default!`});
                }, (error: DataError) => {
                    this.loaderService.hide();

                    console.error(error);

                    SwalService.error({
                        title: 'Something went wrong',
                        text: error.message
                    });
                });
        };

        if (!silent) {
            SwalService.warning({
                title: 'Are you sure?',
                text: `Do you really want to select ${driver.title} as your default storage?`,
                confirmButtonText: `Make Default`,
                cancelButtonText: 'Keep Existing'
            }).then((answer: { value: boolean }) => {
                if (answer.value) {
                    callback();
                }
            });
        } else {
            callback();
        }
    }

    used(driver: UserStorageDriverJsonapiResource): string {
        return UtilsService.prettySize(this.storage(driver).used);
    }

    capacity(driver: UserStorageDriverJsonapiResource): string {
        return UtilsService.prettySize(this.storage(driver).capacity);
    }

    private fetch(): Observable<boolean> {
        if (!this.isLoading) {
            this.loadingTrigger();
        }

        return combineLatest([
            this.userService.storages,
            this.userService.storagesDrivers
        ])
            .pipe(
                switchMap(([storages, storagesOptions]: [Array<UserStorageJsonapiResource>, Array<UserStorageDriverJsonapiResource>]) => {
                    this.storages = storages;
                    this.storagesOptions = storagesOptions;

                    this.loadingCompleted();

                    return of(true);
                })
            );
    }
}
