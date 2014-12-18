<?php

if ($ile <= $na_stronie)
    find_news(0, $na_stronie);
else {

    if (!isset($_GET['page'])) {

        find_news(0, $na_stronie);
        $next_page = '2';
    } elseif (isset($_GET['page']) && !empty($_GET['page']) && preg_match("/^\d+$/", $_GET['page'])) {

        $start = (($_GET['page'] - 1) * $na_stronie);
        $limit = $na_stronie;
        //print $start." ".$limit;
        find_news($start, $limit);
        //print $ile."f";
        if (empty($znalezione)) {
            header('Location: ' . $url . '&page=1');
            exit;
        } else {
            if (((($_GET['page'] - 1) * $na_stronie) + $na_stronie) < $ile)
                $next_page = $_GET['page'] + 1;
            $prev_page = $_GET['page'] - 1;
        }
    }
}
?>