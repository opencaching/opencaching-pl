<?php

function setDBFilter($userid, $maptype=null, $cachelimit=null, $h_u=null, $h_t=null, $h_m=null, $h_v=null, $h_w=null, $h_e=null, $h_q=null, $h_o=null, $h_owncache=null, $h_ignored=null, $h_own=null, $h_found=null, $h_noattempt=null, $h_nogeokret=null, $signes=null, $waypoints=null, $h_avail=null, $h_temp_unavail=null, $h_arch=null, $be_ftf=null,$h_se=null, $h_no=null, $h_de=null, $h_pl=null, $h_nl=null, $min_score=null, $max_score=null, $h_noscore=null)
{
    $maptype = intval($maptype);
    $userid = intval($userid);
    if( $cachelimit == null && $cachesort == null )
    {
        // map v.3 request
        // The following parameters are currently ignored (DB column names in brackets):
        // signes (showsign), waypoints (showwp), pl (pl), de (de), no (no), se (se), nl (nl)
        $sql = "REPLACE map_settings SET
                maptype = $maptype,
                unknown = ".$h_u.",
                traditional = ".$h_t.",
                multicache = ".$h_m.",
                virtual = ".$h_v.",
                webcam = ".$h_w.",
                event = ".$h_e.",
                quiz = ".$h_q.",
                mobile = ".$h_o.",
                owncache = ".$h_owncache.",
                ignored = ".$h_ignored.",
                own = ".$h_own.",
                found = ".$h_found.",
                notyetfound = ".$h_noattempt.",
                geokret = ".$h_nogeokret.",
                active = ".$h_avail.",
                notactive = ".$h_temp_unavail.",
                archived = ".$h_arch.",
                be_ftf = ".$be_ftf.",
                min_score = ".$min_score.",
                max_score = ".$max_score.",
                noscore = ".$h_noscore.",
                user_id = $userid";
    }
    else
    {
        // map v.2 request
        $cachelimit = intval($cachelimit);
        $cachesort = intval($cachesort);

        $sql = "INSERT INTO map_settings_v2
                (user_id, maptype, cachelimit, cachesort)
            VALUES
                ($userid, $maptype, $cachelimit, $cachesort)
            ON DUPLICATE KEY UPDATE
                maptype = $maptype,
                cachelimit = $cachelimit,
                cachesort = $cachesort";
    }
    mysql_query($sql);
}

require_once('./lib/common.inc.php');

global $usr;

if($usr==true)
{
    session_start();

    if ( (isset($_GET['maptype']) && ($_GET['maptype'] != '' )) && (isset($_GET['cachelimit']) && ($_GET['cachelimit'] != '' )) && (isset($_GET['cachesort']) && ($_GET['cachesort'] != '' )) ) {
        setDBFilter($usr['userid'], $_GET['maptype'], $_GET['cachelimit'], $_GET['cachesort']);
    }
    else
        if( isset($_GET['maptype']) && $_GET['maptype'] != '')
            setDBFilter($usr['userid'],$_GET['maptype'],null, $_GET['h_u']=="true"?0:1, $_GET['h_t']=="true"?0:1, $_GET['h_m']=="true"?0:1, $_GET['h_v']=="true"?0:1, $_GET['h_w']=="true"?0:1, $_GET['h_e']=="true"?0:1, $_GET['h_q']=="true"?0:1, $_GET['h_o']=="true"?0:1, $_GET['h_owncache']=="true"?0:1, $_GET['h_ignored']=="true"?0:1, $_GET['h_own']=="true"?0:1, $_GET['h_found']=="true"?0:1, $_GET['h_noattempt']=="true"?0:1, $_GET['h_nogeokret']=="true"?0:1, $_GET['signes']=="true"?1:0, $_GET['waypoints']=="true"?1:0, $_GET['h_avail']=="true"?1:0, $_GET['h_temp_unavail']=="true"?1:0, $_GET['h_arch']=="true"?0:1, $_GET['be_ftf']=="true"?1:0, $_GET['h_se']=="true"?1:0, $_GET['h_no']=="true"?1:0, $_GET['h_de']=="true"?1:0, $_GET['h_pl']=="true"?1:0, $_GET['h_nl']=="true"?1:0, intval($_GET['min_score']),intval($_GET['max_score']), $_GET['h_noscore']=="true"?1:0 );



}

?>
