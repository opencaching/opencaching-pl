<?php
use Utils\Database\XDb;

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id'])) {

    if (isSet($_GET['id']) && !empty($_GET['id']) && preg_match("/^\d+$/", $_GET['id'])) {



        $id = XDb::xEscape($_GET['id']);

        $query = "select user_id,cache_id from cache_watches where id = '" . $id . "'";
        $wynik = XDb::xSql($query);
        $wiersz = XDb::xFetchArray($wynik);

        $user_id2 = $wiersz['user_id'];

        if (!empty($user_id2) && ($user_id2 == $_SESSION['user_id'])) {
            $query = "select wp_oc from caches where cache_id = '" . $wiersz['cache_id'] . "'";
            $wynik = XDb::xSql($query);
            $wiersz2 = XDb::xFetchArray($wynik);
            $cache_id2 = $wiersz2['wp_oc'];

            $query = "delete from cache_watches where id = '" . $id . "'";
            $wynik = XDb::xSql($query);

            header('Location: ./viewcache.php?wp=' . $cache_id2);
            exit;
        }
    }
}

header('Location: ./index.php');
?>