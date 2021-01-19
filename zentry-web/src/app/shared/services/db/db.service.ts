import { Injectable } from '@angular/core';
import Dexie from 'dexie';
import { Observable, Observer } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class DbService {
    private _current: Dexie | null;
    private _connections: Array<Dexie> = [];

    constructor() {
    }

    connect(db: Dexie): DbService {
        if (this._existsIndex(db) === -1) {
            this._connections.push(db);
            this._current = db;
        }

        return this;
    }

    disconnect(db: Dexie): DbService {
        const i: number = this._existsIndex(db);

        if (i !== -1) {
            this._connections.splice(i, 1);
        }

        if (this._current instanceof Dexie && this._current.name === db.name) {
            this._current = null;
        }

        return this;
    }

    create(table: string, item: { identity: string, [key: string]: any }): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.current
                .table(table)
                .where({identity: item.identity})
                .count()
                .then((count: number) => {
                    if (count > 0) {
                        throw new ReferenceError(`Already exists ${item.identity}`);
                    } else {
                        return this.current.table(table).add(item);
                    }
                })
                .then(() => {
                    observer.next(true);
                    observer.complete();
                })
                .catch((error: Error) => {
                    observer.error(error);
                });
        });
    }

    update(table: string, item: { identity: string, [key: string]: any }): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.current
                .table(table)
                .update(item.id, item)
                .then((count: number) => {
                    observer.next(true);
                    observer.complete();
                })
                .catch((error: Error) => {
                    observer.error(error);
                });
        });
    }

    list(table: string, where: {[key: string]: any} = {}): Observable<Array<{ identity: string, [key: string]: any }>> {
        return new Observable<Array<{ identity: string, [key: string]: any }>>((observer: Observer<Array<{ identity: string, [key: string]: any }>>) => {
            let query;

            if (Object.keys(where).length) {
                query = this.current.table(table).where(where);
            } else {
                query = this.current.table(table)
            }

            query
                .toArray()
                .then((results: Array<any>) => {
                    observer.next(results);
                    observer.complete();
                })
                .catch((error: Error) => {
                    observer.error(error);
                });
        });
    }

    get(table: string, identity: string): Observable<{ identity: string, [key: string]: any }> {
        return new Observable<{ identity: string, [key: string]: any }>((observer: Observer<{ identity: string, [key: string]: any }>) => {
            this.current
                .table(table)
                .get({identity: identity})
                .then((result: any | undefined) => {
                    if (result === undefined) {
                        throw new Error(`Not found ${identity}`);
                    } else {
                        observer.next(result);
                        observer.complete();
                    }
                })
                .catch((error: Error) => {
                    observer.error(error);
                });
        });
    }

    delete(table: string, identity: string): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.get(table, identity)
                .subscribe((result: { identity: string, [key: string]: any }) => {
                    this.current
                        .table(table)
                        .delete(result['id'])
                        .then((result: any | undefined) => {
                            observer.next(true);
                            observer.complete();
                        })
                        .catch((error: Error) => {
                            observer.error(error);
                        });
                });
        });
    }

    deleteBulk(table: string, ids: Array<number>): Observable<boolean> {
        return new Observable<boolean>((observer: Observer<boolean>) => {
            this.current
                .table(table)
                .bulkDelete(ids)
                .then((result: any | undefined) => {
                    observer.next(true);
                    observer.complete();
                })
                .catch((error: Error) => {
                    observer.error(error);
                });
        });
    }

    private get current(): Dexie {
        if (!(this._current instanceof Dexie)) {
            throw new Error('Connect first');
        }

        return this._current;
    }

    private _existsIndex(db: Dexie): number {
        return this._connections.findIndex((d: Dexie) => d.name === db.name);
    }
}
