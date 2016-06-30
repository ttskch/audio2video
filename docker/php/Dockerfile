FROM ubuntu:16.04

RUN apt-get update
RUN apt-get install -y software-properties-common # to use apt-add-repository
RUN apt-get update

# install utils
RUN apt-get install -y git unzip vim less curl net-tools

# install ffmpeg, imagemagick
RUN apt-get install -y ffmpeg libavcodec-extra imagemagick

# install php
RUN apt-get install -y php7.0 php7.0-intl php7.0-mcrypt php7.0-xml php7.0-mbstring php7.0-opcache php-imagick

# config php
RUN sed -i -E "s/;date\.timezone =/date.timezone = Asia\/Tokyo/" /etc/php/7.0/fpm/php.ini
RUN sed -i -E "s/;mbstring\.language = Japanese/mbstring.language = Japanese/" /etc/php/7.0/fpm/php.ini
RUN sed -i -E "s/;mbstring\.internal_encoding =/mbstring.internal_encoding = UTF-8/" /etc/php/7.0/fpm/php.ini

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# config php7.0-fpm
RUN sed -i -E "s/listen = /;listen = /" /etc/php/7.0/fpm/pool.d/www.conf
RUN echo "listen = :::9000" >> /etc/php/7.0/fpm/pool.d/www.conf

WORKDIR /var/www/html

EXPOSE 9000
CMD service php7.0-fpm start && tail -f /dev/null
