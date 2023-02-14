FROM php:8.1-apache

# Install SUPERVISOR
RUN apt-get update && \
    apt-get install --yes --force-yes \
    cron g++ gettext libicu-dev openssl \
    libc-client-dev libkrb5-dev  \
    libxml2-dev libfreetype6-dev \
    libgd-dev libmcrypt-dev bzip2 \
    libbz2-dev libtidy-dev libcurl4-openssl-dev \
    libz-dev libmemcached-dev libxslt-dev git-core libpq-dev \
    libzip4 libzip-dev libwebp-dev zsh \
    && apt-get install -y supervisor curl \
    && rm -rf /var/lib/apt/lists/*



# PHP Configuration
RUN docker-php-ext-install bcmath bz2 calendar  dba exif gettext iconv intl  soap tidy xsl zip&&\
    docker-php-ext-install mysqli pdo pdo_mysql   &&\
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp &&\
    docker-php-ext-install gd &&\
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl &&\
    docker-php-ext-install imap &&\
    docker-php-ext-configure hash --with-mhash &&\
    pecl install xdebug && docker-php-ext-enable xdebug &&\
    pecl install xhprof && docker-php-ext-enable xhprof
    #pecl install mongodb && docker-php-ext-enable mongodb &&\

# OpCache
RUN docker-php-ext-install opcache
COPY conf/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Xdebug
COPY conf/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY conf/001-app.conf /etc/apache2/sites-available/001-app.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf &&\
    a2enmod rewrite &&\
    a2dissite 000-default &&\
    a2ensite 001-app

RUN apache2ctl start

# Set USER
USER www-data
WORKDIR /srv/app


