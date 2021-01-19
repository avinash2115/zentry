import { Injectable } from '@angular/core';
import { HttpRequest } from '@angular/common/http';
import { UtilsService } from './utils.service';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { SharedService } from '../../modules/shared/shared.service';

@Injectable({
    providedIn: 'root'
})
export class HeaderService {

    constructor(
        private authenticationService: AuthenticationService,
        private sharedService: SharedService,
    ) {
    }

    defaultHeaders(): { [key: string]: string } {
        return {
            'Accept': 'application/vnd.api+json',
            'Authorization': this.resolveToken(),
            'Content-Type': 'application/vnd.api+json'
        };
    }

    uploadHeaders(): { [key: string]: string } {
        return {
            'Accept': 'application/vnd.api+json',
            'Authorization': this.resolveToken(),
            'X-HTTP-METHOD-OVERRIDE': 'PUT'
        };
    }

    setRequestHeaders(request: HttpRequest<any>): HttpRequest<any> {
        const headers: { [key: string]: string } = this.defaultHeaders();

        if (this.sharedService.isSharing) {
            headers['X-SHARED-ID'] = this.sharedService.identity;
        }

        if (['POST', 'GET', 'OPTIONS'].indexOf(request.method) === -1) {
            if (request.method.toLowerCase() === 'put') {
                delete headers['Content-Type'];
            }

            return request.clone({
                setHeaders: {
                    ...headers,
                    'X-HTTP-METHOD-OVERRIDE': request.method
                },
                body: ['post', 'patch', 'delete', 'put'].indexOf(request.method.toLowerCase()) !== -1 ? (request.body || {data: {}}) : null,
                method: 'POST',
                url: UtilsService.replaceAllAfterFirstOccurrence(request.url.replace(/([^:]\/)\/+/g, "$1"), new RegExp(`(?!^)${window.endpoints.api}`, 'g'))
            });
        } else {
            if (request.headers.has('X-HTTP-METHOD-OVERRIDE') && request.headers.get('X-HTTP-METHOD-OVERRIDE').toLowerCase() === 'put') {
                delete headers['Content-Type'];
            }

            return request.clone({
                setHeaders: headers,
                body: request.method.toLowerCase() === 'post' ? (request.body || {data: {}}) : null,
                url: UtilsService.replaceAllAfterFirstOccurrence(request.url.replace(/([^:]\/)\/+/g, "$1"), new RegExp(`(?!^)${window.endpoints.api}`, 'g'))
            });
        }
    }

    private resolveToken(): string {
        const token = this.authenticationService.token;
        return token ? `Bearer ${token}` : '';
    }
}
