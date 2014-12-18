<?php

/* * *************************************************************************
  ./removelist.php
  -------------------
  begin                : July 25 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  remove a watch from the watchlist

 * ************************************************************************** */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}

//Preprocessing
if ($error == false) {
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'mylist.php';
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : '';

    if ($usr['userid']) {
        if ($cache_id == 'all') {
            unset($_SESSION['print_list']);
            $_SESSION['print_list'] = array();
        } else {
            while (onTheList($_SESSION['print_list'], $cache_id) != -1)
                unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $cache_id)]);
            $_SESSION['print_list'] = array_values($_SESSION['print_list']);
        }
    }
    tpl_redirect($target);
}

tpl_BuildTemplate();
?>
