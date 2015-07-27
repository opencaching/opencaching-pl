<?php

# This script is called by GitHub after each commit. All changes commited to
# the master branch are immediately checked out at the production server.

ignore_user_abort(true);
set_time_limit(0);

header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0");
header("Content-Type: text/plain; charset=utf-8");

# File /var/www/ocpl-update.sh contains proper "git pull" commands. It is
# allowed to be sudo-executed by 'www-data' without password. This is for
# production only; if this is a developer installation, then you should do
# updates by yourself.

print shell_exec("sudo /var/www/ocpl-update.sh 2>&1");
