<?php

/* * *************************************************************************
  ./savequery.php
  -------------------
  begin                : November 4 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  save the query for the specific user

 * ************************************************************************** */

require('./lib/common.inc.php');
require($stylepath . '/query.inc.php');

if ($error == false) {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
        die();
    }

    if ($action == 'save') {
        $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
        $queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
        $submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;

        savequery($queryid, $queryname, false, $submit, 0);
    } else if ($action == 'saveas') {
        $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
        $queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
        $submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;
        $oldqueryid = isset($_REQUEST['oldqueryid']) ? $_REQUEST['oldqueryid'] : 0;

        savequery($queryid, $queryname, true, $submit, $oldqueryid);
    } else if ($action == 'delete') {
        $queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
        deletequery($queryid);
    } else { // default: view
        viewqueries();
    }
}

function deletequery($queryid)
{
    global $tplname, $usr;

    $dbc = new dataBase();

    //$rs = sql("SELECT `id` FROM `queries` WHERE `id`='&1' AND `user_id`='&2'", $queryid, $usr['userid']);
    $query = "SELECT `id` FROM `queries` WHERE `id`=:1 AND `user_id`=:2";
    $dbc->multiVariableQuery($query, $queryid, $usr['userid']);

    if ($dbc->rowCount() == 1) {
        //mysql_free_result($rs);
        //sql("DELETE FROM `queries` WHERE `id`='&1' LIMIT 1", $queryid);

        $query = "DELETE FROM `queries` WHERE `id`=:1 LIMIT 1";
        $dbc->multiVariableQuery($query, $queryid);
    }

    unset($dbc);

    tpl_redirect('query.php?action=view');
    exit;
}

function viewqueries()
{
    global $tplname, $usr;
    global $viewquery_line, $noqueries, $bgcolor1, $bgcolor2;

    $tplname = 'viewqueries';

    $dbc = new dataBase();

    $i = 0;
    $content = '';
    //$rs = sql("SELECT `id`, `name` FROM `queries` WHERE `user_id`='&1' ORDER BY `name` ASC", $usr['userid']);
    $query = "SELECT id, name FROM `queries` WHERE `user_id`=:1 ORDER BY `name` ASC";
    $dbc->multiVariableQuery($query, $usr['userid']);

    if ($dbc->rowCount() != 0) {
        //while ($r = sql_fetch_array($rs))
        while ($r = $dbc->dbResultFetch()) {
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
        //mysql_free_result($rs);
    }
    else {
        $content = $noqueries;
    }

    unset($dbc);
    tpl_set_var('queries', $content);
    tpl_BuildTemplate();
    exit;
}

function savequery($queryid, $queryname, $saveas, $submit, $saveas_queryid)
{
    global $usr, $tplname;
    global $error_empty_name, $nosaveastext, $saveastext, $error_queryname_exists;

    $displayform = ($submit == false);
    $error_no_name = false;
    $error_duplicate_name = false;


    // ok ... checken, ob die query uns gehört und dann speichern
    $rs = sql("SELECT `user_id` FROM `queries` WHERE `id`='&1' AND (`user_id`=0 OR `user_id`='&2')", $queryid, $usr['userid']);
    if (mysql_num_rows($rs) == 0) {
        echo 'fatal error: query not found or permission denied';
        exit;
    }
    mysql_free_result($rs);


    if ($saveas == false) {
        if (($displayform == false) && ($queryname == '')) {
            $displayform = true;
            $error_no_name = true;
        } else {
            // prüfen ob name bereits vorhanden
            $rs = sql("SELECT COUNT(*) `c` FROM `queries` WHERE `user_id`='&1' AND `name`='&2'", $usr['userid'], $queryname);
            $r = sql_fetch_array($rs);
            mysql_free_result($rs);

            if ($r['c'] > 0) {
                $displayform = true;
                $error_duplicate_name = true;
            }
        }
    } else {
        if ($saveas_queryid == 0) {
            $displayform = true;
        } else {
            // prüfen ob saveas_queryid existiert und uns gehört
            $rs = sql("SELECT `user_id` FROM `queries` WHERE `id`='&1' AND (`user_id`=0 OR `user_id`='&2')", $saveas_queryid, $usr['userid']);
            if (mysql_num_rows($rs) == 0) {
                echo 'fatal error: saveas_query not found or permission denied';
                exit;
            }
            mysql_free_result($rs);
        }
    }

    if ($displayform == true) {
        // abfrageform für name
        $tplname = 'savequery';

        if ($error_no_name == true)
            tpl_set_var('nameerror', $error_empty_name);
        else if ($error_duplicate_name == true)
            tpl_set_var('nameerror', $error_queryname_exists);
        else
            tpl_set_var('nameerror', '');

        tpl_set_var('queryname', htmlspecialchars($queryname, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('queryid', htmlspecialchars($queryid, ENT_COMPAT, 'UTF-8'));

        // oldqueries auslesen
        $options = '';
        $rs = sql("SELECT `id`, `name` FROM `queries` WHERE `user_id`='&1' ORDER BY `name` ASC", $usr['userid']);
        if (mysql_num_rows($rs) == 0) {
            tpl_set_var('selecttext', $nosaveastext);
            tpl_set_var('oldqueries', '');
        } else {
            tpl_set_var('selecttext', $saveastext);
            while ($r = sql_fetch_array($rs)) {
                if ($r['id'] == $queryid)
                    $options .= '<option value="' . $r['id'] . '" selected="selected">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
                else
                    $options .= '<option value="' . $r['id'] . '">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
            }
            mysql_free_result($rs);
            tpl_set_var('oldqueries', $options);
        }

        tpl_BuildTemplate();
        exit;
    }

    $rs = sql("SELECT `options` FROM `queries` WHERE `id`='&1'", $queryid);
    $r = sql_fetch_array($rs);
    mysql_free_result($rs);

    // ok, speichern
    if ($saveas == true) {
        sql("UPDATE `queries` SET `options`='&1', `last_queried`=NOW() WHERE `id`='&2'", $r['options'], $saveas_queryid);
    } else {
        sql("INSERT INTO `queries` (`user_id`, `last_queried`, `name`, `uuid`, `options`) VALUES ( '&1', NOW(), '&2', '&3', '&4')", $usr['userid'], $queryname, create_uuid(), $r['options']);
    }

    tpl_redirect('query.php?action=view');
}

?>
