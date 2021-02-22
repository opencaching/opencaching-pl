<?php

use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Utils\Database\XDb;
use src\Models\ApplicationContainer;

require_once (__DIR__.'/lib/common.inc.php');

$no_tpl_build = false;
$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (!$loggedUser || (!isset($_FILES['userfile']) && !isset($_SESSION['log_cache_multi_data']))) {
    tpl_redirect('log_cache_multi_send.php');
    exit;
}
?>
    <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" media="screen,projection" href="/css/style_screen.css" />
            <link rel="stylesheet" type="text/css" media="print" href="/css/style_print.css" />

            <style>
                a:link {
                    color:inherit;
                    text-decoration: none;
                }
                a:visited {
                    color:inherit;
                    text-decoration: none;
                }
                a:hover {
                    color:inherit;
                    font-weight: bold;
                    text-decoration: underline;
                }
                a:active {
                    color:inherit;
                    text-decoration: none;
                }

                .bgcolorM1 {background-color: rgb(170,187,182);}

            </style>


        </head>
        <body>
         <?php
            $dane = array();

            if (isset($_SESSION['log_cache_multi_filteredData'])) {
                $dane = $_SESSION['log_cache_multi_filteredData'];

                $cacheIdList = array();
                foreach ($dane as $k => $v) {
                    $cacheIdList[] = $v['cache_id'];
                }

                // dociagam info o ostatniej aktywnosci dla kazdej skrzynki
                if ( count($cacheIdList) > 0) {
                    $rs = XDb::xSql(
                        "SELECT c.* FROM
                            (
                                SELECT cache_id, MAX(date) date FROM `cache_logs`
                                WHERE user_id= ? AND cache_id IN (" . XDb::xEscape( implode(',',$cacheIdList) ) . ")
                                GROUP BY cache_id
                            ) as x INNER JOIN `cache_logs` as c ON c.cache_id = x.cache_id
                                AND c.date = x.date", $loggedUser->getUserId()  );

                    while( $record = XDb::xFetchArray($rs) ){
                        foreach ($dane as $k => $v) {
                            if ($v['cache_id'] == $record['cache_id']) {
                                $v['got_last_activity'] = true;
                                $v['last_date'] = substr($record['date'], 0, strlen($record['date']) - 3);
                                $v['last_status'] = $record['type'];
                                $dane[$k] = $v;
                            }
                        }
                    }//while
                }



                foreach ($dane as $k => $v) {
                    ?>
                    <form method="POST" name="logCacheForm" action="log.php?cacheid=<?php echo $v['cache_id']; ?>" target="cacheLog">
                        <textarea style="visibility:hidden;position:absolute;" name="logtext"><?php echo $v['koment']; ?></textarea>
                        <input type="hidden" name="logtype" value="<?php echo $v['status']; ?>" />
                        <input type="hidden" name="logyear" value="<?php echo $v['rok']; ?>" />
                        <input type="hidden" name="logmonth" value="<?php echo $v['msc']; ?>" />
                        <input type="hidden" name="logday" value="<?php echo $v['dzien']; ?>" />
                        <input type="hidden" name="loghour" value="<?php echo $v['godz']; ?>" />
                        <input type="hidden" name="logmin" value="<?php echo $v['min']; ?>" />
                        <table border="0" style="table-layout: fixed; border: 1px dotted black; line-height: 1.6em; font-size: 10px; "><?php
                            // jesli zgodne daty i typ to inny kolor:
                            if ((isset($v['data']) && isset($v['last_date']) && $v['data'] == $v['last_date']) && (isset($v['status']) && isset($v['last_status']) && $v['status'] == $v['last_status'])) {
                                $zgodne = true;
                                $styl = "bgcolorM1";
                            } else {
                                $zgodne = false;
                                $styl = "bgcolor2";
                            }
                            ?>
                            <tr class="<?php echo $styl; ?>">
                                <td width=210><?php echo isset($v['cache_name']) ?
                                "<img src=\"" . GeoCacheCommons::CacheIconByType($v['cache_type'], GeoCacheCommons::STATUS_READY) . "\" /> " .
                                        $v['kod_str'] . " " . $v['cache_name'] : " "; ?></td>
                                <td width=70 style="text-align: right"><?php
                                    echo isset($v['data']) ? str_replace(" ", "<br />", $v['data']) : " ";
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                    echo isset($v['status']) ? "<img src=\"" . GeoCacheLogCommons::GetIconForType($v['status']) . "\" />" : " ";
                                    ?></td>
                                <td width=70 style="text-align: right"><?php
                                echo isset($v['got_last_activity']) ? str_replace(" ", "<br />", $v['last_date']) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . (isset($v['last_status']) ? "<img src=\"" . GeoCacheLogCommons::GetIconForType($v['last_status']) . "\" />" : " ") : " ";
                                    ?></td>
                            </tr><tr class="<?php echo $styl; ?>">
                                <td width="280" colspan=2><?php echo isset($v['koment']) ? $v['koment'] : " "; ?>&nbsp;</td>
                                <td style="text-align: center"><?php
                                    if (isset($v['cache_id']) && (!$zgodne)) {
                                        echo "<input type=\"submit\" value=\"Log\" style=\"width: 50px\" onclick=\"parent.cachePreview.location.href='viewcache.php?cacheid=" . $v['cache_id'] . "'; return true;\"/>";
                                    };
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <?php
                }
            }


