// import * as angular from 'angular';
import { Core } from '../core';
import { Resource } from '../resource';
import { Service } from '../service';
import { ICollection, IResourcesById, IResourcesByType } from '../interfaces';
import { ResourceRelationshipsConverter } from './resource-relationships-converter';
import { IDataObject } from '../interfaces/data-object';
import { IDataCollection } from '../interfaces/data-collection';
import { IDataResource } from '../interfaces/data-resource';
import { Base } from '../services/base';

export class Converter {
    /*
    Convert json arrays (like included) to an indexed Resources array by [type][id]
    */
    public static json_array2resources_array_by_type(
        json_array: Array<IDataResource>
    ): IResourcesByType {
        let all_resources: IResourcesById = {};
        let resources_by_type: IResourcesByType = {};

        Converter.json_array2resources_array(json_array, all_resources);
        Base.forEach(all_resources, (resource: Resource) => {
            if (!(resource.type in resources_by_type)) {
                resources_by_type[resource.type] = {};
            }
            resources_by_type[resource.type][resource.id] = resource;
        });

        return resources_by_type;
    }

    public static json2resource(
        json_resource: IDataResource,
        instance_relationships
    ): Resource {
        const resource_service = Converter.getService(json_resource.type);
        if (resource_service) {
            return Converter.procreate(json_resource);
        } else {
            // service not registered
            console.warn(
                '`' + json_resource.type + '`',
                'service not found on json2resource()'
            );
            let temp = new Resource();
            temp.id = json_resource.id;
            temp.type = json_resource.type;

            return temp;
        }
    }

    public static getService(type: string): Service {
        let resource_service = Core.me.getResourceService(type);

        return resource_service;
    }

    public static build(
        document_from: IDataCollection | IDataObject,
        resource_dest: Resource | ICollection
    ) {
        // Instance include and I keep them in included arrary
        let included_resources: IResourcesByType = {};
        if ('included' in document_from) {
            included_resources = Converter.json_array2resources_array_by_type(
                document_from.included
            );
        }

        if (Array.isArray(document_from.data)) {
            Converter._buildCollection(
                <IDataCollection>document_from,
                <ICollection>resource_dest,
                included_resources
            );
        } else {
            Converter._buildResource(
                document_from.data,
                <Resource>resource_dest,
                included_resources
            );
        }
    }

    /*
    Convert json arrays (like included) to an Resources arrays without [keys]
    */
    private static json_array2resources_array(
        json_array: Array<IDataResource>,
        destination_array: IResourcesById = {}
    ): void {
        for (let data of json_array) {
            let resource = Converter.json2resource(data, false);
            destination_array[resource.type + '_' + resource.id] = resource;
        }
    }

    /* return a resource type(resoruce_service) with data(data) */
    private static procreate(data: IDataResource): Resource {
        if (!('type' in data && 'id' in data)) {
            console.error('Jsonapi Resource is not correct', data);
        }

        let resource: Resource;
        if (data.id in Converter.getService(data.type).cachememory.resources) {
            resource = Converter.getService(data.type).cachememory.resources[
                data.id
                ];
        } else {
            resource = Converter.getService(
                data.type
            ).cachememory.getOrCreateResource(data.type, data.id);
        }

        resource.attributes = data.attributes || {};
        resource.is_new = false;

        return resource;
    }

    private static _buildCollection(
        collection_data_from: IDataCollection,
        collection_dest: ICollection,
        included_resources: IResourcesByType
    ) {
        // sometime get Cannot set property 'number' of undefined (page)
        if (collection_dest.pagination && collection_data_from.meta && collection_data_from.meta.pagination) {
            collection_dest.pagination.page = collection_data_from.meta.pagination.page || 1;
            collection_dest.pagination.limit = collection_data_from.meta.pagination.limit || null;
            collection_dest.pagination.total_records = collection_data_from.meta.pagination.total_records || null;
            collection_dest.pagination.total_pages = collection_data_from.meta.pagination.total_pages || null;
        }

        // convert and add new dataresoures to final collection
        const new_ids = {};
        for (const dataResource of collection_data_from.data) {
            if (!(dataResource.id in collection_dest)) {
                collection_dest[dataResource.id] = Converter.getService(
                    dataResource.type
                ).cachememory.getOrCreateResource(
                    dataResource.type,
                    dataResource.id
                );
            }
            Converter._buildResource(
                dataResource,
                collection_dest[dataResource.id],
                included_resources
            );
            new_ids[dataResource.id] = dataResource.id;
        }

        // remove old members of collection (bug, for example, when request something like orders/10/details and has new ids)
        Base.forEach(collection_dest, resource => {
            if (!(resource.id in new_ids)) {
                delete collection_dest[resource.id];
            }
        });
    }

    private static _buildResource(
        resource_data_from: IDataResource,
        resource_dest: Resource,
        included_resources: IResourcesByType
    ) {
        resource_dest.id = resource_data_from.id || '';
        resource_dest.attributes = resource_data_from.attributes || {};
        resource_dest.links = resource_data_from.links || {};
        resource_dest.meta = resource_data_from.meta && Object.prototype.toString.call(resource_data_from.meta) === '[object Object]' ? resource_data_from.meta : {};
        if (resource_dest.selfLinkAsPath && resource_dest.links.self) {
            resource_dest.path = resource_dest.links.self;
        } else if (resource_data_from.path) {
            resource_dest.path = resource_data_from.path;
        }

        resource_dest.is_new = false;
        const service = Converter.getService(resource_data_from.type);

        // this prevents the indefinite creation of resources
        // the service must be if we do not have the schema
        if (!resource_dest.relationships || !service) {
            return;
        }

        Converter.getService(resource_data_from.type).parseFromServer(resource_dest.attributes);

        new ResourceRelationshipsConverter(
            Converter.getService,
            resource_data_from.relationships || {},
            resource_dest.relationships,
            included_resources,
            service.schema,
            resource_dest.path ? `${resource_dest.path}/relationships/` : `${resource_dest.type}/${resource_dest.id}/relationships/`
        ).buildRelationships();
    }
}
