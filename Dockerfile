FROM ttskch/nginx-php-fpm-heroku

RUN apk update \
    # install ffmpeg
    && apk add ffmpeg \
    \
    # install imagemagick, imagick and dependencies
    && apk add imagemagick \
    # just to install imagick
    && apk add autoconf g++ make libtool \
    # to use phpize and pecl
    && apk add php7-dev php7-pear \
    && pecl install imagick \
    && apk add php7-imagick \
    # delete unnecessary packages
    && apk del autoconf g++ make libtool \
    \
    # instal utils
    && apk add curl \
    \
    # remove caches to decrease image size
    && rm -rf /var/cache/apk/*

COPY docker/php.ini $PHP_INI_DIR/
COPY docker/nginx.conf $NGINX_CONFD_DIR/audio2video.me.conf

USER nonroot