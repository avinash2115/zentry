import { Component, ChangeDetectorRef, OnInit, OnDestroy } from '@angular/core';
import { animate, keyframes, style, transition, trigger } from '@angular/animations';

import { NotificationService } from '../../shared/services/notification.service';
import { Notification, NotificationType } from './notification';
import { takeUntil } from 'rxjs/operators';
import { BaseDetachedComponent } from '../../shared/classes/abstracts/component/base-detached-component';

@Component({
  selector: 'app-notification',
  templateUrl: './notification.component.html',
  styleUrls: ['./notification.component.scss'],
  animations: [
    trigger('notificationTransition', [
      transition(':enter', [
        style({transform: 'scale(0.5)', opacity: 0}),  // initial
        animate('1s cubic-bezier(.8, -0.6, 0.2, 1.5)',
            style({transform: 'scale(1)', opacity: 1}))  // final
      ]),
      transition(':leave', [
          style({transform: 'scale(1)', opacity: 1, height: '*'}),
          animate('1s cubic-bezier(.8, -0.6, 0.2, 1.5)',
              style({
                  transform: 'scale(0.5)', opacity: 0,
                  height: '0px', margin: '0px'
              }))
      ])
    ]),
  ]
})
export class NotificationListComponent extends BaseDetachedComponent implements OnInit, OnDestroy {

  notifications: Notification[] = [];

  constructor(
    protected cdr: ChangeDetectorRef,
    protected _notificationService: NotificationService
  ) {
    super(cdr)
  }

  protected addNotification(notification: Notification): void {
    this.notifications.push(notification);

    if (notification.timeout !== 0) {
      setTimeout(() => this.close(notification), notification.timeout);
    }
  }

  ngOnInit(): void {
    this._notificationService.notifications
      .pipe(takeUntil(this._destroy$))
      .subscribe(notification => {
        this.addNotification(notification)
        this.detectChanges();
      });
  }

  close(notification: Notification): void {
    this.notifications = this.notifications.filter(notif => notif.id !== notification.id);
    this.detectChanges();
  }

  className(notification: Notification): string {
    switch (notification.type) {
      case NotificationType.success:
        return 'success';
      case NotificationType.warning:
        return 'warning';
      case NotificationType.error:
        return 'error';
      default:
        return 'info';
    }
  }
  icon(notification: Notification): string {
    switch (notification.type) {
      case NotificationType.success:
        return 'check';
      case NotificationType.error:
        return 'times-circle';
      case NotificationType.warning:
      default:
        return 'info';
    }
  }
}
