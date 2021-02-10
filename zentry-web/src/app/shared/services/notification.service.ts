import { Injectable } from '@angular/core';
import { Subject, Observable } from 'rxjs';
import { Notification, NotificationType } from '../../components/notification/notification';

@Injectable()
export class NotificationService {

  private notification$ = new Subject<Notification>();
  private _idx = 0;

  constructor() { }

  get notifications(): Observable<Notification> {
    return this.notification$.asObservable();
  }

  info(message: string, timeout: number = 3000): void {
    this.notification$.next(new Notification(this._idx++, NotificationType.info, message, timeout));
  }

  success(message: string, timeout: number = 3000): void {
    this.notification$.next(new Notification(this._idx++, NotificationType.success, message, timeout));
  }

  warning(message: string, timeout: number = 3000): void {
    this.notification$.next(new Notification(this._idx++, NotificationType.warning, message, timeout));
  }

  error(message: string, timeout: number = 0): void {
    this.notification$.next(new Notification(this._idx++, NotificationType.error, message, timeout));
  }

}
