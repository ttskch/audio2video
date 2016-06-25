FROM nginx

RUN apt-get update
RUN apt-get install -y vim less net-tools

COPY ./default.conf /etc/nginx/conf.d/

WORKDIR /usr/share/nginx/html
