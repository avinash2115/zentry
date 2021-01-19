FROM php:7.4.11-fpm

# install main dependencies

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    ffmpeg \
  && rm -rf /var/lib/apt/lists/*

# install sub dependencies

RUN rm /etc/apt/preferences.d/no-debian-php

RUN apt-get update -yqq && \
    apt-get -y install libxml2-dev php-soap && \
    docker-php-ext-install soap && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install pdo_pgsql && \
    docker-php-ext-configure gd \
    --with-jpeg=/usr/lib \
    --with-freetype=/usr/include/freetype2 && \
    docker-php-ext-install gd


# install mcrypt dependencies

RUN pecl install mcrypt-1.0.3 && \
        docker-php-ext-enable mcrypt

# install redis extension

RUN printf "\n" | pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# install mongo extension

RUN pecl install mongodb-1.8.0 && \
    docker-php-ext-enable mongodb

# install OpCache extension

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# install Composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.16

# Run Composer Install to a Mars

RUN composer global require hirak/prestissimo

ADD ./laravel.ini /usr/local/etc/php/conf.d
ADD ./xlaravel.pool.conf /usr/local/etc/php-fpm.d/

# install WKHTMLTOPDF

RUN apt-get update && apt-get install -y xvfb xauth wkhtmltopdf

# install Cron and Supervisor

RUN apt-get update && apt-get install -y cron supervisor

RUN touch /var/log/cron.log

COPY ./supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# install PDFTK

RUN mkdir -p /usr/share/man/man1
RUN apt-get install -y pdftk

WORKDIR /var/www

CMD ["/usr/bin/supervisord"]

EXPOSE 9000
