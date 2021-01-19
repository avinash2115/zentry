export function debounce(timeout: number, cancelDebounce?: CallableFunction): MethodDecorator {
    // store timeout value for cancel the timeout
    let timeoutRef = null;

    return function (target: Object, propertyKey: string, descriptor: PropertyDescriptor): PropertyDescriptor {

        // store original function for future use
        const original = descriptor.value;

        // override original function body
        descriptor.value = function fn(...args: Array<any>): void {

            // clear previous timeout
            clearTimeout(timeoutRef);

            // sechudle timer
            timeoutRef = setTimeout(() => {

                // call original function
                original.apply(this, args);

            }, timeout);

            // define a property to cancel existing debounce timmer
            Object.defineProperty(fn, 'cancelDebounce', {
                value: function (): void {
                    clearTimeout(timeoutRef);
                }
            });
        };

        // return descriptor with new value
        return descriptor;
    };
}
