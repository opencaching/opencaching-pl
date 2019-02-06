<?php

/**
 * DEFAULT general site properties for ALL nodes
 */

$site = [];

/**
 * Primary country (or countries) for this node. See site.nl.php for an
 * example setting.
 *
 * If not set in site.XX.php, the site's topelevel domain will be used as
 * primary country.
 */

$site['primaryCountries'] = [];

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
