<?php

namespace okapi;

use src\Models\OcConfig\OcConfig;

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

    # prepare settings for OKAPI
    return [
        # These first section of settings is OKAPI-specific, OCPL's
        # settings.inc.php file does not provide them. For more
        # OKAPI-specific settings, see okapi/settings.php file.

        'OC_BRANCH' => 'oc.pl',
        'EXTERNAL_AUTOLOADER' => __DIR__.'/lib/ClassPathDictionary.php',
        # Copy the rest from settings.inc.php:

        'DATA_LICENSE_URL' => $config['okapi']['data_license_url'],
        'ADMINS' => ($config['okapi']['admin_emails'] ? $config['okapi']['admin_emails'] :
            array('techNotify@opencaching.pl','rygielski@mimuw.edu.pl')),

        'FROM_FIELD' => OcConfig::getEmailAddrNoReply(),
        'DEBUG' => OcConfig::debugModeEnabled(),
        'DB_SERVER' => OcConfig::getDbHost(),
        'DB_NAME' => OcConfig::getDbName(),
        'DB_USERNAME' => OcConfig::getDbUser(),
        'DB_PASSWORD' => OcConfig::getDbPass(),
        'SITELANG' => OcConfig::getI18nDefaultLang(),
        'SITE_URL' => isset($OKAPI_server_URI) ? $OKAPI_server_URI : $absolute_server_URI,
        'VAR_DIR' => OcConfig::getDynFilesPath(TRUE),
        'TILEMAP_FONT_PATH' => $config['okapi']['tilemap_font_path'],
        'IMAGES_DIR' => rtrim(OcConfig::getPicUploadFolder(), '/'),
        'IMAGES_URL' => rtrim((isset($OKAPI_server_URI) ? $OKAPI_server_URI : $absolute_server_URI) . OcConfig::getPicBaseUrl(), '/').'/',
        'IMAGE_MAX_UPLOAD_SIZE' => OcConfig::getPicMaxSize() * 1024 * 1024,
        'IMAGE_MAX_PIXEL_COUNT' => $config['limits']['image']['height'] * $config['limits']['image']['width'],
        'OC_NODE_ID' => OcConfig::getSiteNodeId(),
        'OC_COOKIE_NAME' => $config['cookie']['name'].'_auth',
        'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => false,
        'REGISTRATION_URL' => (isset($OKAPI_server_URI) ? $OKAPI_server_URI : $absolute_server_URI) . 'UserRegistration',
        'USE_SQL_SUBQUERIES' => true,
        'CRON_JOBS_BLACKLIST' => OcConfig::getOkapiCronJobBlacklist(),
    ];
}
