FROM registry.gitlab.trisk.us/supplies/docker/php-fpm:7.4

COPY ./crontab /etc/cron.d

RUN chmod -R 644 /etc/cron.d
RUN crontab /etc/cron.d/laravel

ADD ./supervisor/laravel.conf /etc/supervisor/conf.d/laravel.conf

CMD ["/usr/bin/supervisord"]
