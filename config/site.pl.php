<?php

/**
 * General site properties configuration
 *
 * Those are configuration overrides for OCPL node only.
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
 * Site main domain - this should be only main part of domain eg. opencaching.pl for OCPL
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
$site['defaultCountriesList'] = ['PL', 'BY', 'CZ', 'DE', 'DK', 'LT', 'NL', 'RO', 'SE', 'SK', 'GB', 'UA', 'HU'];
