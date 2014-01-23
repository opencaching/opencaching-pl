<?

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
        'SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE' => true,  # OCPL supports it
        'DATA_LICENSE_URL' => 'http://wiki.opencaching.pl/index.php/OC_PL_Conditions_of_Use',

        # Copy the rest from settings.inc.php:

        'ADMINS' => array($sql_errormail, 'rygielski@mimuw.edu.pl'),
        'FROM_FIELD' => $emailaddr,
        'DEBUG' => $debug_page,
        'DB_SERVER' => $dbserver,
        'DB_NAME' => $dbname,
        'DB_USERNAME' => $dbusername,
        'DB_PASSWORD' => $dbpasswd,
        'SITELANG' => $lang,
        'SITE_URL' => $absolute_server_URI,
        'ORIGIN_URL' => (
            isset($origin_oc_url) ? $origin_oc_url :
            ($oc_nodeid == 14) ? "http://www.opencaching.nl/" :
            "http://opencaching.pl/"
        ),
        'VAR_DIR' => rtrim($dynbasepath, '/'),
        'IMAGES_DIR' => rtrim($picdir, '/'),
        'OC_NODE_ID' => $oc_nodeid,
        'OC_COOKIE_NAME' => $cookiename.'data',
    );
}
