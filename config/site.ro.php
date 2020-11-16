<?php
/**
 * Configuration of general site properties of OCRO
 */

/**
 * Site name for the node
 */
$site['siteName']= 'Opencaching.ro';

/**
 * Page title (to display on the browser title bar)
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
 * Settings for QR Code Generator /UserUtils/qrCodeGen
 */
$site['qrCode'] = [
    'defaultText' => 'https://www.opencaching.ro/viewcache.php?wp=OR0001',
    'defaultImage' => 'qrcode_bg.jpg',
];
