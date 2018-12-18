FROM php:7.2.12-cli-alpine3.8
RUN apk --no-cache add curl git

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
# adding ability to install composer packages in parallel for speedup
RUN composer global require hirak/prestissimo

WORKDIR /app

ENTRYPOINT [""]
