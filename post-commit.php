<?php

# This script is called by Google Code after each commit. All changes commited
# to SVN are immediately updated on the production server.

ignore_user_abort(true);
set_time_limit(0);

header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0");
header("Content-Type: text/plain; charset=utf-8");

# File /var/www/ocpl-update.sh contains "svn up /var/www/ocpl" command
# (and a couple of others). It is allowed to be sudo-executed by 'www-data'
# without password.

print shell_exec("sudo /var/www/ocpl-update.sh up 2>&1");
