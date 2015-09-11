<?php

require_once('./lib/common.inc.php');
$no_tpl_build = false;
if ($usr == false || (!isset($_FILES['userfile']) && !isset($_SESSION['log_cache_multi_data']))) {
    tpl_redirect('log_cache_multi_send.php');
} else {
    require_once($rootpath . 'lib/caches.inc.php');
    require($stylepath . '/log_cache.inc.php');
    ?>
    <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/style_screen.css" />
            <link rel="stylesheet" type="text/css" media="print" href="tpl/stdstyle/css/style_print.css" />
            <link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/style_autumn.css" />
        </head>
        <body>
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
            <?php
            $dane = array();

            if (isset($_SESSION['log_cache_multi_filteredData'])) {
                $dane = $_SESSION['log_cache_multi_filteredData'];

                $cacheIdList = "";
                foreach ($dane as $k => $v) {
                    if (strlen($cacheIdList) > 0) {
                        $cacheIdList .= ",";
                    }
                    $cacheIdList .= "'" . $v['cache_id'] . "'";
                }

                // dociagam info o ostatniej aktywnosci dla kazdej skrzynki
                if (strlen($cacheIdList) > 0) {
                    $rs = sql("SELECT c.* FROM (SELECT cache_id, MAX(date) date FROM `cache_logs` WHERE user_id='" . sql_escape($usr['userid']) . "' AND cache_id IN (" . $cacheIdList . ") GROUP BY cache_id) as x INNER JOIN `cache_logs` as c ON c.cache_id = x.cache_id AND c.date = x.date");
                    if (mysql_num_rows($rs) != 0) {
                        $i = 0;
                        while ($i < mysql_num_rows($rs)) {
                            $record = sql_fetch_array($rs);
                            foreach ($dane as $k => $v) {
                                if ($v['cache_id'] == $record['cache_id']) {
                                    $v['got_last_activity'] = true;
                                    $v['last_date'] = substr($record['date'], 0, strlen($record['date']) - 3);
                                    $v['last_status'] = $record['type'];
                                    $dane[$k] = $v;
                                }
                            }
                            $i++;
                        }
                    }
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
                                <td width=210><?php echo isset($v['cache_name']) ? "<img src=\"tpl/stdstyle/images/" . get_icon_for_cache_type($v['cache_type']) . "\" /> " . $v['kod_str'] . " " . $v['cache_name'] : " "; ?></td>
                                <td width=70 style="text-align: right"><?php
                                    echo isset($v['data']) ? str_replace(" ", "<br />", $v['data']) : " ";
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                    echo isset($v['status']) ? "<img src=\"tpl/stdstyle/images/" . get_icon_for_status($v['status']) . "\" />" : " ";
                                    ?></td>
                                <td width=70 style="text-align: right"><?php
                                    echo isset($v['got_last_activity']) ? str_replace(" ", "<br />", $v['last_date']) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . (isset($v['last_status']) ? "<img src=\"tpl/stdstyle/images/" . get_icon_for_status($v['last_status']) . "\" />" : " ") : " ";
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
        }

        function get_icon_for_status($status)
        {
            $typyStatusow = get_log_types_from_database();
            foreach ($typyStatusow as $k => $v) {
                if ($v['id'] == $status) {
                    return $v['icon_small'];
                }
            }
        }

        function get_icon_for_cache_type($type)
        {
            $typySkrzynek = get_cache_types_from_database();
            foreach ($typySkrzynek as $k => $v) {
                if ($v['id'] == $type) {
                    return $v['icon_large'];
                }
            }
        }
        ?>
