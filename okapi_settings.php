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

    require(__DIR__.'/lib/settings.inc.php');  # (into the *local* scope)

    $json = file_get_contents(__DIR__.'/var/okapi_settings.json');
    $dynamicSettings = json_decode($json, true);

    return [
        # These first section of settings is OKAPI-specific, OCPL's
        # settings.inc.php file does not provide them. For more
        # OKAPI-specific settings, see okapi/settings.php file.

        'OC_BRANCH' => 'oc.pl',
        'EXTERNAL_AUTOLOADER' => __DIR__.'/lib/ClassPathDictionary.php',
        'USE_SQL_SUBQUERIES' => true,

        # These settings will stay in local configuration

        'DB_SERVER' => $dbserver,
        'DB_NAME' => $dbname,
        'DB_USERNAME' => $dbusername,
        'DB_PASSWORD' => $dbpasswd,
        'DEBUG' => $debug_page,
    ]
        # Load the rest from OCPL settings (add the associative arrays):

        + $dynamicSettings;
}
