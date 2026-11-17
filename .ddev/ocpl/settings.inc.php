<?php

require __DIR__ . '/settings-example.inc.php';

$config['cookie']['domain'] = '.ocpl.ddev.site';
$config['okapi']['admin_emails'] = ['admin@ocpl.ddev.site'];

$absolute_server_URI = 'https://ocpl.ddev.site/';

$dynbasepath = '/var/www/html/ocpl-dynamic-files/';
$mp3dir = $dynbasepath . 'mp3';
$mp3url = 'https://ocpl.ddev.site/mp3';

$dbserver = 'db';
$dbname = 'db';
$dbusername = 'db';
$dbpasswd = 'db';

$opt['db']['admin_username'] = 'root';
$opt['db']['admin_password'] = 'root';
