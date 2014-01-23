<?php
//confirmCacheCandidate.php
//prepare the templates and include all neccessary
if (!isset ($rootpath)) $rootpath='./';
require_once('./lib/common.inc.php');
require_once('./lib/db.php');
require_once './powerTrail/powerTrailBase.php';

$no_tpl_build = false;
//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target='.$target);
    }
    else {
        $tplname = 'confirmCacheCandidate';

        tpl_set_var('ptYes', 'none');
        tpl_set_var('ptNo', 'none');
        tpl_set_var('hack', 'none');
        tpl_set_var('ptName', '');
        tpl_set_var('noRecord', 'none');
        tpl_set_var('ptId', '');

        // check if logged user is a cache owner
        $code = $_REQUEST['code'];
        $ownerDecision = (int) $_REQUEST['result'];
        $query = 'SELECT * FROM `PowerTrail_cacheCandidate`, caches WHERE `link` = :1 AND PowerTrail_cacheCandidate.CacheId = caches.cache_id';
        $db = new dataBase;
        $db->multiVariableQuery($query, $code);
        $dbData = $db->dbResultFetch();
        if($dbData ===false){ // record not found
            tpl_set_var('noRecord', 'block');
        } else {
            $ptData = powerTrailBase::getPtDbRow($dbData['PowerTrailId']);
            tpl_set_var('ptName', $ptData['name']);
            tpl_set_var('ptId', $dbData['PowerTrailId']);

            if($usr['userid'] == $dbData['user_id']){ // go on
                if($ownerDecision === 0){ // just remove cache from candidate table
                    removeDbEntery($code);
                    tpl_set_var('ptNo', 'block');
                }
                if($ownerDecision === 1){ // addcachetoPt
                    $_REQUEST['calledFromConfirm'] = 1;
                    $_REQUEST['projectId'] = $dbData['PowerTrailId'];
                    $_REQUEST['cacheId'] = $dbData['cache_id'];
                    include_once 'powerTrail/ajaxAddCacheToPt.php';
                    removeDbEntery($code);
                    if ($cacheAddedToPt == true){
                        tpl_set_var('ptYes', 'block');
                    }
                }
            } else {
                tpl_set_var('hack', 'block');
            }
        }
        if ($no_tpl_build == false) {
            //make the template and send it out
            tpl_BuildTemplate();
        }
    }
}

function removeDbEntery($code){

    $db = new dataBase;
    $query = 'DELETE FROM `PowerTrail_cacheCandidate` WHERE `link` = :1';
    $db->multiVariableQuery($query, $code);
}
?>