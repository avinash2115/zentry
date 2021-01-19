import Echo from "laravel-echo";
import { Injectable } from '@angular/core';
import { SessionService } from './session.service';
import { filter, switchMap, take } from 'rxjs/operators';
import { Observable } from 'rxjs/internal/Observable';
import { of } from 'rxjs/internal/observable/of';
import { Observer } from 'rxjs/internal/types';
import { HeaderService } from './header.service';
import { AuthenticationService } from '../../modules/authentication/authentication.service';
import { Channel, PresenceChannel, SocketIoPrivateChannel } from 'laravel-echo/dist/channel';
import * as io from "socket.io-client";

interface ISubscriptionFallbackBagItem {
    channelName: string,
    occurrences: number,
    httpCodes: Array<number>
}

@Injectable({
    providedIn: 'root'
})
export class EchoService {
    private instance: Echo;

    private subscriptionFallbackBag: Array<ISubscriptionFallbackBagItem> = [];
    private subscriptionFallbackThreshold: number = 3;

    constructor(
        private headerService: HeaderService,
        private sessionService: SessionService,
        private authenticationService: AuthenticationService
    ) {
        this.sessionService.isRefreshed.subscribe(() => {
            this.refresh();
        });
    }

    get echo(): Echo {
        if (!this.instance) {
            this.instance = this.createEchoConnection();
        }

        return this.instance;
    }

    get isConnected(): boolean {
        if (!(this.instance instanceof Echo)) {
            return false;
        }

        return !!this.instance.connector.socketId;
    }

    refresh(): void {
        if (this.isConnected) {
            this.echo.connector.setOptions(this._settings());
        }
    }

    disconnect(): void {
        if (this.isConnected) {
            const connector: any = this.instance.connector;

            Object.keys(connector.channels).forEach((name: string) => {
                connector.leaveChannel(name);
            });

            connector.disconnect();
        }

        this.instance = null;
    }

    private(name: string): Observable<Channel> {
        const closure: (channelName: string) => Observable<Channel> = (channelName: string) => {
            const channel: Channel = this.echo.private(channelName);

            const channelSocket: SocketIoPrivateChannel = channel as SocketIoPrivateChannel;

            return new Observable((observer: Observer<Channel>) => {
                observer.next(channel);
                observer.complete();

                // channelSocket.listen('subscribed', () => {
                //     if (!window.config.production) {
                //         console.log(`${String(channelSocket.name)} is subscribed!`);
                //     }
                //
                //     const bag: ISubscriptionFallbackBagItem | undefined = this.subscriptionFallbackBag.find((r: ISubscriptionFallbackBagItem) => r.channelName === String(channelSocket.name));
                //
                //     if (bag) {
                //         bag.occurrences = 0;
                //         bag.httpCodes = [];
                //     }
                // });

                channelSocket.on('subscription_error', (httpCode: number) => {
                    if (!window.config.production) {
                        console.error('Detected failed subscription!!!');
                        console.error('Failed channel:');
                        console.error(channel);
                        console.error('HTTP Status Code:');
                        console.error(httpCode);
                        console.error('Current auth token:');
                        console.error(this.authenticationService.token);
                        console.error('Current echo options:');
                        console.error(this.echo.options);
                    }

                    if (this.sessionService.isRefreshingValue) {
                        this.sessionService
                            .isRefreshing
                            .pipe(
                                filter((result: boolean) => !result),
                                take(1)
                            )
                            .subscribe(() => {
                                if (!this._subscriptionFallback(channelSocket, httpCode)) {
                                    observer.error(new Error('cannot subscribe'));
                                }
                            });
                    } else {
                        if (!this._subscriptionFallback(channelSocket, httpCode)) {
                            observer.error(new Error('cannot subscribe'));
                        }
                    }
                });
            });
        };

        if (this.sessionService.isRefreshingValue) {
            return this.sessionService
                .isRefreshing
                .pipe(
                    filter((result: boolean) => !result),
                    take(1),
                    switchMap(() => {
                        return closure(name);
                    })
                );
        } else {
            return closure(name);
        }
    }

    join(name: string): Observable<PresenceChannel> {
        if (this.sessionService.isRefreshingValue) {
            return this.sessionService
                .isRefreshing
                .pipe(
                    filter((result: boolean) => !result),
                    take(1),
                    switchMap(() => {
                        return of(this.echo.join(name));
                    })
                );
        } else {
            return of(this.echo.join(name));
        }
    }

    leave(name: string): void {
        if (this.isConnected) {
            this.echo.leave(name);
        }
    }

    private _subscriptionFallback(channel: SocketIoPrivateChannel, httpCode: number): boolean {
        let bag: ISubscriptionFallbackBagItem | undefined = this.subscriptionFallbackBag.find((r: ISubscriptionFallbackBagItem) => r.channelName === String(channel.name));

        if (!bag) {
            bag = {
                channelName: String(channel.name),
                occurrences: 0,
                httpCodes: []
            };

            this.subscriptionFallbackBag.push(bag);
        }

        if (!window.config.production) {
            console.warn(`Trying fallback ... ${bag.channelName} ${bag.occurrences}`);
        }

        if (!this.authenticationService.isRotationNeeded()) {
            if (bag.occurrences < this.subscriptionFallbackThreshold) {
                bag.occurrences++;
                bag.httpCodes.push(httpCode);
                channel.subscribe();
                if (!window.config.production) {
                    console.warn(`Trying resubscribe ... ${bag.channelName} ${bag.occurrences}`);
                }
            } else {
                if (!window.config.production) {
                    console.warn(`Reached threshold ... ${bag.channelName} ${bag.occurrences}`);
                    console.warn('HTTP codes occurred:');
                    console.warn(bag.httpCodes);

                    return false;
                }
            }
        }

        return true;
    }

    private _settings(): object {
        return {
            client: io,
            broadcaster: 'socket.io',
            host: window.endpoints.echo,
            namespace: '',
            auth: {
                headers: this.headerService.defaultHeaders()
            },
            csrfToken: this.authenticationService.token
        };
    }

    private createEchoConnection(): Echo {
        return new Echo(this._settings());
    }
}
