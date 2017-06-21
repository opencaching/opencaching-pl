<?php

require_once('./lib/common.inc.php');

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$usrid = -1;
if (isset($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
} else {
    $userid = $usr['userid'];
}

$badge_id = $_REQUEST['badge_id'];
$sel_pos = $_REQUEST['pos'];

$show = "";
if ( isset($_REQUEST['showNotGained']))
    $show .= "N";

if ( isset($_REQUEST['showGained']))
    $show .="Y";
        
    
if ($sel_pos == "l" ) //list
    tpl_redirect("badge_positions_list.php?user_id=$userid&badge_id=$badge_id");

if ($sel_pos == "m" ) //map
    tpl_redirect("badge_map.php?user_id=$userid&badge_id=$badge_id&show=$show");
