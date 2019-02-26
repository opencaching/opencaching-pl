<?php
/**
 * Configuration of general site properties of OCPL
 */

/**
 * Site name for the node
 */
$site['siteName']= 'Opencaching.pl';

/**
 * Page title (to display on the browser titlebar)
 */
$site['pageTitle'] = 'Geocaching Opencaching Polska';

/**
 * NodeId: globally unique ID of opencaching node
 * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
 */
$site['ocNodeId'] = 2;

/**
 * Site main domain - this shoudl be only main part of domain eg. opencaching.pl for OCPL
 */
$site['mainDomain'] = 'opencaching.pl';

/**
 * Primary countries for this node.
 */
$site['primaryCountries'] = ['PL'];

/**
 * List of default countries used to present on countries list (for example in search).
 * List will be presented in same order as below.
 * Use only two-letters codes UPPERCASE.
 */
$site['defaultCountriesList'] = ['BY', 'CZ', 'DE', 'DK', 'LT', 'NL', 'PL', 'RO', 'SE', 'SK', 'GB', 'UA', 'HU'];

