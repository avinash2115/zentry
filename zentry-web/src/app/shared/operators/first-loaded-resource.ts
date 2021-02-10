import { first } from 'rxjs/operators';
import { OperatorFunction } from 'rxjs/internal/types';
import { Resource } from '../../../vendor/vp-ngx-jsonapi';

export default function firstLoadedResource<T extends Resource>(): OperatorFunction<T, T> {
    return first((data: T) => data && !data.is_loading);
}
