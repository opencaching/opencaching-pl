<?php
use Utils\Uri\Uri;
use lib\Objects\GeoCache\GeoCache;

const LEAFLET_CSS = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.2.0/leaflet.css';
const LEAFLET_JS = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.2.0/leaflet.js';

require_once ('./lib/common.inc.php');

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit();
}
if (! isset($_REQUEST['wp']) || empty($_REQUEST['wp'])) {
    tpl_redirect('');
    exit();
}

$cache = new GeoCache([
    'cacheWp' => $_REQUEST['wp']
]);

$view = tpl_getView();
$view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/compass/compass.css'));
$view->addLocalCss(LEAFLET_CSS);
$view->setVar('cache', $cache);
$view->setVar('compassJs', Uri::getLinkWithModificationTime('/tpl/stdstyle/compass/compass.js'));
$view->setVar('leafletJs', LEAFLET_JS);
tpl_set_var('htmlheaders', '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">');
tpl_set_tplname('compass/compass');
tpl_BuildTemplate();