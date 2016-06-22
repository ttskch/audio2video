FROM ubuntu:16.04

RUN apt-get update
RUN apt-get install -y software-properties-common # to use apt-add-repository
RUN apt-get update

# install utils
RUN apt-get install -y vim less curl net-tools

# install ffmpeg, imagemagick
RUN apt-get install -y ffmpeg libavcodec-extra imagemagick

# install php
RUN apt-get install -y php7.0 php7.0-intl php7.0-mcrypt php7.0-xml php7.0-mbstring php7.0-opcache php-imagick

# config php
RUN sed -i -E "s/;date\.timezone =/date.timezone = Asia\/Tokyo/" /etc/php/7.0/cli/php.ini
RUN sed -i -E "s/;mbstring\.language = Japanese/mbstring.language = Japanese/" /etc/php/7.0/cli/php.ini
RUN sed -i -E "s/;mbstring\.internal_encoding =/mbstring.internal_encoding = UTF-8/" /etc/php/7.0/cli/php.ini

# install composer
RUN curl -sS https://getcomposer.org/installer | php && ln -sf /composer.phar /usr/local/bin/composer

# tweak /etc/hosts for port forwarding for 0.0.0.0:8888->8888/tcp
RUN echo "0.0.0.0 localhost" >> /etc/hosts
