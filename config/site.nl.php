<?php

/**
 * Configuration of general site properties of OC NL
 */



/**
 * Site name for the node
 */
$site['siteName'] = 'Opencaching.nl';

/**
 * Page title (to display on the browser titlebar)
 */
$site['pageTitle'] = 'Opencaching.nl';

/**
 * NodeId: globally unique ID of opencaching node
 * @see https://wiki.opencaching.eu/index.php?title=Node_IDs
 */
$site['ocNodeId'] = 14;

/**
 * Site main domain - this shoudl be only main part of domain eg. opencaching.pl for OCPL
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
$site['defaultCountriesList'] = ['DE', 'NL', 'PL', 'RO', 'UK'];
