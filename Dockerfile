FROM debian:bullseye

# setup basic installation
ENV DEBIAN_FRONTEND noninteractive

RUN dpkg --configure -a && \
	apt-get update -y && \
	apt-get upgrade -y && \
	mkdir -p /var/lib/dpkg

# get them
RUN apt-get update -y && apt-get install -f -y \
    php-fpm \
    php-cli \
    php-curl \
    php-mbstring \
    php-opcache \
    php-xml \
    php-zip \
    locales \
    gnupg2 \
    supervisor \
    procps \
    nginx \
    build-essential \
    pkg-config \
    wkhtmltopdf \
    unzip \
    vim \
    wget \
    curl

# Install Composer and hen the php-fpm library
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Setup the run script
COPY ./presets/locale.gen /etc/locale.gen
COPY ./presets/run.sh /run.sh
ADD ./presets/fpm.conf /etc/php/7.4/fpm/pool.d/nginx.conf
ADD ./presets/supervisor.conf /etc/supervisord.conf
ADD ./presets/nginx.conf /etc/nginx/sites-available/default.conf
ADD ./presets/fastcgi.conf /app/fastcgi.conf

# configure locales
RUN /usr/sbin/locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# build the required folders
RUN chmod a+x /run.sh  && \
    useradd -d /app nginx  && \
    mkdir -p /var/log  && \
    mkdir -p /var/run/php-fpm && \
    mkdir -p /var/log/app && \
    mkdir -p /run/php/ && \
    touch /var/log/nginx/access.log && \
    rm /etc/nginx/sites-available/default && \
    rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf && \
    ln -s /etc/init.d/php7.4-fpm /etc/init.d/php-fpm && \
    rm /etc/php/7.4/fpm/pool.d/www.conf && \
    usermod www-data --shell /bin/bash

# add app and install libs
ADD ./presets/app /app
RUN chown www-data:www-data /app -R

# install additional packages
RUN cd "/app" && /usr/local/bin/composer install

# Expose ports, set working directory and execute the run script
WORKDIR /app
EXPOSE 80
CMD ["/run.sh"]
