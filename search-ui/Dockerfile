FROM php:7-apache

ENV SERVICE_NAME=search-ui \
    SERVICE_8080_NAME=search-ui \
    SERVICE_DESC='Interface for searching' \
    SERVICE_TAGS='micro-service,search-ui' \
    SERVICE_CHECK_HTTP=/health-check.php \
    SERVICE_CHECK_INTERVAL=15s \
    SERVICE_CHECK_TIMEOUT=2s  

WORKDIR /usr/app

EXPOSE 80
