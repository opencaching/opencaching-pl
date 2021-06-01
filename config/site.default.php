<?php

/**
 * General site properties configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

$site = [];

/**
 * Site name for the node
 */
$site['siteName'] = 'OpenCaching';

/**
 * Site short name
 */
$site['shortName'] = 'OC';

/**
 * Page title (to display on the browser titlebar)
 */
$site['pageTitle'] = 'OpenCaching';

/**
 * NodeId: globally unique ID of opencaching node
 * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
 */
$site['ocNodeId'] = 4;

/**
 * Site main domain - this should be only main part of domain eg. opencaching.pl for OCPL
 */
$site['mainDomain'] = 'opencaching.pl';

/**
 * Primary country (or countries) for this node.
 * Primary countries are base countries for this node.
 *
 * See site.nl.php for an example setting.
 *
 * If not set in site.XX.php, the site's topelevel domain will be used as
 * primary country.
 */
$site['primaryCountries'] = [];

/**
 * List of default countries used to present on countries list (for example in search).
 * List will be presented in same order as below.
 * Use only two-letters codes UPPERCASE.
 */
$site['defaultCountriesList'] = ['DE', 'NL', 'PL', 'RO', 'GB'];

/**
 * Set of icons used as website icon (favicon)
 * Check the format of the icon (size etc.) before customization in node-config files.
 */
$site['mainViewIcons'] = [
    'shortcutIcon' => '/images/icons/oc_icon.png',        // <link rel="shortcut icon"
    'appleTouch' => '/images/icons/apple-touch-icon.png', // <link rel="apple-touch-icon"
    'icon32' => '/images/icons/favicon-32x32.png',        // <link rel="icon" type="image/png" sizes="32x32"
    'icon16' => '/images/icons/favicon-16x16.png',        // <link rel="icon" type="image/png" sizes="16x16"
    'webmanifest' => '/images/icons/site.webmanifest',    // <link rel="manifest"
    'maskIcon' => '/images/icons/safari-pinned-tab.svg',  // <link rel="mask-icon"
];

/**
 * Save all accesses to geocaches in DB for debug/security purpose
 */
$site['cacheAccessLogEnabled'] = false;

/**
 * Enable debug mode (USE ONLY IN DEV/TEST ENV!)
 */
$site['debugModeEnabled'] = false;

/**
 * Display the list of users which are online (was seens in last minutes)
 * on the bottom of the page
 */
$site['displayOnlineUsers'] = true;

