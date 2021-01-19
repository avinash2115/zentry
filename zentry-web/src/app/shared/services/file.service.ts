import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { DataError } from '../classes/data-error';
import { UtilsService } from './utils.service';
import { SwalService } from './swal.service';
import { LoaderService } from './loader.service';

@Injectable({
    providedIn: 'root'
})
export class FileService {

    constructor(
        private http: HttpClient,
        private loaderService: LoaderService,
    ) {
    }

    download(url: string, filename: string): void {
        this.loaderService.show();

        this.http
            .get(url, {responseType: 'blob', observe: 'body'})
            .subscribe((response: Blob) => {
                this.loaderService.hide();

                UtilsService.downloadBlob(response, filename);
            }, (error: DataError | HttpErrorResponse) => {
                this.loaderService.hide();
                switch (error.status) {
                    case 404:
                        SwalService.error({title: 'Something went wrong', text: 'File is not found'});
                        break;
                    default:
                        SwalService.error({title: 'Something went wrong', text: error.message});
                        break;
                }
            });
    }
}
