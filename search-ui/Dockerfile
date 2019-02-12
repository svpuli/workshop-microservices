FROM php:7.1-apache

# Install Xdebug
RUN curl -fsSL 'https://xdebug.org/files/xdebug-2.5.1.tgz' -o xdebug.tar.gz \
    && mkdir -p xdebug \
    && tar -xf xdebug.tar.gz -C xdebug --strip-components=1 \
    && rm xdebug.tar.gz \
    && ( \
    cd xdebug \
    && phpize \
    && ./configure --enable-xdebug \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r xdebug \
    && docker-php-ext-enable xdebug

# Copy php.ini into image
COPY php.ini /usr/local/etc/php/php.ini

ENV SERVICE_NAME=search-ui \
    SERVICE_8080_NAME=search-ui \
    SERVICE_DESC='Interface for searching' \
    SERVICE_TAGS='micro-service,search-ui' \
    SERVICE_CHECK_HTTP=/health-check.php \
    SERVICE_CHECK_INTERVAL=15s \
    SERVICE__CHECK_TIMEOUT=2s  

WORKDIR /usr/app

EXPOSE 80
