<?php

/**
 * General site properties configuration
 *
 * Those are configuration overrides for OCRO node only.
 */

/**
 * Site name for the node
 */
$site['siteName']= 'Opencaching.ro';

/**
 * Site short name
 */
$site['shortName'] = 'OC RO';

/**
 * Page title (to display on the browser titlebar)
 */
$site['pageTitle'] = 'Opencaching România';

/**
 * NodeId: globally unique ID of opencaching node
 * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
 */
$site['ocNodeId'] = 16;

/**
 * Site main domain - this should be only main part of domain eg. opencaching.pl for OCPL
 */
$site['mainDomain'] = 'opencaching.ro';

/**
 * Primary countries for this node.
 */
$site['primaryCountries'] = ['RO'];

/**
 * List of default countries used to present on countries list (for example in search).
 * List will be presented in same order as below.
 * Use only two-letters codes UPPERCASE.
 */
$site['defaultCountriesList'] = ['DE', 'NL', 'PL', 'RO', 'GB'];

/**
 * Icons customization
 */
$site['mainViewIcons']['shortcutIcon'] = '/images/icons/oc_icon-ro.png';

/**
 * Set of icons used as website icon (favicon)
 * Check the format of the icon (size etc.) before customization in node-config files.
 */
$site['mainViewIcons'] = [
    'shortcutIcon' => '/images/icons/oc_icon-ro.svg',        // <link rel="shortcut icon"
    'appleTouch' => '/images/icons/apple-touch-icon-ro.png', // <link rel="apple-touch-icon"
    'icon32' => '/images/icons/favicon-32x32-ro.png',        // <link rel="icon" type="image/png" sizes="32x32"
    'icon16' => '/images/icons/favicon-16x16-ro.png',        // <link rel="icon" type="image/png" sizes="16x16"
    'webmanifest' => '/images/icons/site-ro.webmanifest',    // <link rel="manifest"
    'maskIcon' => '/images/icons/safari-pinned-tab-ro.svg',  // <link rel="mask-icon"
];
