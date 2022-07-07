ARG PHP_VERSION
FROM bref/tmp/cleaned-build-php-$PHP_VERSION

COPY bootstrap /opt/bootstrap
COPY bootstrap.php /opt/bref/bootstrap.php
COPY breftoolbox.php /opt/bref/breftoolbox.php
COPY php.ini /opt/bref/etc/php/conf.d/bref.ini
COPY gitconfig /opt/bref/etc/gitconfig

# Remove PHP-FPM
RUN rm /opt/bref/sbin/php-fpm /opt/bin/php-fpm

# Build the final image from the amazon image that is close to the production environment
FROM public.ecr.aws/lambda/provided:al2
COPY --from=0 /opt /opt
# Copy files to /var/runtime to support deploying as a Docker image
RUN cp /opt/bootstrap /var/runtime

# Let's grab composer from the official composer image.
COPY --from=composer /usr/bin/composer /opt/bin/composer
