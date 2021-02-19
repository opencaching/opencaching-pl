<?php

/**
 * General site properties configuration
 *
 * Those are configuration overrides for OCUK node only.
 */

/**
 * Site name for the node
 */
$site['siteName'] = 'OPENCACHE.uk';

/**
 * Page title (to display on the browser titlebar)
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
