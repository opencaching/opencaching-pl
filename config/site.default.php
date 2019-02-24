<?php

/**
 * DEFAULT general site properties for ALL nodes
 */

$site = [];



/**
 * Site name for the node
 */
$site['siteName'] = 'OpenCaching';

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
 * Site main domain - this shoudl be only main part of domain eg. opencaching.pl for OCPL
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
$site['defaultCountriesList'] = ['DE', 'NL', 'PL', 'RO', 'UK'];
