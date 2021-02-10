import { isObject } from 'rxjs/internal-compatibility';
import { Params } from '@angular/router';

export class UrlParamsBuilder {
    public toparams(params, reorderKey?: string): string {
        let result: string = '';
        Object.keys(params).forEach((key: string) => {
            result += this.toparamsarray(key, params[key], '&' + key, reorderKey);
        });
        return result.slice(1);
    }

    private toparamsarray(key: string, params: Params, add: string = '', reorderKey: string = ''): string {
        let result: string = '';
        if (Array.isArray(params) || isObject(params)) {
            const keys: Array<string> = Object.keys(params);
            if (reorderKey && reorderKey === key) {
                keys.sort((a, b) => b.localeCompare(a));
            }

            keys.forEach((k: string) => {
                result += this.toparamsarray(k, params[k], add + '[' + k + ']', reorderKey);
            });
        } else {
            result += add + '=' + params;
        }

        return result;
    }
}
