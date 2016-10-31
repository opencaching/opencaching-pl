<?php

namespace okapi;

function get_okapi_settings()
{
    # This comment is here for all international OC developers. This
    # file serves as an example of how to implement it on other sites.
    # These settings are for OCPL site, but they should work with most
    # others too.

    # Note, that this file should be located outside of OKAPI directory,
    # directly in your root path.

    # OKAPI needs this file present. Every OC site has to provide it.
    # Since OC sites differ (they don't even have a common-structured
    # settings file), all sites have to provide a valid mapping from
    # *their* settings to OKAPI settings.

    # Note, that this function needs to execute FAST. If your default
    # settings file is not a simple $variable="value" mapping, then you
    # *should* *hardcode* the settings below, instead of including your
    # slow settings file!

    # OKAPI defines only *one* global variable, named 'rootpath'.
    # You may access it to get a proper path to your own settings file.

    require($GLOBALS['rootpath'].'lib/settings.inc.php');  # (into the *local* scope)

    return array(
        # These first section of settings is OKAPI-specific, OCPL's
        # settings.inc.php file does not provide them. For more
        # OKAPI-specific settings, see okapi/settings.php file.

        'OC_BRANCH' => 'oc.pl',
        # Copy the rest from settings.inc.php:

        'DATA_LICENSE_URL' => $config['okapi']['data_license_url'],
        'ADMINS' => ($config['okapi']['admin_emails'] ? $config['okapi']['admin_emails'] : array($sql_errormail, 'rygielski@mimuw.edu.pl')),
        'FROM_FIELD' => $emailaddr,
        'DEBUG' => $debug_page,
        'DB_SERVER' => $dbserver,
        'DB_NAME' => $dbname,
        'DB_USERNAME' => $dbusername,
        'DB_PASSWORD' => $dbpasswd,
        'SITELANG' => $lang,
        'SITE_URL' => isset($OKAPI_server_URI) ? $OKAPI_server_URI : $absolute_server_URI,
        'VAR_DIR' => rtrim($dynbasepath, '/'),
        'IMAGES_DIR' => rtrim($picdir, '/'),
        'IMAGES_URL' => rtrim($picurl, '/').'/',
        'IMAGE_MAX_UPLOAD_SIZE' => $config['limits']['image']['filesize'] * 1024 * 1024,
        'IMAGE_MAX_PIXEL_COUNT' => $config['limits']['image']['height'] * $config['limits']['image']['width'],
        'OC_NODE_ID' => $oc_nodeid,
        'OC_COOKIE_NAME' => $cookiename.'data',
        //'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => isset($enable_cache_access_logs) ? $enable_cache_access_logs : false
        'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => false
    );
}
