<?php
use Utils\Database\XDb;
/*
  Save last used user map settings in DB
  This is called by ajax request from map scripts.
*/

require_once('./lib/common.inc.php');

global $usr;

if ($usr != true)
    exit(1);


if(isset($_GET['map_v']) && ($_GET['map_v'] == 3 )) { //this is request from map in ver. 3

    $columns[] = 'user_id ='. intval($usr['userid']);

    if( isset($_GET['maptype']) )
        $columns[] = 'maptype = '.intval($_GET['maptype']);

    if( isset($_GET['h_u']) )
        $columns[] = 'unknown = '.($_GET['h_u'] == 'true' ? 0 : 1);
    if( isset($_GET['h_t']) )
        $columns[] = 'traditional = '.($_GET['h_t'] == 'true' ? 0 : 1);
    if( isset($_GET['h_m']) )
        $columns[] = 'multicache = '.($_GET['h_m'] == 'true' ? 0 : 1);
    if( isset($_GET['h_v']) )
        $columns[] = '`virtual` = '.($_GET['h_v'] == 'true' ? 0 : 1);
    if( isset($_GET['h_w']) )
        $columns[] = 'webcam = '.($_GET['h_w'] == 'true' ? 0 : 1);
    if( isset($_GET['h_e']) )
        $columns[] = 'event = '.($_GET['h_e'] == 'true' ? 0 : 1);
    if( isset($_GET['h_q']) )
        $columns[] = 'quiz = '.($_GET['h_q'] == 'true' ? 0 : 1);
    if( isset($_GET['h_o']) )
        $columns[] = 'mobile = '.($_GET['h_o'] == 'true' ? 0 : 1);
    if( isset($_GET['h_owncache']) )
        $columns[] = 'owncache = '.($_GET['h_owncache'] == 'true' ? 0 : 1);
    if( isset($_GET['h_ignored']) )
        $columns[] = 'ignored = '.($_GET['h_ignored'] == 'true' ? 0 : 1);
    if( isset($_GET['h_own']) )
        $columns[] = 'own = '.($_GET['h_own'] == 'true' ? 0 : 1);
    if( isset($_GET['h_found']) )
        $columns[] = 'found = '.($_GET['h_found'] == 'true' ? 0 : 1);
    if( isset($_GET['h_noattempt']) )
        $columns[] = 'notyetfound = '.($_GET['h_noattempt'] == 'true' ? 0 : 1);
    if( isset($_GET['h_nogeokret']) )
        $columns[] = 'geokret = '.($_GET['h_nogeokret'] == 'true' ? 0 : 1);
    if( isset($_GET['h_avail']) )
        $columns[] = 'active = '.($_GET['h_avail'] == 'true' ? 1 : 0);
    if( isset($_GET['h_temp_unavail']) )
        $columns[] = 'notactive = '.($_GET['h_temp_unavail'] == 'true' ? 1 : 0);
    if( isset($_GET['h_arch']) )
        $columns[] = 'archived = '.($_GET['h_arch'] == 'true' ? 0 : 1);
    if( isset($_GET['be_ftf']) )
        $columns[] = 'be_ftf = '.($_GET['be_ftf'] == 'true' ? 1 : 0);

    global $powerTrailModuleSwitchOn;
    if( isset($_GET['powertrail_only']) && $powerTrailModuleSwitchOn===true ){
        //powertrail_only param update only if powertrails are enabled
        $columns[] = 'powertrail_only = '.($_GET['powertrail_only'] == 'true' ? 1 : 0);
    }

    if( isset($_GET['min_score']) && is_numeric($_GET['min_score']))
        $columns[] = 'min_score = '.intval($_GET['min_score']);
    if( isset($_GET['max_score']) && is_numeric($_GET['max_score']))
        $columns[] = 'max_score = '.intval($_GET['max_score']);

    if( isset($_GET['h_noscore']) )
        $columns[] = 'noscore = '.($_GET['h_noscore'] == 'true' ? 1 : 0);

    $q = 'REPLACE map_settings SET '. implode(',',$columns);
    XDb::xQuery($q);
}
