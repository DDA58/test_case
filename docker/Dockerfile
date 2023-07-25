FROM php:8.2-cli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get -y update \
    && apt-get -y install git \
      libzip-dev \
      zip \
      unzip \
      cron \
    && docker-php-ext-install zip pdo pdo_mysql
#    && pecl install xdebug \

WORKDIR application

#At 1:00 pm
RUN crontab -l | { cat; echo "0 1 * * * bash /application/docker/cron_init_before_expiration"; } | crontab -
#At 01:00 pm on day-of-month 5
RUN crontab -l | { cat; echo "0 1 5 * * bash /application/docker/cron_init_after_expired"; } | crontab -
#At 01:00 pm on day-of-month 20
RUN crontab -l | { cat; echo "0 1 20 * * bash /application/docker/cron_init_after_expired"; } | crontab -