#!/usr/bin/env bash

# Creates dynamic file directories and copies local config templates that don't exist yet.

set -euo pipefail
cd /var/www/html

mkdir -p ocpl-dynamic-files/{images/{mini-mapa,statpics,uploads,upload/thumbnails},lib/templates_c,mp3,searchdata,tmp,tpl,wigo,download/zip}

if [ ! -f lib/settings.inc.php ]; then
    cp .ddev/ocpl/settings.inc.php lib/settings.inc.php
fi

for template in .ddev/ocpl/config/*.local.php; do
    target=config/$(basename "$template")
    if [ ! -f "$target" ]; then
        cp "$template" "$target"
    fi
done
