<?php

use src\Utils\Database\XDb;
use src\Utils\Database\OcDb;
use src\Utils\Generators\Uuid;
use src\Models\ApplicationContainer;
use src\Models\User\User;

require(__DIR__.'/lib/common.inc.php');

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();
if (!$loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    die();
}

// former query.inc.php
$error_queryname_exists = '<tr><td colspan="2" class="errormsg">Podana nazwa istnieje już</td></tr>';
$error_empty_name = '<tr><td colspan="2" class="errormsg">Musisz podać nazwe pod jaką ma być zapisane opcje szukania</td></tr>';
$viewquery_line = '<tr>
            <td  width="30%" style="text-align: left; vertical-align: middle; background-color: {bgcolor}"><a href="search.php?queryid={queryid}">{queryname}</a></td>
            <td width="60%" style="text-align: left; vertical-align: middle; background-color: {bgcolor}">
            <a href="search.php?queryid={queryid}&output=gpx&count=max&zip=1" title="GPS Exchange Format .gpx">GPX</a>
            <a href="search.php?queryid={queryid}&output=gpxgc&count=max&zip=1" title="GPS Exchange Format (Groundspeak) .gpx">GPX GC</a>
            <a href="search.php?queryid={queryid}&output=loc&count=max&zip=1" title="Waypoint .loc">LOC</a>
            <a href="search.php?queryid={queryid}&output=kml&count=max&zip=1" title="Google Earth .kml">KML</a>
            <a href="search.php?queryid={queryid}&output=ov2&count=max&zip=1" title="TomTom POI .ov2">OV2</a>
            <a href="search.php?queryid={queryid}&output=ovl&count=max&zip=1" title="TOP50-Overlay .ovl">OVL</a>
            <a href="search.php?queryid={queryid}&output=txt&count=max&zip=1" title="Tekst .txt">TXT</a>
            <a href="search.php?queryid={queryid}&output=wpt&count=max&zip=1" title="Oziexplorer .wpt">WPT</a>
            <a href="search.php?queryid={queryid}&output=uam&count=max&zip=1" title="AutoMapa .uam">UAM</a>
            <a href="search.php?queryid={queryid}&output=xml&count=max&zip=1" title="xml">XML</a>
            <a href="search.php?queryid={queryid}&output=zip&count=max&zip=1" title="Garmin ZIP file (GPX + zdjęcia)  .zip">GARMIN</a>
            </td>
            <td width="10%" bgcolor="{bgcolor}" style="text-align: center"; vertical-align: middle; background-color: {bgcolor}"><a href="query.php?queryid={queryid}&action=delete" onclick="return confirm(\'' . tr("myviewqueries_1") . '\');" ><img style="vertical-align: middle;" src="images/log/16x16-trash.png" alt="" title=' . tr('delete') . ' /></a></td>
            </tr>';
$noqueries = '<tr><td colspan="2">' . tr('no_queries') . '</td></tr>';

$saveastext = tr('select_queries');
$nosaveastext = tr('no_queries');

$bgcolor1 = '#eeeeee';
$bgcolor2 = '#ffffff';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

    if ($action == 'save') {
        $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
        $queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
        $submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;

        savequery($queryid, $queryname, false, $submit, 0, $loggedUser);
    } else if ($action == 'saveas') {
        $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
        $queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
        $submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;
        $oldqueryid = isset($_REQUEST['oldqueryid']) ? $_REQUEST['oldqueryid'] : 0;

        savequery($queryid, $queryname, true, $submit, $oldqueryid, $loggedUser);
    } else if ($action == 'delete') {
        $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
        deletequery($queryid, $loggedUser);
    } else { // default: view
        viewqueries($loggedUser);
    }


function deletequery($queryid, User $loggedUser)
{
    global $tplname;

    $dbc = OcDb::instance();

    $query = "SELECT `id` FROM `queries` WHERE `id`=:1 AND `user_id`=:2";
    $s = $dbc->multiVariableQuery($query, $queryid, $loggedUser->getUserId());

    if ($dbc->rowCount($s) == 1) {

        $query = "DELETE FROM `queries` WHERE `id`=:1 LIMIT 1";
        $dbc->multiVariableQuery($query, $queryid);
    }

    unset($dbc);

    tpl_redirect('query.php?action=view');
    exit;
}

function viewqueries(User $loggedUser)
{
    global $tplname;
    global $viewquery_line, $noqueries, $bgcolor1, $bgcolor2;

    $tplname = 'viewqueries';

    $dbc = OcDb::instance();

    $i = 0;
    $content = '';
    $query = "SELECT id, name FROM `queries` WHERE `user_id`=:1 ORDER BY `name` ASC";
    $s = $dbc->multiVariableQuery($query, $loggedUser->getUserId());

    if ($dbc->rowCount($s) != 0) {
        while ($r = $dbc->dbResultFetch($s)) {
            $thisline = $viewquery_line;

            $thisline = mb_ereg_replace('{queryname}', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{queryid}', htmlspecialchars($r['id'], ENT_COMPAT, 'UTF-8'), $thisline);

            if (($i % 2) == 1)
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor2, $thisline);
            else
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor1, $thisline);

            $content .= $thisline;
            $i++;
        }
    }
    else {
        $content = $noqueries;
    }

    unset($dbc);
    tpl_set_var('queries', $content);
    tpl_BuildTemplate();
    exit;
}

function savequery($queryid, $queryname, $saveas, $submit, $saveas_queryid, User $loggedUser)
{
    global $tplname;
    global $error_empty_name, $nosaveastext, $saveastext, $error_queryname_exists;

    $displayform = ($submit == false);
    $error_no_name = false;
    $error_duplicate_name = false;


    // ok ... verify that it our query and then save
    $rs = XDb::xSql(
        "SELECT `user_id` FROM `queries` WHERE `id`= ? AND (`user_id`=0 OR `user_id`= ? )",
        $queryid, $loggedUser->getUserId());

    if (false == XDb::xFetchArray($rs)) {
        echo 'fatal error: query not found or permission denied';
        exit;
    }
    XDb::xFreeResults($rs);

    if ($saveas == false) {
        if (($displayform == false) && ($queryname == '')) {
            $displayform = true;
            $error_no_name = true;
        } else {
            // test ob the name already exists
            $r['c'] = XDb::xMultiVariableQueryValue(
                "SELECT COUNT(*) `c` FROM `queries`
                WHERE `user_id`= :1 AND `name`= :2 ",
                0, $loggedUser->getUserId(), $queryname);

            if ($r['c'] > 0) {
                $displayform = true;
                $error_duplicate_name = true;
            }
        }
    } else {
        if ($saveas_queryid == 0) {
            $displayform = true;
        } else {
            // test if saveas_queryid exists and is ours
            $rs = XDb::xSql(
                "SELECT `user_id` FROM `queries` WHERE `id`= ? AND (`user_id`=0 OR `user_id`= ? )",
                $saveas_queryid, $loggedUser->getUserId());
            if (false === XDb::xFetchArray($rs)) {
                echo 'fatal error: saveas_query not found or permission denied';
                exit;
            }
            XDb::xFreeResults($rs);
        }
    }

    if ($displayform == true) {
        // form to enter the name
        $tplname = 'savequery';

        if ($error_no_name == true)
            tpl_set_var('nameerror', $error_empty_name);
        else if ($error_duplicate_name == true)
            tpl_set_var('nameerror', $error_queryname_exists);
        else
            tpl_set_var('nameerror', '');

        tpl_set_var('queryname', htmlspecialchars($queryname, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('queryid', htmlspecialchars($queryid, ENT_COMPAT, 'UTF-8'));

        // read oldqueries
        $options = '';
        $rs = XDb::xSql(
            "SELECT `id`, `name` FROM `queries` WHERE `user_id`= ? ORDER BY `name` ASC",
            $loggedUser->getUserId());

        if (!$r = XDb::xFetchArray($rs)) {
            tpl_set_var('selecttext', $nosaveastext);
            tpl_set_var('oldqueries', '');
        } else {
            tpl_set_var('selecttext', $saveastext);
            do{
                if ($r['id'] == $queryid)
                    $options .= '<option value="' . $r['id'] . '" selected="selected">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
                else
                    $options .= '<option value="' . $r['id'] . '">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
            }while( $r = XDb::xFetchArray($rs) );
            XDb::xFreeResults($rs);
            tpl_set_var('oldqueries', $options);
        }

        tpl_BuildTemplate();
        exit;
    }

    $r['options'] = XDb::xMultiVariableQueryValue(
        "SELECT `options` FROM `queries` WHERE `id`= :1 LIMIT 1", 0, $queryid);

    // ok, save
    if ($saveas == true) {
        XDb::xSql(
            "UPDATE `queries` SET `options`= ?, `last_queried`=NOW() WHERE `id`= ? ",
            $r['options'], $saveas_queryid);
    } else {
        XDb::xSql(
            "INSERT INTO `queries` (`user_id`, `last_queried`, `name`, `uuid`, `options`)
            VALUES ( ?, NOW(), ?, ?, ?)",
            $loggedUser->getUserId(), $queryname, Uuid::create(), $r['options']);
    }

    tpl_redirect('query.php?action=view');
}
