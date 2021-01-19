import { IParamsCollection, IParamsResource } from '../interfaces';
import { Core } from '../core';
import { Service } from '../service';
import { UrlParamsBuilder } from './url-params-builder';

export class PathBuilder {
    public paths: Array<string> = [];
    public includes: Array<string> = [];
    private get_params: Array<string> = [];

    public applyParams(
        service: Service,
        params: IParamsResource | IParamsCollection = {},
        path?: string
    ) {
        this.appendPath(service.getPrePath());
        if (params.beforepath) {
            this.appendPath(params.beforepath);
        }
        this.appendPath(path ? path : service.getPath());
        if (params.afterpath) {
            this.appendPath(params.afterpath);
        }
        if (params.include) {
            this.setInclude(params.include);
        }
        if (params['remotefilter'] && Object.keys(params['remotefilter']).length > 0) {
            const paramsUrl = new UrlParamsBuilder();
            this.addParam(paramsUrl.toparams({filter: params['remotefilter']}));
        }
    }

    public appendPath(value: string) {
        if (value !== '') {
            this.paths.push(value);
        }
    }

    public addParam(param: string): void {
        this.get_params.push(param);
    }

    public getForCache(): string {
        return this.paths.join('/') + this.get_params.join('/');
    }

    public get(removeInclude = false): string {
        let params = [...this.get_params];

        if (!removeInclude && this.includes.length > 0) {
            params.push('include=' + this.includes.join(','));
        }

        return (
            this.paths.join('/') +
            (params.length > 0
                ? Core.injectedServices.rsJsonapiConfig.params_separator +
                params.join('&')
                : '')
        );
    }

    private setInclude(strings_array: Array<string>) {
        this.includes = strings_array;
    }
}
