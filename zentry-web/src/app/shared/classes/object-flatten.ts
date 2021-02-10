import { Injectable } from '@angular/core';

interface OptsInterface {
    delimiter?: string,
    safe?: boolean,
    object?: boolean,
    overwrite?: boolean,
    maxDepth?: number,
    saveArray?: boolean
}

@Injectable()
export class ObjectFlatten {

    static flatten(target: object, opts: OptsInterface = {}): object {
        const delimiter = opts.delimiter || '.';
        const maxDepth = opts.maxDepth;
        const output = {};

        const step = (object: object, prev?: string, currentDepth?: number): void => {
            currentDepth = currentDepth || 1;
            Object.keys(object).forEach((key: string) => {
                const value = object[key];
                const isarray = opts.safe && Array.isArray(value);
                const type = Object.prototype.toString.call(value);
                const isobject = (
                    type === '[object Object]' ||
                    type === '[object Array]'
                );

                const newKey = prev ? prev + delimiter + key : key;

                if (
                    !isarray
                    && isobject
                    && Object.keys(value).length
                    && (
                        !opts.maxDepth || currentDepth < maxDepth
                    )
                ) {
                    return step(value, newKey, currentDepth + 1);
                }

                if (isarray && opts.saveArray) {
                    value.forEach((v: string, index: number) => {
                        output[newKey + delimiter + index] = v;
                    });

                    return;
                }

                output[newKey] = value;
            });
        };

        step(target);

        return output;
    }

    static unflatten(target: object, opts: OptsInterface = {}): object {
        const delimiter = opts.delimiter || '.';
        const overwrite = opts.overwrite || false;
        const result = {};

        if (Object.prototype.toString.call(target) !== '[object Object]') {
            return target;
        }

        const getkey = (key) => {
            const parsedKey = Number(key);

            return (
                isNaN(parsedKey) ||
                key.indexOf('.') !== -1 ||
                opts.object
            ) ? key : parsedKey;
        };

        const sortedKeys = Object.keys(target).sort((keyA, keyB) => keyA.length - keyB.length);

        sortedKeys.forEach((key) => {
            const split = key.split(delimiter);
            let key1 = getkey(split.shift());
            let key2 = getkey(split[0]);
            let recipient = result;

            while (key2 !== undefined) {
                const type = Object.prototype.toString.call(recipient[key1]);
                const isobject = (
                    type === '[object Object]' ||
                    type === '[object Array]'
                );

                // do not write over falsey, non-undefined values if overwrite is false
                if (!overwrite && !isobject && typeof recipient[key1] !== 'undefined') {
                    return;
                }

                if ((overwrite && !isobject) || (!overwrite && recipient[key1] == null)) {
                    recipient[key1] = (typeof key2 === 'number' && !opts.object ? [] : {});
                }

                recipient = recipient[key1];
                if (split.length > 0) {
                    key1 = getkey(split.shift());
                    key2 = getkey(split[0]);
                }
            }

            // unflatten again for 'messy objects'
            recipient[key1] = this.unflatten(target[key], opts);
        });

        return result;
    }
}
