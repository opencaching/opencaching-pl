<?php

# This script is called by Google Code after each commit. All changes commited
# to SVN are immediately updated on the production server.

ignore_user_abort(true);
set_time_limit(0);

header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0");
header("Content-Type: text/plain; charset=utf-8");

if (isset($_GET['from']) && ($_GET['from'] == 'google'))
{
	# Google doesn't care what we do output. But it cares for timeouts.
	# If I understood the docs correctly - when this request doesn't finish
	# after 15 seconds, Google will send it again. We don't want that.
	
	header("Connection: close");
	header("Content-Length: 9");
	print "Received.";
	flush();
	ob_flush();
}

# File /var/www/ocpl-update.sh contains "svn up /var/www/ocpl" command
# (and a couple of others). It is allowed to be sudo-executed by 'www-data'
# without password.

print shell_exec("sudo /var/www/ocpl-update.sh up 2>&1");
