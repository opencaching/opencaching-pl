<?php
use Utils\Uri\Uri;
use lib\Objects\ApplicationContainer;
use lib\Objects\GeoCache\GeoCache;

require_once ('./lib/common.inc.php');

global $hide_coords;

$view = tpl_getView();
$app = ApplicationContainer::Instance();

if (($cache = GeoCache::fromCacheIdFactory($_REQUEST['cacheid'])) === null) {
    $view->redirect('/');
    exit();
}

// Chceck if gallery should be visible
if (($cache->getStatus() == GeoCache::STATUS_WAITAPPROVERS
        || $cache->getStatus() == GeoCache::STATUS_NOTYETAVAILABLE
        || $cache->getStatus() == GeoCache::STATUS_BLOCKED)
    && ($app->getLoggedUser() === null
        || ($app->getLoggedUser()->getUserId() != $cache->getOwnerId()
            && ! $app->getLoggedUser()->getIsAdmin()))) {
    $view->redirect('/');
    exit();
}

// Prepare array of log pictures
$params = [];
$query = 'SELECT `pictures`.`url`, `pictures`.`title`, `pictures`.`uuid`, `pictures`.`spoiler`, `pictures`.`object_id`
                  FROM `pictures`,`cache_logs`
                  WHERE `pictures`.`object_id`=`cache_logs`.`id` AND `cache_logs`.`deleted` = 0
                      AND `pictures`.`object_type`= 1
                      AND `cache_logs`.`cache_id`= :cacheid
                  ORDER BY `pictures`.`date_created` DESC';
$params['cacheid']['value'] = $cache->getCacheId();
$params['cacheid']['data_type'] = 'integer';
$stmt = $app->db->paramQuery($query, $params);
$logpictures = [];
while ($row = $app->db->dbResultFetch($stmt)) {
    $row['url'] = str_replace("images/uploads", "upload", $row['url']);
    if ($row['spoiler'] == '1') {
        $row['thumbUrl'] = 'tpl/stdstyle/images/thumb/thumbspoiler.gif';
    } else {
        $row['thumbUrl'] = 'thumbs.php?uuid=' . $row['uuid'];
    }
    $logpictures[] = $row;
}
$view->setVar('logpictures', $logpictures);

$view->setVar('cachepictures', $cache->getPicturesList(false));
$view->setVar('hidespoilers', ($app->getLoggedUser() === null && $hide_coords));
$view->setVar('cache', $cache);
$view->setVar('cacheicon', $cache->getCacheIcon($app->getLoggedUser()));
$view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/viewcache/viewcache.css'));
$view->loadFancyBox();
$view->setTemplate('gallery_cache');
$view->buildView();