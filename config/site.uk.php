<?php
/**
 * Configuration of general site properties of OC UK
 */

/**
 * Site name for the node
 */
$site['siteName'] = 'OPENCACHE.uk';

/**
 * Page title (to display on the browser title bar)
 */
$site['pageTitle'] = 'Geocaching Opencaching UK';

/**
 * NodeId: globally unique ID of opencaching node
 * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
 */
$site['ocNodeId'] = 6;

/**
 * Site main domain - this should be only main part of domain eg. opencaching.pl for OCPL
 */
$site['mainDomain'] = 'opencache.uk';

/**
 * Primary countries for this node.
 *
 * UK is one of the few countries where the TLD differs from ISO-3166 code!
 */
$site['primaryCountries'] = ['GB'];

/**
 * List of default countries used to present on countries list (for example in search).
 * List will be presented in same order as below.
 * Use only two-letters codes UPPERCASE.
 */
$site['defaultCountriesList'] = ['DE', 'NL', 'PL', 'RO', 'GB'];

/**
 * Settings for QR Code Generator /UserUtils/qrCodeGen
 */
$site['qrCode'] = [
    'defaultText' => 'https://opencache.uk/viewcache.php?wp=OK0001',
    'defaultImage' => 'qrcode_bg.jpg',
];
