FROM php:8.2-cli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY --from=ncabatoff/process-exporter:latest /bin/process-exporter /usr/local/bin/process-exporter

#COPY --from=prom/node-exporter:latest /bin/node_exporter /usr/local/bin/node_exporter

RUN apt-get -y update \
    && apt-get -y install git \
      libzip-dev \
      zip \
      unzip \
      cron \
    && docker-php-ext-install zip pdo pdo_mysql sockets
#    && pecl install xdebug \

WORKDIR application