FROM php:8.1-fpm-bullseye

# Set package manager mode to run without any user prompts
ARG DEBIAN_FRONTEND=noninteractive

# Get latest packages
RUN apt-get update

# Define command macros
ARG apt_get_install="apt-get install -y --no-install-recommends"
ARG php_ext_install="docker-php-ext-install"
ARG php_ext_configure="docker-php-ext-configure"
ARG php_ext_enable="docker-php-ext-enable"

# Install system packages
RUN $apt_get_install curl
RUN $apt_get_install wget
RUN $apt_get_install git
RUN $apt_get_install vim
RUN $apt_get_install nano
RUN $apt_get_install zip
RUN $apt_get_install unzip
RUN $apt_get_install apt-utils
RUN $apt_get_install ca-certificates
RUN $apt_get_install wait-for-it
RUN $apt_get_install g++
RUN $apt_get_install zlib1g-dev
RUN $apt_get_install libonig-dev
RUN $apt_get_install libicu-dev
RUN $apt_get_install libbz2-dev

# Install PHP extensions
RUN $php_ext_install pdo_mysql
RUN $php_ext_install intl
RUN $php_ext_install bz2
RUN $php_ext_install opcache

# Configure the service
COPY config/settings.ini "$PHP_INI_DIR/conf.d"
RUN ln -s "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN useradd --no-log-init -m -u 1000 php && groupmod -g 1000 php

# Install Composer: https://getcomposer.org/download/
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer

# Configure miscellaneous settings
RUN ln -snf /usr/share/zoneinfo/Europe/Prague /etc/localtime
RUN echo 'alias ll="ls -alk"' >> ~/.bashrc
RUN echo 'alias composer="runuser -u php -- composer"' >> ~/.bashrc

# Expose ports to Docker network
EXPOSE 9000
