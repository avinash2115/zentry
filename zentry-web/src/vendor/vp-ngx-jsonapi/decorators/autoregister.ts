import { Service } from '../service';

export function Autoregister() {
    return function <T extends typeof Service>(target: T): T {
        // save a reference to the original constructor
        const original = target;

        // the new constructor behaviour
        const f: any = function (...args) {
            const instance = original.apply(this, args);
            instance.register();
            return instance;
        };

        // copy prototype so intanceof operator still works
        f.prototype = original.prototype;

        // return new constructor (will override original)
        return f;
    };
}
