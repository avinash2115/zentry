import { BehaviorSubject, noop, Observable, Subject } from 'rxjs';

import { Core } from './core';
import { Base } from './services/base';
import { Resource } from './resource';
import { ParentResourceService } from './parent-resource-service';
import { PathBuilder } from './services/path-builder';
import { UrlParamsBuilder } from './services/url-params-builder';
import { Converter } from './services/converter';
import { LocalFilter } from './services/localfilter';
import { CacheMemory } from './services/cachememory';
import { CacheStore } from './services/cachestore';
import { IAttributes, ICacheMemory, ICollection, IExecParams, IParamsCollection, IParamsResource, ISchema } from './interfaces';

export class Service<R extends Resource = Resource> extends ParentResourceService {
    public schema: ISchema;
    public cachememory: ICacheMemory<R>;
    public cachestore: CacheStore;
    public type: string;
    public resource = Resource;
    public url: string;
    public path: string; // without slashes
    // used when loading initial collection (not relationship)
    public initialPath: string;
    private smartfiltertype = 'undefined';

    /*
    Register schema on Core
    @return true if the resource don't exist and registered ok
    */
    public register(): Service<R> | false {
        if (Core.me === null) {
            throw new Error(
                'Error: you are trying register `' +
                this.type +
                '` before inject JsonapiCore somewhere, almost one time.'
            );
        }
        // only when service is registered, not cloned object
        this.cachememory = new CacheMemory();
        this.cachestore = new CacheStore();
        this.schema = {...{}, ...Base.Schema, ...this.schema};

        return Core.me.registerService<R>(this);
    }

    public newResource(): R {
        const resource = new this.resource();

        return <R>resource;
    }

    public new(): R {
        const resource = this.newResource();
        resource.type = this.type;
        // issue #36: just if service is not registered yet.
        this.getService();
        resource.reset();

        return resource;
    }

    public getPrePath(): string {
        return '';
    }

    public getPath(): string {
        return this.path ? this.path : this.type;
    }

    public get(
        id: string,
        params?: IParamsResource | Function,
        fc_success?: Function,
        fc_error?: Function
    ): Observable<R> {
        return <Observable<R>>this.__exec({
            id: id,
            params: params,
            fc_success: fc_success,
            fc_error: fc_error,
            exec_type: 'get'
        });
    }

    public delete(
        id: string,
        params?: Object | Function,
        fc_success?: Function,
        fc_error?: Function
    ): Observable<void> {
        return <Observable<void>>this.__exec({
            id: id,
            params: params,
            fc_success: fc_success,
            fc_error: fc_error,
            exec_type: 'delete'
        });
    }

    public all(
        params?: IParamsCollection | Function,
        fc_success?: Function,
        fc_error?: Function
    ): Observable<ICollection<R>> {
        return <Observable<ICollection<R>>>this.__exec({
            id: null,
            params: params,
            fc_success: fc_success,
            fc_error: fc_error,
            exec_type: 'all'
        });
    }

    public _get(
        id: string,
        params: IParamsResource,
        fc_success,
        fc_error
    ): Observable<R> {
        // http request
        let path = new PathBuilder();
        path.applyParams(this, params, params.fullPath);
        path.appendPath(id);

        // CACHEMEMORY
        let resource = <R>this.getService().cachememory.getOrCreateResource(
            this.type,
            id
        );
        resource.is_loading = true;

        let subject = new BehaviorSubject<R>(resource);

        // exit if ttl is not expired
        let temporal_ttl = params.ttl || 0; // this.schema.ttl
        if (this.getService().cachememory.isResourceLive(id, temporal_ttl)) {
            // we create a promise because we need return collection before
            // run success client function
            let promise: Promise<void> = new Promise(
                (resolve, reject): void => {
                    resolve(fc_success);
                    promise
                        .then(fc_success2 => {
                            console.warn('vp-ngx-jsonapi: THIS CODE NEVER RUN, RIGHT? :/ Please check.');
                            subject.next(resource);
                            this.runFc(fc_success2, 'cachememory');
                        })
                        .catch(noop);
                    resource.is_loading = false;
                }
            );
            subject.next(resource);
            subject.complete();

            return subject.asObservable();
        } else if (Core.injectedServices.rsJsonapiConfig.cachestore_support) {
            // CACHESTORE
            this.getService()
                .cachestore.getResource(resource)
                .then(success => {
                    if (Base.isObjectLive(temporal_ttl, resource.lastupdate)) {
                        subject.next(resource);
                        this.runFc(fc_success, {data: success});
                    } else {
                        this.getGetFromServer(
                            path,
                            params,
                            fc_success,
                            fc_error,
                            resource,
                            subject
                        );
                    }
                })
                .catch(error => {
                    this.getGetFromServer(path, params, fc_success, fc_error, resource, subject);
                });
        } else {
            this.getGetFromServer(path, params, fc_success, fc_error, resource, subject);
        }
        subject.next(resource);

        return subject.asObservable();
    }

    /*
    @return This resource like a service
    */
    public getService<T extends Service<R>>(): T {
        return <T>(Converter.getService(this.type) || this.register());
        // let serv = Converter.getService(this.type);
        // if (serv) {
        //     return serv;
        // } else {
        //     return this.register();
        // }
    }

    public clearCacheMemory(): boolean {
        let path = new PathBuilder();
        path.applyParams(this);

        return (
            this.getService().cachememory.deprecateCollections(
                path.getForCache()
            ) &&
            this.getService().cachestore.deprecateCollections(
                path.getForCache()
            )
        );
    }

    public parseToServer(attributes: IAttributes): void {
        /* */
    }

    public parseFromServer(attributes: IAttributes): void {
        /* */
    }

    protected __exec(exec_params: IExecParams): Observable<R | ICollection<R> | void> {
        let exec_pp = super.proccess_exec_params(exec_params);

        switch (exec_pp.exec_type) {
            case 'get':
                return this._get(
                    exec_pp.id,
                    exec_pp.params,
                    exec_pp.fc_success,
                    exec_pp.fc_error
                );
            case 'delete':
                return this._delete(
                    exec_pp.id,
                    exec_pp.params,
                    exec_pp.fc_success,
                    exec_pp.fc_error
                );
            case 'all':
                return this._all(
                    exec_pp.params,
                    exec_pp.fc_success,
                    exec_pp.fc_error
                );
        }
    }

    private getGetFromServer(path, params, fc_success, fc_error, resource: R, subject: Subject<R>) {
        Core.injectedServices.JsonapiHttp.get(path.get(), this.url)
            .then(success => {
                if (params.applyPathToResource) {
                    resource.path = path.get(true);
                } else if (params.afterpath) {
                    resource.path = `${path.get(true)}/${success.data.id}`;
                }
                Converter.build(success /*.data*/, resource);
                resource.is_loading = false;
                this.getService().cachememory.setResource(resource);
                if (Core.injectedServices.rsJsonapiConfig.cachestore_support) {
                    this.getService().cachestore.setResource(resource);
                }
                subject.next(resource);
                subject.complete();
                this.runFc(fc_success, success);
            })
            .catch(error => {
                subject.error(error);
                this.runFc(fc_error, error);
            });
    }

    private _all(params: IParamsCollection, fc_success, fc_error): Observable<ICollection<R>> {
        // check smartfiltertype, and set on remotefilter
        if (params.smartfilter && this.smartfiltertype !== 'localfilter') {
            Object.assign(params.remotefilter, params.smartfilter);
        }

        params.cachehash = params.cachehash || '';

        // http request
        const path = new PathBuilder();
        // const paramsUrl = new UrlParamsBuilder();

        // todo: check and remove
        path.applyParams(this, params);
        // if (
        //     params.remotefilter &&
        //     Object.keys(params.remotefilter).length > 0
        // ) {
        //     if (this.getService().parseToServer) {
        //         this.getService().parseToServer(params.remotefilter);
        //     }
        //     path.addParam(paramsUrl.toparams({ filter: params.remotefilter }));
        // }
        if (params.pagination) {
            if (params.pagination.page > 0) {
                path.addParam(
                    Core.injectedServices.rsJsonapiConfig.parameters.pagination.page +
                    '=' + params.pagination.page
                );
            }
            if (params.pagination.limit) {
                path.addParam(
                    Core.injectedServices.rsJsonapiConfig.parameters.pagination.limit +
                    '=' + params.pagination.limit
                );
            }
        }
        if (params.sort) {
            path.addParam(
                Core.injectedServices.rsJsonapiConfig.parameters.sort +
                '=' + params.sort
            );
        }
        if (params.sortBy && Object.keys(params.sortBy).length > 0) {
            const paramsUrl = new UrlParamsBuilder();
            path.addParam(paramsUrl.toparams({
                [Core.injectedServices.rsJsonapiConfig.parameters.sortBy]: params.sortBy
            }, params.sortByReorderKey));
        }
        if (params.filterBy && Object.keys(params.filterBy).length > 0) {
            const paramsUrl = new UrlParamsBuilder();
            path.addParam(paramsUrl.toparams({
                [Core.injectedServices.rsJsonapiConfig.parameters.filterBy.elastic]: params.filterBy
            }));
        }

        if (params.search) {
            const search = encodeURI(params.search).replace('#', '%23');

            path.addParam(
                Core.injectedServices.rsJsonapiConfig.parameters.search +
                '=' + search
            );
        }
        // make request
        // if we remove this, dont work the same .all on same time (ej: <component /><component /><component />)
        const temporaryCollection = this.getService().cachememory.getOrCreateCollection(
            path.getForCache()
        );

        // creamos otra colleción si luego será filtrada
        let localfilter = new LocalFilter(params.localfilter);
        let cached_collection: ICollection<R>;
        if (params.localfilter && Object.keys(params.localfilter).length > 0) {
            cached_collection = Base.newCollection();
        } else {
            cached_collection = temporaryCollection;
        }

        let subject = new BehaviorSubject<ICollection<R>>(cached_collection);

        // MEMORY_CACHE
        let temporal_ttl = params.ttl || this.schema.ttl;
        if (
            temporal_ttl >= 0 &&
            this.getService().cachememory.isCollectionExist(path.getForCache())
        ) {
            // get cached data and merge with temporal collection
            temporaryCollection.$source = 'memory';

            // check smartfiltertype, and set on localfilter
            if (params.smartfilter && this.smartfiltertype === 'localfilter') {
                Object.assign(params.localfilter, params.smartfilter);
            }

            // fill collection and localfilter
            localfilter.filterCollection(
                temporaryCollection,
                cached_collection
            );

            // exit if ttl is not expired
            if (
                this.getService().cachememory.isCollectionLive(
                    path.getForCache(),
                    temporal_ttl
                )
            ) {
                // we create a promise because we need return collection before
                // run success client function
                let promise: Promise<void> = new Promise(
                    (resolve, reject): void => {
                        resolve(fc_success);
                        promise
                            .then(fc_success2 => {
                                subject.next(temporaryCollection);
                                this.runFc(fc_success2, 'cachememory');
                            })
                            .catch(noop);
                    }
                );
            } else {
                this.getAllFromServer(
                    path,
                    params,
                    fc_success,
                    fc_error,
                    temporaryCollection,
                    cached_collection,
                    subject
                );
            }
        } else if (Core.injectedServices.rsJsonapiConfig.cachestore_support) {
            // STORE
            temporaryCollection.$is_loading = true;

            this.getService()
                .cachestore.getCollectionFromStorePromise(
                path.getForCache(),
                path.includes,
                temporaryCollection
            )
                .then(success => {
                    temporaryCollection.$source = 'store';
                    temporaryCollection.$is_loading = false;

                    // when load collection from store, we save collection on memory
                    this.getService().cachememory.setCollection(
                        path.getForCache(),
                        temporaryCollection
                    );

                    // localfilter getted data
                    localfilter.filterCollection(
                        temporaryCollection,
                        cached_collection
                    );

                    if (
                        Base.isObjectLive(
                            temporal_ttl,
                            temporaryCollection.$cache_last_update
                        )
                    ) {
                        subject.next(temporaryCollection);
                        this.runFc(fc_success, {data: success});
                    } else {
                        this.getAllFromServer(
                            path,
                            params,
                            fc_success,
                            fc_error,
                            temporaryCollection,
                            cached_collection,
                            subject
                        );
                    }
                })
                .catch(error => {
                    this.getAllFromServer(
                        path,
                        params,
                        fc_success,
                        fc_error,
                        temporaryCollection,
                        cached_collection,
                        subject
                    );
                });
        } else {
            // STORE
            temporaryCollection.$is_loading = true;
            this.getAllFromServer(
                path,
                params,
                fc_success,
                fc_error,
                temporaryCollection,
                cached_collection,
                subject
            );
        }

        subject.next(<ICollection<R>>cached_collection);

        return subject.asObservable();
    }

    private getAllFromServer(
        path,
        params: IParamsCollection,
        fc_success,
        fc_error,
        temporary_collection: ICollection<R>,
        cached_collection: ICollection,
        subject: BehaviorSubject<ICollection<R>>
    ) {
        // SERVER REQUEST
        temporary_collection.$is_loading = true;
        Core.injectedServices.JsonapiHttp.get(path.get(), this.url)
            .then(success => {
                temporary_collection.$source = 'server';
                temporary_collection.$is_loading = false;

                // this create a new ID for every resource (for caching proposes)
                // for example, two URL return same objects but with different attributes
                if (params.cachehash || params.afterpath || params.applyPathToResource) {
                    Base.forEach(success.data, resource => {
                        if (params.cachehash) {
                            resource.id = resource.id + params.cachehash;
                        }
                        if (params.applyPathToResource) {
                            resource.path = path.get(true);
                        } else if (params.afterpath) {
                            resource.path = `${path.get(true)}/${resource.id}`;
                        }
                    });
                }

                Converter.build(success /*.data*/, temporary_collection);
                temporary_collection.meta = success.meta;

                this.getService().cachememory.setCollection(
                    path.getForCache(),
                    temporary_collection
                );
                if (Core.injectedServices.rsJsonapiConfig.cachestore_support) {
                    this.getService().cachestore.setCollection(
                        path.getForCache(),
                        temporary_collection,
                        params.include
                    );
                }

                // localfilter getted data
                let localfilter = new LocalFilter(params.localfilter);
                localfilter.filterCollection(
                    temporary_collection,
                    cached_collection
                );

                // trying to define smartfiltertype
                if (this.smartfiltertype === 'undefined') {
                    let page = temporary_collection.pagination;
                    if (
                        page.page === 1 &&
                        page.total_records <= page.limit
                    ) {
                        this.smartfiltertype = 'localfilter';
                    } else if (
                        page.page === 1 &&
                        page.total_records > page.limit
                    ) {
                        this.smartfiltertype = 'remotefilter';
                    }
                }

                subject.next(temporary_collection);
                subject.complete();
                this.runFc(fc_success, success);
            })
            .catch(error => {
                // do not replace $source, because localstorage don't write if = server
                // temporary_collection.$source = 'server';
                temporary_collection.$is_loading = false;
                subject.next(temporary_collection);
                subject.error(error);
                this.runFc(fc_error, error);
            });
    }

    private _delete(id: string, params, fc_success, fc_error): Observable<void> {
        // http request
        let path = new PathBuilder();
        path.applyParams(this, params);
        path.appendPath(id);

        let subject = new Subject<void>();

        Core.injectedServices.JsonapiHttp.delete(path.get(), this.url)
            .then(success => {
                this.getService().cachememory.removeResource(id);
                subject.next();
                subject.complete();
                this.runFc(fc_success, success);
            })
            .catch(error => {
                subject.error(error);
                this.runFc(fc_error, error);
            });


        return subject.asObservable();
    }
}
