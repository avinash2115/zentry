import { Injectable } from '@angular/core';

@Injectable({
    providedIn: 'root'
})
export class UtilsService {
    static deepObjectsCompare(a: object, b: object): boolean {
        if (a === b) {
            // items are identical
            return true;
        } else if (typeof a === 'object' && a !== null && typeof b === 'object' && b !== null) {
            // items are objects - do a deep property value compare
            // join keys from both objects together in one array
            let keys: Array<string> = Object.keys(a).concat(Object.keys(b));
            // filter out duplicate keys
            keys = keys.filter((value, index, self) => self.indexOf(value) === index);
            for (const p of keys) {
                if (typeof a[p] === 'object' && typeof b[p] === 'object') {
                    if (this.deepObjectsCompare(a[p], b[p]) === false) {
                        return false;
                    }
                } else if (a[p] !== b[p]) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    static deepObjectsClone(obj: any): any {
        let copy;
        // Handle the 3 simple types, and null or undefined
        // tslint:disable-next-line:triple-equals
        if (null == obj || 'object' != typeof obj) {
            return obj;
        }

        // Handle Date
        if (obj instanceof Date) {
            copy = new Date();
            copy.setTime(obj.getTime());
            return copy;
        }

        // Handle Array
        if (obj instanceof Array) {
            copy = [];
            for (let i = 0, len = obj.length; i < len; i++) {
                copy[i] = this.deepObjectsClone(obj[i]);
            }
            return copy;
        }

        // Handle Object
        if (obj instanceof Object) {
            copy = {};
            for (const attr in obj) {
                if (obj.hasOwnProperty(attr)) {
                    copy[attr] = this.deepObjectsClone(obj[attr]);
                }
            }
            return copy;
        }

        throw new Error('Unable to copy obj! Its type isn\'t supported.');
    }

    static replaceAllAfterFirstOccurrence(str: string, searcher: RegExp, replaceValue: string = ''): string {
        return str.replace(searcher, replaceValue);
    }

    static msHuman(ms: number, h: boolean = false): string {
        let result: string = '';

        if (h) {
            const hours: number = parseInt(String((ms / (1000 * 60 * 60)) % 24), 10);
            result += hours < 10 ? `0${hours}:` : hours;
        }

        const seconds: number = parseInt(String((ms / 1000) % 60), 10);
        const minutes: number = parseInt(String((ms / (1000 * 60)) % 60), 10);

        result += ((minutes < 10) ? `0${minutes}` : minutes) + ':' + ((seconds < 10) ? `0${seconds}` : seconds);

        return String(result);
    }

    static downloadBlob(blob: Blob, filename: string): void {
        const url: string = window.URL.createObjectURL(blob);
        const tag: HTMLAnchorElement = document.createElement('a');

        tag.setAttribute('style', 'display: none');
        tag.href = window.URL.createObjectURL(blob);
        tag.download = filename;

        document.body.appendChild(tag);

        tag.click();

        window.URL.revokeObjectURL(url);

        tag.remove();
    }

    static propertyFromCSSClass(className: string, property: string): string {
        const el: HTMLDivElement = document.createElement('div')
        let color: string;
        el.style.cssText = 'position:fixed;left:-100px;top:-100px;width:1px;height:1px';
        el.className = className;
        document.body.appendChild(el);
        color = getComputedStyle(el).getPropertyValue(property);
        document.body.removeChild(el);
        return color
    }

    static blobToFile(blob: Blob, filename: string): File {
        const b: any = blob;

        b.lastModifiedDate = new Date();
        b.name = filename;

        return <File>blob;
    }

    static prettySize(bytes: number, separator: string = ' ', postFix: string = ''): string {
        if (bytes) {
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.min(parseInt(Math.floor(Math.log(bytes) / Math.log(1024)).toString(), 10), sizes.length - 1);
            return `${(bytes / (1024 ** i)).toFixed(i ? 1 : 0)}${separator}${sizes[i]}${postFix}`;
        }

        return '0';
    }

    static isInternalUrl(url: string): boolean {
        return url.includes(window.endpoints.api) || url.includes(window.endpoints.echo);
    }

    static isExternalUrl(url: string): boolean {
        return !UtilsService.isInternalUrl(url);
    }

    static toClipboard(value: string): void {
        const selBox = document.createElement('textarea');
        selBox.style.position = 'fixed';
        selBox.style.left = '0';
        selBox.style.top = '0';
        selBox.style.opacity = '0';
        selBox.value = value;
        document.body.appendChild(selBox);
        selBox.focus();
        selBox.select();
        document.execCommand('copy');
        document.body.removeChild(selBox);
    }

    static cssEscape(value: string): string {
        const string: string = String(value);
        const length: number = string.length;
        let index: number = -1;
        let codeUnit: number;
        let result: string = '';
        const firstCodeUnit = string.charCodeAt(0);
        while (++index < length) {
            codeUnit = string.charCodeAt(index);
            // Note: there’s no need to special-case astral symbols, surrogate
            // pairs, or lone surrogates.

            // If the character is NULL (U+0000), then the REPLACEMENT CHARACTER
            // (U+FFFD).
            if (codeUnit === 0x0000) {
                result += '\uFFFD';
                continue;
            }

            if (
                // If the character is in the range [\1-\1F] (U+0001 to U+001F) or is
                // U+007F, […]
                (codeUnit >= 0x0001 && codeUnit <= 0x001F) || codeUnit === 0x007F ||
                // If the character is the first character and is in the range [0-9]
                // (U+0030 to U+0039), […]
                (index === 0 && codeUnit >= 0x0030 && codeUnit <= 0x0039) ||
                // If the character is the second character and is in the range [0-9]
                // (U+0030 to U+0039) and the first character is a `-` (U+002D), […]
                (
                    index === 1 &&
                    codeUnit >= 0x0030 && codeUnit <= 0x0039 &&
                    firstCodeUnit === 0x002D
                )
            ) {
                // https://drafts.csswg.org/cssom/#escape-a-character-as-code-point
                result += '\\' + codeUnit.toString(16) + ' ';
                continue;
            }

            if (
                // If the character is the first character and is a `-` (U+002D), and
                // there is no second character, […]
                index === 0 &&
                length === 1 &&
                codeUnit === 0x002D
            ) {
                result += '\\' + string.charAt(index);
                continue;
            }

            // If the character is not handled by one of the above rules and is
            // greater than or equal to U+0080, is `-` (U+002D) or `_` (U+005F), or
            // is in one of the ranges [0-9] (U+0030 to U+0039), [A-Z] (U+0041 to
            // U+005A), or [a-z] (U+0061 to U+007A), […]
            if (
                codeUnit >= 0x0080 ||
                codeUnit === 0x002D ||
                codeUnit === 0x005F ||
                codeUnit >= 0x0030 && codeUnit <= 0x0039 ||
                codeUnit >= 0x0041 && codeUnit <= 0x005A ||
                codeUnit >= 0x0061 && codeUnit <= 0x007A
            ) {
                // the character itself
                result += string.charAt(index);
                continue;
            }

            // Otherwise, the escaped character.
            // https://drafts.csswg.org/cssom/#escape-a-character
            result += '\\' + string.charAt(index);

        }
        return result;
    }

    static getParameterByName(name: string, url: string = window.location.href): string | null {
        name = name.replace(/[\[\]]/g, '\\$&');

        const results: Array<any> = (new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)')).exec(url);

        if (!results) { return null };
        if (!results[2]) { return null };

        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
}
