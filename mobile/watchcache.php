<?php

require_once("./lib/common.inc.php");

if (isset($_SESSION['user_id'])) {

    if (isSet($_GET['wp']) && !empty($_GET['wp'])) {

        db_connect();

        $wp = mysql_real_escape_string($_GET['wp']);

        $query = "select cache_id from caches where wp_oc = '" . $wp . "'";
        $wynik = db_query($query);
        $wiersz = mysql_fetch_row($wynik);
        $wiersz = $wiersz[0];

        if (!empty($wiersz)) {
            $query = "insert into cache_watches (cache_id,user_id) values ('" . $wiersz . "','" . $_SESSION['user_id'] . "')";
            $wynik = db_query($query);

            header('Location: ./viewcache.php?wp=' . $wp);
            exit;
        }
    }
}

header('Location: ./index.php');
?>