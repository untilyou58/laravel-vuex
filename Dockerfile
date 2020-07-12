FROM php:7.4.1-fpm

COPY install-composer.sh /
RUN apt-get update \
  && apt-get install -y wget git \
    autoconf \
    build-essential \
    apt-utils \
    zlib1g-dev \
    libzip-dev \
    unzip \
    zip \
    libmagick++-dev \
    libmagickwand-dev \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
  && : 'Install Node.js' \
  &&  curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs \
  && : 'Install PHP Extensions' \
  && docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install -j$(nproc) pdo_pgsql \
  && : 'Install Composer' \
  && chmod 755 /install-composer.sh \
  && /install-composer.sh \
  && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
  && chown -R root:www-data /var/www/html \
  && chmod u+rwx,g+rx,o+rx /var/www/html \
  && find /var/www/html -type d -exec chmod u+rwx,g+rx,o+rx {} + \
  && find /var/www/html -type f -exec chmod u+rw,g+rw,o+r {} + \
  && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html/vue
