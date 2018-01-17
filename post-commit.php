<?php

require_once __DIR__ . '/lib/settingsGlue.inc.php';

# This script is called by GitHub after each commit. All changes commited to
# the master branch are immediately checked out at the production server.

ignore_user_abort(true);
set_time_limit(0);

header("Cache-Control: no-store");
header("Content-Type: text/plain; charset=utf-8");

# Script file ocpl-update.sh contains proper "git pull" commands. 
# This file is located at /var/www (by default) or customized by
# $config['server']['update']['script'].
# System configuration must be set such that it is allowed to be sudo-executed 
# by the webserver user ('www-data', 'www' or 'apache', depending on your
# distro) without password. 
# For production, your node must be added to receive update notifications
# from the repository at Github: https://github.com/opencaching/opencaching-pl
#
# This is for production only.
#
# If this is a developer installation, then you should do updates by yourself.

print shell_exec("sudo " . $config['server']['update']['script'] . " 2>&1");

?>