#!/bin/sh
set -eu

for dir in \
    images/uploads \
    wigo \
    download \
    images/statpics \
    images/mini-mapa \
    mp3 \
    tmp
do
    mkdir -p "/srv/ocpl-dynamic-files/$dir"
done

chown -R www-data:www-data /srv/ocpl-dynamic-files

exec /usr/local/bin/docker-php-entrypoint "$@"
