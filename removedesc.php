<?php

use src\Utils\Database\XDb;
use src\Models\GeoCache\GeoCache;
use src\Utils\I18n\Languages;
use src\Utils\I18n\I18n;
use src\Models\OcConfig\OcConfig;
use src\Models\ApplicationContainer;

require_once (__DIR__.'/lib/common.inc.php');


//cacheid
$cache_id = 0;
if (isset($_REQUEST['cacheid'])) {
    $cache_id = $_REQUEST['cacheid'];
}
$desclang = '';
if (isset($_REQUEST['desclang'])) {
    $desclang = $_REQUEST['desclang'];
}
$remove_commit = 0;
if (isset($_REQUEST['commit'])) {
    $remove_commit = $_REQUEST['commit'];
}

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();
if (!$loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

        $cache_rs = XDb::xSql(
            "SELECT `user_id`, `name` FROM `caches` WHERE `cache_id`= ? LIMIT 1", $cache_id);

        if ( $cache_record = XDb::xFetchArray($cache_rs)) {

            if ($cache_record['user_id'] == $loggedUser->getUserId() || $loggedUser->hasOcTeamRole()) {

                $desc_rs = XDb::xSql(
                    "SELECT `id`, `uuid` FROM `cache_desc` WHERE `cache_id`= ? AND `language`= ? LIMIT 1", $cache_id, $desclang);
                if ($desc_record = XDb::xFetchArray($desc_rs)) {

                    XDb::xFreeResults($desc_rs);

                    if ($remove_commit == 1) {
                        //add to removed_objects
                        XDb::xSql(
                            "INSERT INTO `removed_objects` (`id`, `localID`, `uuid`, `type`, `removed_date`, `node`)
                            VALUES ('', ?, ?, '3', NOW(), ?)",
                            $desc_record['id'], $desc_record['uuid'], OcConfig::getSiteNodeId());

                        //remove it from cache_desc
                        XDb::xSql(
                            "DELETE FROM `cache_desc` WHERE `cache_id`= ? AND `language`= ? LIMIT 1", $cache_id, $desclang);

                        // update cache-record, including last modification date
                        GeoCache::setCacheDefaultDescLang($cache_id);

                        tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
                        exit;
                    } else {
                        //commit the removement
                        $tplname = 'removedesc';

                        tpl_set_var('desclang_name', Languages::LanguageNameFromCode($desclang, I18n::getCurrentLang()));
                        tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('desclang', htmlspecialchars($desclang, ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('desclang_urlencode', htmlspecialchars(urlencode($desclang), ENT_COMPAT, 'UTF-8'));
                    }
                } else {
                    //TODO: desc not exist
                }
            } else {
                //TODO: not the owner
            }
        } else {
            //TODO: cache not exist
        }


//make the template and send it out
tpl_BuildTemplate();
