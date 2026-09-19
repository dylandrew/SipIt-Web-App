FROM php:8.3-apache-bookworm

RUN docker-php-ext-install -j"$(nproc)" mbstring \
    && a2enmod rewrite

COPY . /var/www/html/

RUN printf '%s\n' \
    '<Directory /var/www/html>' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    > /etc/apache2/conf-available/sipit.conf \
    && a2enconf sipit \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
