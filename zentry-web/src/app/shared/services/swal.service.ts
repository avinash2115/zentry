import { Injectable } from '@angular/core';
import swal, { SweetAlertOptions, SweetAlertResult } from 'sweetalert2';

export interface SwalConfigInterface {
    title: string,
    text?: string,
    imageUrl?: string,
    confirmButtonText?: string,
    confirmButtonClass?: string,
    cancelButtonText?: string,
    cancelButtonClass?: string,
    heightAuto?: boolean
}

@Injectable({
    providedIn: 'root'
})
export class SwalService {

    static hydrateConfig(config: SwalConfigInterface): SweetAlertOptions {
        return {
            title: config.title,
            text: config.text || null,
            imageUrl: config.imageUrl || null,
            customClass: {
                icon: 'swal--icons__hide'
            },
            confirmButtonText: config.confirmButtonText || 'Ok',
            confirmButtonClass: config.confirmButtonClass || 'btn btn__standard btn--radius__md btn--color__green',
            cancelButtonText: config.cancelButtonText || 'Cancel',
            cancelButtonClass: 'btn btn__standard btn--radius__md btn-outline-dark',
            showCloseButton: false,
            buttonsStyling: false,
            heightAuto: typeof config.heightAuto === 'boolean' ? config.heightAuto : true,
            allowOutsideClick: () => swal.isLoading(),
            allowEscapeKey: () => swal.isLoading(),
            allowEnterKey: () => swal.isLoading()
        };
    }

    static success(config: SwalConfigInterface): Promise<SweetAlertResult> {
        return swal.fire({
            ...this.hydrateConfig(config),
            type: 'success',
            imageUrl: '/assets/img/icons/swal/success.svg',
        });
    }

    static successQuestion(config: SwalConfigInterface): Promise<SweetAlertResult> {
        return swal.fire({
            ...this.hydrateConfig(config),
            type: 'warning',
            imageUrl: '/assets/img/icons/swal/success.svg',
            confirmButtonClass: 'btn btn__standard btn--radius__md btn--color__orange',
            showCancelButton: true,
        });
    }

    static error(config: SwalConfigInterface): Promise<SweetAlertResult> {
        return swal.fire({
            ...this.hydrateConfig(config),
            type: 'error',
            imageUrl: '/assets/img/icons/swal/error.svg',
            confirmButtonClass: config.confirmButtonClass || 'btn btn__standard btn--radius__md btn--color__blue',
        });
    }

    static warning(config: SwalConfigInterface): Promise<SweetAlertResult> {
        return swal.fire({
            ...this.hydrateConfig(config),
            type: 'warning',
            imageUrl: '/assets/img/icons/swal/warning.svg',
            confirmButtonClass: 'btn btn__standard btn--radius__md btn--color__orange',
            showCancelButton: true,
        });
    }

    static remove(config: SwalConfigInterface): Promise<SweetAlertResult> {
        return swal.fire({
            ...this.hydrateConfig(config),
            type: 'warning',
            imageUrl: '/assets/img/icons/swal/remove.svg',
            confirmButtonText: 'Yes, remove',
            confirmButtonClass: 'btn btn__standard btn--radius__md btn--color__red',
            showCancelButton: true,
        });
    }

    static toastSuccess(config: SwalConfigInterface): Promise<SweetAlertResult> {
        return swal.mixin({
            toast: true,
            position: 'bottom',
            showConfirmButton: false,
            timer: 3000,
            onOpen: (toast: HTMLElement) => {
                toast.addEventListener('mouseenter', swal.stopTimer)
                toast.addEventListener('mouseleave', swal.resumeTimer)
            }
        }).fire({
            ...this.hydrateConfig(config),
            imageUrl: '/assets/img/icons/swal/success.svg'
        });
    }
}
