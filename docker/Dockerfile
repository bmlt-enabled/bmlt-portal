FROM ubuntu:jammy

# Install packages
ENV TZ=America/New_York
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt-get update \
    && apt-get -yqq install \
        software-properties-common \
        apache2 \
        curl \
        libapache2-mod-php \
        libcurl4-gnutls-dev \
        libmcrypt-dev \
        libpng-dev \
        libxml2-dev \
        mcrypt \
        unzip

# Install older php
RUN apt-get update \
    && add-apt-repository ppa:ondrej/php \
    && apt-get -yqq install \
        php8.0 \
        php8.0-curl \
        php8.0-dev \
        php8.0-gd \
        php8.0-mbstring \
        php8.0-mysql \
        php8.0-pdo \
        php8.0-xdebug \
        php8.0-xml \
        php8.0-zip \
    && update-alternatives --set php /usr/bin/php8.0

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Configure apache
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_RUN_DIR www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
RUN /bin/bash -c "source /etc/apache2/envvars"
RUN sed -i 's/Listen 80/Listen 8000/' /etc/apache2/ports.conf
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
    ln -sf /proc/self/fd/1 /var/log/apache2/error.log
RUN a2enmod rewrite \
     && a2dismod php8.1 \
     && a2enmod php8.0

RUN rm /var/www/html/index.html

# Configure apache php
ENV PHP_INI_PATH /etc/php/8.0/apache2/php.ini
ENV PHP_CLI_INI_PATH /etc/php/8.0/cli/php.ini
ENV PHP_XDEBUG_ENABLED: 1
RUN echo "zend_extension=$(find /usr/lib/php/ -name xdebug.so)" >> ${PHP_INI_PATH} \
    && echo "xdebug.mode=debug" >> ${PHP_INI_PATH} \
    && echo "xdebug.client_port=9003" >> ${PHP_INI_PATH} \
    && echo "xdebug.client_host=host.docker.internal" >> ${PHP_INI_PATH} \
    && echo "xdebug.start_with_request=yes" >> ${PHP_INI_PATH} \
    && echo "xdebug.log=/tmp/xdebug.log" >> ${PHP_INI_PATH} \
    && echo "xdebug.idekey=ROOT_SERVER_DEBUG" >> ${PHP_INI_PATH}

RUN echo "zend_extension=$(find /usr/lib/php/ -name xdebug.so)" >> ${PHP_CLI_INI_PATH} \
    && echo "xdebug.mode=debug" >> ${PHP_CLI_INI_PATH} \
    && echo "xdebug.client_port=9003" >> ${PHP_CLI_INI_PATH} \
    && echo "xdebug.client_host=host.docker.internal" >> ${PHP_CLI_INI_PATH} \
    && echo "xdebug.start_with_request=yes" >> ${PHP_CLI_INI_PATH} \
    && echo "xdebug.log=/tmp/xdebug.log" >> ${PHP_CLI_INI_PATH} \
    && echo "xdebug.idekey=ROOT_SERVER_DEBUG" >> ${PHP_CLI_INI_PATH}

EXPOSE 8000

CMD ["apachectl", "-D", "FOREGROUND"]
