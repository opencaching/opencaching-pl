<?php

require_once('./lib/common.inc.php');

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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache" align="middle"/>{{log01}}</div>
<p>
    {{log02}}.
</p>
<br />

<form method="POST">{{log03}} <input type="text" name="filter_from" value="{filter_from}" /> {{log04}}: <input type="text" name="filter_to" value="{filter_to}"/><input type="submit" value="{{log16}}" /></form>
<div>
    <div style="position: relative; top: 4px; float: left; font-family: Arial, Tahoma, Verdana; font-size: 8pt;">{{log05}}: </div>
    <div style="float: left;"><form method="POST" name="ShiftTimePlusOne"><input name="SubmitShiftTimePlusOne" type="submit" value="+1 {{log15}}."></form></div>
    <div style="float: left;"><form method="POST" name="ShiftTimeMinusOne"><input name="SubmitShiftTimeMinusOne" type="submit" value="-1 {{log15}}."></form></div>
    <div style="position:relative; left:60px; float: left;"><form method="LINK" action="log_cache_multi_panelframes.htm" name="otworzPanel"><input type="submit" value="{{log17}}"></form></div>
</div>

<table width="770" class="table" style="line-height: 1.6em; font-size: 10px; border: 1px solid black; empty-cells: show;">
    <tr>
        <td style="text-align: center">

            <table border="0" style="line-height: 1.6em; font-size: 10px; table-layout: fixed; border: 1px dotted black;">
                <tr class="bgcolor2">
                    <th width=560>{{log06}}</th>
                    <th width=70>{{log07}}</th>
                    <th width=70>{{log08}}<br/>{{log09}}</th>
                </tr><tr class="bgcolor2">
                    <th colspan=2>{{log10}}</th>
                    <th>{{log11}}</th>
                </tr>
            </table>
            <?php
            global $dane;
            global $lang;
            global $dateFormat, $datetimeFormat;
            foreach ($dane as $k => $v) {
                ?>
                <form method="POST" name="logCacheForm" action="log.php?cacheid=<?php echo @$v['cache_id']; ?>" target="_blank">
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
                            <td width=560><?php echo isset($v['cache_name']) ? "<A href=\"viewcache.php?cacheid=" . $v['cache_id'] . "\" target=\"_blank\">" . "<img src=\"tpl/stdstyle/images/" . get_icon_for_cache_type($v['cache_type']) . "\" /> " . $v['kod_str'] . " " . $v['cache_name'] . "</a>" : " "; ?></td>
                            <td width=70 style="text-align: right"><?php
                                $v['data'] = date($datetimeFormat, strtotime($v['data']));


                                echo isset($v['data']) ? str_replace(" ", "<br />", $v['data']) : " ";
                                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                echo isset($v['status']) ? "<img src=\"tpl/stdstyle/images/" . get_icon_for_status($v['status']) . "\" />" : " ";
                                ?></td>
                            <td width=70 style="text-align: right"><?php
                                if (isset($v['got_last_activity'])) {
                                    $v['last_date'] = date($datetimeFormat, strtotime($v['last_date']));
                                    echo str_replace(" ", "<br />", $v['last_date']) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                    if (isset($v['last_status'])) {
                                        echo "<img src=\"tpl/stdstyle/images/" . get_icon_for_status($v['last_status']) . "\" />";
                                    } else {
                                        echo " ";
                                    }
                                } else {
                                    echo " ";
                                }
                                ?></td>
                        </tr><tr class="<?php echo $styl; ?>">
                            <td width="630" colspan=2><?php echo isset($v['koment']) ? $v['koment'] : " "; ?>&nbsp;</td>
                            <td style="text-align: center"><?php
                                if (isset($v['cache_id']) && (!$zgodne)) {
                                    echo "
    <input type=\"submit\" value=\"Log\" style=\"width: 40px\"/>
<!--    <input onclick=\"this.form.submit(); setTimeout(function() { window.open('viewcache.php?cacheid=" . $v['cache_id'] . "'); }, 250); return false;\" type=\"submit\" value=\"T 1\" style=\"width: 20px\"/> -->
    <input onclick=\"this.form.submit(); window.open('viewcache.php?cacheid=" . $v['cache_id'] . "'); return false;\" type=\"submit\" value=\"2\" style=\"width: 20px\"/>
<!--    <input onclick=\"window.open('viewcache.php?cacheid=" . $v['cache_id'] . "'); this.form.submit(); return false;\" type=\"submit\" value=\"T 3\" style=\"width: 20px\"/> -->
    ";
                                };
                                ?>
                                <!--    <a href="http://www.example.org" onclick="window.open('http://www.google.com','newwin');">click me</a> -->
                            </td>
                        </tr>
                    </table>
                </form>
                <?php
            }
            ?>

        </td></tr>
</table>
<br /><hr /><br />

<!-- Mass komentarze... -->
<p>
    {{log12}}.
</p>
<form name="massCommentsForm" method="POST">
    <textarea name="logtext" id="logtext" cols="100" rows="7"></textarea>
    <input type="submit" name="submitCommentsForm" id="submitCommentsForm" value="{{log13}}" style="width:120px"/>
</form>
<br />
<hr />
<br />
<div style="text-align: center">
    <form name="GpsDownload" method="POST">
        <input type="submit" name="submitDownloadGpx" id="submitDownloadGpxId" value="{{log14}}" style="width:120px"/>
    </form>
</div>
<br />
<br />
