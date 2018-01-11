FROM php:5.6-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libmcrypt-dev \
    libpng-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    mc \
    git \
    curl \
    libmagickwand-dev \
    mysql-client \
    cron

RUN apt-get install -y supervisor

RUN pecl install imagick
RUN docker-php-ext-enable imagick

RUN docker-php-ext-install \
    intl \
    mbstring \
    mcrypt \
    opcache \
    pdo \
    pdo_mysql \
    soap \
    iconv \
    mysqli \
    zip \
    bcmath \
    sockets

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install apcu-4.0.10 \
    && docker-php-ext-enable apcu

# Xdebug for dev environment
ARG APP_ENV=dev
RUN printf "\033[0;36m===> \033[0;33m\$APP_ENV=$APP_ENV\033[0m\n"

RUN if [ "${APP_ENV}" = "dev" ];\
    then pecl install xdebug && docker-php-ext-enable xdebug;\
    else echo "";\
  fi

RUN rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini                 /usr/local/etc/php/
COPY www.conf                /usr/local/etc/php-fpm.d/www.conf
COPY docker-entrypoint.sh    /usr/local/bin/
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/www/app/logs/supervisor
RUN mkdir -p /var/run/supervisord

RUN chmod 755 /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf" ]

#CMD ["php-fpm"]