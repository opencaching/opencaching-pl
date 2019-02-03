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

    require(__DIR__.'/lib/settingsGlue.inc.php');  # (into the *local* scope)

    return [
        # OKAPI-specific settings which are not taken from OCPL settings.
        # For more OKAPI-specific settings, see okapi/Settings.php file.

        'OC_BRANCH' => 'oc.pl',
        'EXTERNAL_AUTOLOADER' => __DIR__.'/lib/ClassPathDictionary.php',
        'USE_SQL_SUBQUERIES' => true,

        # These settings will stay in local settings.inc.php.

        'DB_SERVER' => $dbserver,
        'DB_NAME' => $dbname,
        'DB_USERNAME' => $dbusername,
        'DB_PASSWORD' => $dbpasswd,
        'DEBUG' => $debug_page,

        # These settings will be refactored to UpdateController::updateOkapiSettings().

        'ADMINS' => ($config['okapi']['admin_emails'] ? $config['okapi']['admin_emails'] : array($sql_errormail, 'rygielski@mimuw.edu.pl', 'following@online.de')),
        'DATA_LICENSE_URL' => $config['okapi']['data_license_url'],
        'FROM_FIELD' => $emailaddr,
        'SITE_URL' => isset($OKAPI_server_URI) ? $OKAPI_server_URI : $absolute_server_URI,
        'IMAGES_DIR' => rtrim($picdir, '/'),
        'IMAGES_URL' => rtrim($picurl, '/').'/',
        'IMAGE_MAX_UPLOAD_SIZE' => $config['limits']['image']['filesize'] * 1024 * 1024,
        'IMAGE_MAX_PIXEL_COUNT' => $config['limits']['image']['height'] * $config['limits']['image']['width'],
        'OC_COOKIE_NAME' => $config['cookie']['name'].'_auth',
        'TILEMAP_FONT_PATH' => $config['okapi']['tilemap_font_path'],
    ]
        # Load the rest from OCPL Config (add the associative arrays):

        + include __DIR__.'/var/okapi_settings.inc.php';
}
