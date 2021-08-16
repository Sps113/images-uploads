####################################
# PHPDocker.io PHP 7.1 / FPM image #
####################################

FROM phpdockerio/php74-cli

# Install FPM
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.4-fpm \
    && apt-get -y --no-install-recommends install php7.4-mbstring \
    && apt-get -y --no-install-recommends install php7.4-mysql \
    && apt-get -y --no-install-recommends install php-xdebug \
    && apt-get -y --no-install-recommends install php7.4-bcmath \
    && apt-get -y --no-install-recommends install php7.4-gd \
    && apt-get -y --no-install-recommends install php7.4-intl \
    && apt-get -y --no-install-recommends install wget \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


RUN wget https://getcomposer.org/installer --no-check-certificate
RUN php installer
# RUN ln -s /composer.phar /usr/bin/composer

# Configure FPM to run properly on docker
RUN sed -i "/listen = .*/c\listen = [::]:9000" /etc/php/7.4/fpm/pool.d/www.conf \
    && sed -i "/;access.log = .*/c\access.log = /proc/self/fd/2" /etc/php/7.4/fpm/pool.d/www.conf \
    && sed -i "/;clear_env = .*/c\clear_env = no" /etc/php/7.4/fpm/pool.d/www.conf \
    && sed -i "/;catch_workers_output = .*/c\catch_workers_output = yes" /etc/php/7.4/fpm/pool.d/www.conf \
    && sed -i "/pid = .*/c\;pid = /run/php/php7.4-fpm.pid" /etc/php/7.4/fpm/php-fpm.conf \
    && sed -i "/;daemonize = .*/c\daemonize = no" /etc/php/7.4/fpm/php-fpm.conf \
    && sed -i "/error_log = .*/c\error_log = /proc/self/fd/2" /etc/php/7.4/fpm/php-fpm.conf \
    && usermod -u 1000 www-data

RUN echo "xdebug.remote_enable=1" >> /etc/php/7.4/mods-available/xdebug.ini \
    && echo "xdebug.remote_autostart=0" >> /etc/php/7.4/mods-available/xdebug.ini \
    && echo 'xdebug.remote_enable=1' >> /etc/php/7.4/php.ini \
    && echo 'xdebug.remote_port=9000' >> /etc/php/7.4/php.ini

# Configure FPM Xdebug
#RUN echo "xdebug.remote_enable=1" >> /etc/php/7.4/mods-available/xdebug.ini \
#    && echo "xdebug.remote_autostart=0" >> /etc/php/7.4/mods-available/xdebug.ini

# The following runs FPM and removes all its extraneous log output on top of what your app outputs to stdout
CMD /usr/sbin/php-fpm7.4 -F -O 2>&1 | sed -u 's,.*: \"\(.*\)$,\1,'| sed -u 's,"$,,' 1>&1

# Open up fcgi port
EXPOSE 9000
