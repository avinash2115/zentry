import { Core } from './core';
import { Service } from './service';
import { Base } from './services/base';
import { ParentResourceService } from './parent-resource-service';
import { PathBuilder } from './services/path-builder';
// import { UrlParamsBuilder } from './services/url-params-builder';
import { Converter } from './services/converter';
import { IDataObject } from './interfaces/data-object';

import { isArray, isFunction } from 'rxjs/internal-compatibility';

import { IAttributes, ICollection, IExecParams, IParamsResource, IRelationship, IRelationships } from './interfaces';
import { ILinks } from './interfaces/links';

export class Resource extends ParentResourceService {
    public is_new = true;
    public is_loading = false;
    public is_saving = false;
    public id = '';
    public type = '';
    public attributes: IAttributes = {};
    public attributesOriginal: IAttributes = {};
    public relationships: IRelationships = {};
    public lastupdate: number;
    public path = '';
    public artifact: { [key: string]: Resource } = null;
    public links: ILinks = {};
    public meta?: object;
    public selfLinkAsPath = true;

    public attributePreserve(attribute: string): void {
        if (!this.attributesOriginal.hasOwnProperty(attribute)) {
            this.attributesOriginal[attribute] = this.attributes[attribute];
        }
    }

    public attributesPreserve(): void {
        Object.keys(this.attributes).forEach((attribute: string) => {
            this.attributesOriginal[attribute] = this.attributes[attribute];
        });
    }

    public attributeRestore(attribute: string): void {
        if (this.attributesOriginal.hasOwnProperty(attribute)) {
            this.attributes[attribute] = this.attributesOriginal[attribute];
            delete this.attributesOriginal[attribute];
        }
    }

    public attributesRestore(): void {
        Object.keys(this.attributesOriginal).forEach((attribute: string) => {
            this.attributes[attribute] = this.attributesOriginal[attribute];
        });
    }

    public reset(): void {
        this.id = '';
        this.attributes = {};
        this.relationships = {};
        Base.forEach(this.getService().schema.relationships, (value, key) => {
            if (this.getService().schema.relationships[key].hasMany) {
                const relation: IRelationship = {
                    data: Base.newCollection(),
                    content: 'collection'
                };
                this.relationships[key] = relation;
            } else {
                const relation: IRelationship = {data: {}, content: 'none'};
                this.relationships[key] = relation;
            }
        });
        this.is_new = true;
    }

    public toObject(params?: IParamsResource): IDataObject {
        params = {...{}, ...Base.Params, ...params};

        const relationships = {};
        const included = [];
        const includedIds = []; // just for control don't repeat any resource

        // RELATIONSHIPS
        Base.forEach(
            this.relationships,
            (relationship: IRelationship, relation_alias: string) => {
                if (
                    this.getService().schema.relationships[relation_alias] &&
                    this.getService().schema.relationships[relation_alias]
                        .hasMany
                ) {
                    // has many (hasMany:true)
                    relationships[relation_alias] = {data: []};

                    Base.forEach(relationship.data, (resource: Resource) => {
                        const relational_object = resource.toObject();
                        relationships[relation_alias].data.push(
                            relational_object.data
                        );

                        // has not yet been added to included && has been asked to include with the params.include
                        const temporal_id = resource.type + '_' + resource.id;
                        if (
                            includedIds.indexOf(temporal_id) === -1 &&
                            params.include.indexOf(relation_alias) !== -1
                        ) {
                            includedIds.push(temporal_id);
                            included.push(resource.toObject({}).data);
                        }
                    });
                } else {
                    // has one (hasMany:false)

                    const relationship_data = <Resource>relationship.data;
                    if (
                        !('id' in relationship.data) &&
                        Object.keys(relationship.data).length > 0
                    ) {
                        console.warn(
                            relation_alias +
                            ' defined with hasMany:false, but I have a collection'
                        );
                    }

                    if (relationship_data.type) {
                        relationships[relation_alias] = relationship_data.toObject();
                    } else {
                        relationships[relation_alias] = {data: {}};
                    }

                    // has not yet been added to included && has been asked to include with the params.include
                    const temporaryId =
                        relationship_data.type + '_' + relationship_data.id;
                    if (
                        includedIds.indexOf(temporaryId) === -1 &&
                        params.include.indexOf(relationship_data.type) !== -1
                    ) {
                        includedIds.push(temporaryId);
                        included.push(relationship_data.toObject({}).data);
                    }
                }
            }
        );

        // just for performance don't copy if not necessary
        let attributes;
        if (this.getService() && this.getService().parseToServer) {
            attributes = {...{}, ...this.attributes};
            this.getService().parseToServer(attributes);
        } else {
            attributes = this.attributes;
        }

        const ret: IDataObject = {
            data: {
                type: this.type,
                id: this.id,
                attributes: attributes,
                relationships: relationships,
                links: this.links || {},
            }
        };

        if (included.length > 0) {
            ret.included = included;
        }

        return ret;
    }

    public async save<T extends Resource>(params?: Object | Function,
                                          fc_success?: Function,
                                          fc_error?: Function): Promise<object> {
        return this.__exec({
            id: null,
            params: params,
            fc_success: fc_success,
            fc_error: fc_error,
            exec_type: 'save'
        });
    }

    public async archive<T extends Resource>(fc_success?: Function,
                                             fc_error?: Function): Promise<object> {
        return this.__exec({
            id: null,
            params: null,
            fc_success,
            fc_error,
            exec_type: 'archive'
        });
    }

    public async clone<T extends Resource>(fc_success?: Function,
                                           fc_error?: Function): Promise<object> {
        return this.__exec({
            id: null,
            params: null,
            fc_success,
            fc_error,
            exec_type: 'clone'
        });
    }

    public async delete<T extends Resource>(fc_success?: Function,
                                            fc_error?: Function): Promise<object> {
        return this.__exec({
            id: null,
            params: null,
            fc_success,
            fc_error,
            exec_type: 'delete'
        });
    }

    public async customCall<T extends Resource>(requestParams: { method: string, body?: IDataObject, postfixPath?: string, fullPath?: string, params?: IParamsResource, pieceToChange?: string, newPiece?: string },
                                                fc_success?,
                                                fc_error?): Promise<object> {
        const promiseArchive: Promise<object> = new Promise(
            (resolve, reject): void => {
                const path = new PathBuilder();
                path.applyParams(this.getService(), requestParams.params ? requestParams.params : {}, this.path);
                if (!this.path && this.id) {
                    path.appendPath(this.id);
                }

                path.appendPath(requestParams.postfixPath);
                let requestURLPath = requestParams.fullPath || path.get();

                if (requestParams.pieceToChange) {
                    const splitedPath = path.get().split('/');
                    const pathSectionIndex = splitedPath.findIndex((pathPiece) => {
                        return pathPiece === requestParams.pieceToChange;
                    });
                    splitedPath[pathSectionIndex + 1] = requestParams.newPiece;
                    requestURLPath = splitedPath.join('/');
                }

                if (requestParams.params && requestParams.params.pathWithoutId) {
                    requestURLPath = requestURLPath.replace(`/${this.id}`, '');
                }

                const body = requestParams.body === undefined ? this.toObject(requestParams.params) : requestParams.body;
                const promise = Core.injectedServices.JsonapiHttp.exec(
                    this.getService().url,
                    requestURLPath,
                    requestParams.method,
                    body,
                    isFunction(fc_error)
                );
                promise.then(success => {
                    this.is_saving = false;
                    this.runFc(fc_success, success);
                    resolve(success);
                })
                    .catch(error => {
                        this.is_saving = false;
                        this.runFc(
                            fc_error,
                            'data' in error ? error.data : error
                        );
                        reject('data' in error ? error.data : error);
                    });
            }
        );
        return promiseArchive;
    }

    public async reloadResource<T extends Resource>(params?: IParamsResource,
                                                    fc_success?: Function,
                                                    fc_error?: Function): Promise<object> {
        return this.selfCall('GET', params, fc_success, fc_error);
    }

    public addRelationship<T extends Resource>(resource: T,
                                               type_alias?: string) {
        let object_key = resource.id;
        if (!object_key) {
            object_key = 'new_' + Math.floor(Math.random() * 100000);
        }

        type_alias = type_alias || resource.getService().path || resource.type;
        if (!(type_alias in this.relationships)) {
            this.relationships[type_alias] = {data: {}, content: 'none'};
        }

        resource.path = this.getRelationPath(resource);
        if (
            type_alias in this.getService().schema.relationships &&
            this.getService().schema.relationships[type_alias].hasMany
        ) {
            this.relationships[type_alias].data[object_key] = resource;
            this.relationships[type_alias].content = 'collection';
        } else {
            this.relationships[type_alias].data = resource;
            this.relationships[type_alias].content = 'resource';
        }
    }

    public getRelationPath(relation: Resource) {
        return `${this.path || (this.getService().getPath() + '/' + this.id)}/relationships/${relation.getService().getPath()}/${relation.id}`;
    }

    public updateRelationshipKey<T extends Resource>(resource: T, newKey?: string, type_alias?: string) {
        newKey = newKey || resource.id || 'new_' + Math.floor(Math.random() * 100000);
        type_alias = type_alias || resource.getService().path || resource.type;
        if (
            type_alias in this.getService().schema.relationships &&
            this.getService().schema.relationships[type_alias].hasMany
        ) {
            Object.keys(this.relationships[type_alias].data).forEach(key => {
                if (this.relationships[type_alias].data[key] && this.relationships[type_alias].data[key].id === resource.id) {
                    delete this.relationships[type_alias].data[key];
                }
            });
            this.relationships[type_alias].data[newKey] = resource;
        } else {
            this.relationships[type_alias].data = resource;
        }
    }

    updateRelationshipPath<T extends Resource>(resource: T) {
        resource.path = `${this.path || (this.getService().getPath() + '/' + this.id)}/relationships/${resource.getService().getPath()}/${resource.id}`;
    }

    public addRelationships(resources: ICollection, type_alias: string) {
        if (!(type_alias in this.relationships)) {
            this.relationships[type_alias] = {data: {}, content: 'none'};
        } else {
            // we receive a new collection of this relationship. We need remove old (if don't exist on new collection)
            Base.forEach(this.relationships[type_alias].data, resource => {
                if (resource && !(resource.id in resources)) {
                    delete this.relationships[type_alias].data[resource.id];
                }
            });
        }

        Base.forEach(resources, resource => {
            resource.path = `${this.path || (this.getService().getPath() + '/' + this.id)}/relationships/${resource.getService().getPath()}/${resource.id}`;
            this.relationships[type_alias].data[resource.id] = resource;
        });
    }

    public addRelationshipsArray<T extends Resource>(resources: Array<T>,
                                                     type_alias?: string): void {
        resources.forEach((item: Resource) => {
            type_alias = type_alias || item.getService().path || item.type;
            this.addRelationship(item, type_alias || item.type);
        });
    }

    public deleteRelationship<T extends Resource>(resource: T,
                                                  type_alias?: string,
                                                  onSuccess?: Function,
                                                  onError?: Function): Promise<object> {
        type_alias = type_alias || resource.getService().path || resource.type;
        return resource.delete(onSuccess, onError).then(result => {
            this.removeRelationship(type_alias, resource.id);
            return result;
        });
    }

    public removeRelationship(type_alias: string, id: string): boolean {
        if (!(type_alias in this.relationships)) {
            return false;
        }
        if (!('data' in this.relationships[type_alias])) {
            return false;
        }

        if (
            type_alias in this.getService().schema.relationships &&
            this.getService().schema.relationships[type_alias].hasMany
        ) {
            if (!(id in this.relationships[type_alias].data)) {
                return false;
            }
            delete this.relationships[type_alias].data[id];
        } else {
            this.relationships[type_alias].data = {};
        }

        return true;
    }

    getRelationTypeByAlias(alias: string): string {
        return Object.keys(this.getService().schema.relationships).find(relationKey => {
            const schemaAlias = this.getService().schema.relationships[relationKey].alias;
            return schemaAlias === alias || relationKey === alias;
        }) || '';
    }

    /*
        @return This resource like a service
    */
    public getService(): Service {
        return Converter.getService(this.type);
    }

    protected async __exec<T extends Resource>(exec_params: IExecParams): Promise<object> {
        const exec_pp = this.proccess_exec_params(exec_params);

        switch (exec_params.exec_type) {
            case 'save':
                return this._save(
                    exec_pp.params,
                    exec_params.fc_success,
                    exec_params.fc_error
                );
            case 'delete':
                return this.customCall(
                    {method: 'DELETE', body: null},
                    exec_params.fc_success,
                    exec_params.fc_error
                );
            case 'clone':
            case 'archive':
            default:
                return this.customCall(
                    {method: 'POST', body: null, postfixPath: exec_params.exec_type},
                    exec_params.fc_success,
                    exec_params.fc_error
                );
        }
    }

    private async _save<T extends Resource>(params: IParamsResource,
                                            fc_success: Function,
                                            fc_error: Function): Promise<object> {
        return this.selfCall('POST', params, fc_success, fc_error);
    }

    // self call is similar to customCall, but it uses different parameters and applies results from server to self (this)
    private async selfCall<T extends Resource>(method: string = 'GET',
                                               params?: IParamsResource,
                                               fc_success?: Function,
                                               fc_error?: Function): Promise<object> {
        const callPromise: Promise<object> = new Promise(
            (resolve, reject): void => {
                if (this.is_saving || this.is_loading) {
                    return;
                }
                this.is_saving = true;

                const object = this.toObject(params);

                // http request
                const pathBuilder = new PathBuilder();
                pathBuilder.applyParams(this.getService(), params, this.path);
                if (!this.path && this.id) {
                    pathBuilder.appendPath(this.id);
                }
                let path = params.fullPath || pathBuilder.get();
                if (params) {
                    if (params.pathWithoutId) {
                        path = path.replace(`/${this.id}`, '');
                    }
                    if (!params.preserveRelationships) {
                        delete object.data.relationships;
                    }
                }
                const promise = Core.injectedServices.JsonapiHttp.exec(
                    this.getService().url,
                    path,
                    method,
                    object,
                    !isFunction(fc_error)
                );

                promise
                    .then(success => {
                        this.is_saving = false;

                        // foce reload cache (for example, we add a new element)
                        if (!this.id) {
                            this.getService().cachememory.deprecateCollections(
                                pathBuilder.get()
                            );
                            this.getService().cachestore.deprecateCollections(
                                pathBuilder.get()
                            );
                        }
                        // is a resource?
                        if ('id' in success.data) {
                            this.id = success.data.id;
                            Converter.build(success, this);
                            if (this.path) {
                                const pathSegments = this.path.split('/');
                                if (pathSegments[pathSegments.length - 1] !== this.id) {
                                    const lastCharacter = this.path[this.path.length - 1];
                                    this.path += `${lastCharacter === '/' ? '' : '/'}${this.id}`;
                                }
                            }
                            /*
                                If I save it in the cache, then it is not blended with the view
                                Use {{$ ctrl.service.getCachedResources () | json}}, add a new one, edit
                            */
                            // this.getService().cachememory.setResource(this);
                        } else if (isArray(success.data)) {
                            console.warn(
                                'Server return a collection when we save()',
                                success.data
                            );

                            /*
                                we request the service again, because server maybe are giving
                                us another type of resource (getService(resource.type))
                            */
                            const temporary_collection = this.getService().cachememory.getOrCreateCollection(
                                'justAnUpdate'
                            );
                            Converter.build(
                                success,
                                temporary_collection
                            );
                            Base.forEach(
                                temporary_collection,
                                (resource_value: Resource, key: string) => {
                                    const res = Converter.getService(
                                        resource_value.type
                                    ).cachememory.resources[resource_value.id];
                                    Converter.getService(
                                        resource_value.type
                                    ).cachememory.setResource(resource_value);
                                    Converter.getService(
                                        resource_value.type
                                    ).cachestore.setResource(resource_value);
                                    res.id = res.id + 'x';
                                }
                            );

                            console.warn(
                                'Temporal collection for a resource_value update',
                                temporary_collection
                            );
                        }

                        this.runFc(fc_success, success);
                        resolve(success);
                    })
                    .catch(error => {
                        this.is_saving = false;
                        this.runFc(
                            fc_error,
                            'data' in error ? error.data : error
                        );
                        reject('data' in error ? error.data : error);
                    });
            }
        );

        return callPromise;
    }
}
