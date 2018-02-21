FROM ttskch/nginx-php-fpm-heroku

RUN \
    apk update \
    \
    # for simple-phpunit
    # @see https://symfony.com/doc/current/components/phpunit_bridge.html
    && apk add php7-zip \
    \
    # install ffmpeg
    && apk add ffmpeg \
    \
    # install imagemagick, imagick and dependencies
    && apk add imagemagick imagemagick-dev \
    # just to install imagick
    && apk add autoconf g++ make libtool \
    # to use phpize and pecl
    && apk add php7-dev php7-pear \
    && pecl install imagick \
    && apk add php7-imagick \
    # delete unnecessary packages
    && apk del --purge autoconf g++ make libtool \
    \
    # instal utils
    && apk add curl \
    && apk add nodejs-npm \
    \
    # remove caches to decrease image size
    && rm -rf /var/cache/apk/* \
    \
    # re-run composer install
    # (sudo -u nonroot is required because cannot run npm install by root without --unsafe-perm)
    && sudo -u nonroot composer install --no-interaction \
    #  tweak to set env to prod
    && sed -i -E "s/APP_ENV=dev/APP_ENV=prod/" .env \
    && mv config/routes/annotations.yaml.prod config/routes/annotations.yaml \
    && chmod -R a+w $DOCROOT

COPY docker/php.ini $PHP_INI_DIR/
COPY docker/nginx.conf $NGINX_CONFD_DIR/audio2video.me.conf

USER nonroot
