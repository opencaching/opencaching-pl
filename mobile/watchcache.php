<?php
use Utils\Database\XDb;

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id'])) {

    if (isSet($_GET['wp']) && !empty($_GET['wp'])) {



        $wp = XDb::xEscape($_GET['wp']);

        $query = "select cache_id from caches where wp_oc = '" . $wp . "'";
        $wynik = XDb::xSql($query);
        $wiersz = XDb::xFetchArray($wynik);
        $wiersz = $wiersz[0];

        if (!empty($wiersz)) {
            $query = "insert into cache_watches (cache_id,user_id) values ('" . $wiersz . "','" . $_SESSION['user_id'] . "')";
            $wynik = XDb::xSql($query);

            header('Location: ./viewcache.php?wp=' . $wp);
            exit;
        }
    }
}

header('Location: ./index.php');
?>