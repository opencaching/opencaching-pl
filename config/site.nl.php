<?php

/**
 * General site properties configuration
 *
 * Those are configuration overrides for OCNL node only.
 */

/**
 * Site name for the node
 */
$site['siteName'] = 'Opencaching.nl';

/**
 * Page title (to display on the browser titlebar)
 */
$site['pageTitle'] = 'Opencaching Benelux';

/**
 * NodeId: globally unique ID of opencaching node
 * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
 */
$site['ocNodeId'] = 14;

/**
 * Site main domain - this should be only main part of domain eg. opencaching.pl for OCPL
 */
$site['mainDomain'] = 'opencaching.nl';

/**
 * Primary countries for this node.
 */
$site['primaryCountries'] = ['NL', 'BE', 'LU'];

/**
 * List of default countries used to present on countries list (for example in search).
 * List will be presented in same order as below.
 * Use only two-letters codes UPPERCASE.
 */
$site['defaultCountriesList'] = ['NL', 'BE', 'LU', 'DE', 'FR', 'GB'];

/**
 * Icons customization
 */
//$site['mainViewIcons']['shortcutIcon'] = '/images/icons/oc_icon-nl.png';

$site['mainViewIcons'] = [
    'shortcutIcon' => '/images/icons/oc_icon-nl.png',
    'appleTouch' => '/images/icons/apple-touch-icon-nl.png',
    'icon32' => '/images/icons/favicon-nl-32x32.png',
    'icon16' => '/images/icons/favicon-nl-16x16.png',
    //'webmanifest' => '/images/icons/site.webmanifest',    // <link rel="manifest"
    //'maskIcon' => '/images/icons/safari-pinned-tab.svg',  // <link rel="mask-icon"
];
