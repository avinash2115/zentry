import { first } from 'rxjs/operators';
import { OperatorFunction } from 'rxjs/internal/types';
import { ICollection, Resource } from '../../../vendor/vp-ngx-jsonapi';

export default function firstLoadedCollection<T extends Resource>(): OperatorFunction<ICollection<T>, ICollection<T>> {
    return first((data: ICollection<T>) => data && !data.$is_loading);
}
