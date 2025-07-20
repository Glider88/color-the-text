FROM dunglas/frankenphp:php8.4

# Set Caddy server name to "http://" to serve on 80 and not 443
ENV SERVER_NAME="http://"

WORKDIR /usr/src/app

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions xdebug pgsql pdo_pgsql

ENV USER=docker
ENV GROUPNAME=$USER
ENV UID=1000
ENV GID=1000

RUN addgroup --gid "$GID" "$GROUPNAME" \
 && adduser --disabled-password --gecos "" --no-create-home --ingroup "$GROUPNAME" --uid "$UID" $USER

RUN \
  # Add additional capability to bind to port 80 and 443
  setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
  # Give write access to /data/caddy and /config/caddy
  chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy

# add /config/psysh for laravel tinker
RUN mkdir /config/psysh && chown -R ${USER}:${USER} /config/psysh

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
