<?php

# This script is called by Google Code after each commit. All changes commited
# to SVN are immediately updated on the production server.

ignore_user_abort(true);
set_time_limit(0);

header("Content-Type: text/plain; charset=utf-8");

# File /var/www/ocpl-update.sh contains "svn up /var/www/ocpl" command.
# It is allowed to be sudo-executed by www-data without password.

print "Running svn up...\n";
print shell_exec("sudo /var/www/ocpl-update.sh up 2>&1");

