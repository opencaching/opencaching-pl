<table class="content" width="97%">
    <tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}}" align="middle" /><font size="4">  <b>{{statistics}}</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
</table>

<script type="text/javascript">
    TimeTrack("START");
</script>

<?php
global $debug_page;
if ($debug_page)
    echo "<script type='text/javascript'>TimeTrack( 'DEBUG' );</script>";
?>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
    <tr><td>
            <?php
            global $lang, $rootpath;
            if (!isset($rootpath))
                $rootpath = './';
            //include template handling
            require_once($rootpath . 'lib/common.inc.php');
            if (isset($_REQUEST['region']))
                $region = $_REQUEST['region'];
            echo '<table width="97%"><tr><td align="center"><center><b> ' . tr('ranking_by_number_of_created_caches') . ' </b><br />tylko aktywne skrzynki<br />';
            $woj = sqlValue("SELECT nuts_codes.name FROM nuts_codes WHERE code='$region'", 0);
            echo '<br /><b><font color="blue">' . $woj . '</font></b></center></td></tr></table>';
            echo '<table border="1" bgcolor="white" width="97%">' . "\n";
            $wyniki = sql("SELECT count(*) count, user.username nick,caches.user_id userid FROM caches JOIN user USING(user_id) JOIN cache_location USING (cache_id) WHERE caches.status=1 AND caches.type<>6 AND cache_location.code3='$region' GROUP BY caches.user_id ORDER by count DESC, user.username ASC");
            $licznik = 0;
            $wartosc = 0;
            echo '<tr class="bgcolor2"><td align="right">&nbsp;&nbsp;<b>' . tr('ranking') . '</b>&nbsp;&nbsp;</td><td align="center">&nbsp;&nbsp;<b>' . tr('number_of_caches') . '</b>&nbsp;&nbsp;</td><td align="center">&nbsp;&nbsp;<b>' . tr('username') . '</b>&nbsp;&nbsp;</td></tr>';
            echo '<tr><td height="2"></td></tr>';
            while ($wynik = sql_fetch_assoc($wyniki)) {
                if ($wartosc == 0) {
                    $wartosc = $wynik['count'];
                    $licznik = 1;
                    echo "<tr class='bgcolor2'><td align='right'>&nbsp;&nbsp;<b>$licznik</b>&nbsp;&nbsp;</td><td align='right'>&nbsp;&nbsp;<b>$wynik[count]</b>&nbsp;&nbsp;</td><td><a href='viewprofile.php?userid=$wynik[userid]'>" . htmlspecialchars($wynik[nick]) . "</a>";
                } else
                if ($wartosc == $wynik[count]) {
                    echo ', <a href="viewprofile.php?userid=' . $wynik[userid] . '">' . htmlspecialchars($wynik[nick]) . '</a>';
                } else {
                    $licznik++;
                    $wartosc = $wynik[count];
                    echo '</td></tr><tr class="bgcolor2"><td align="right">&nbsp;&nbsp;<b>' . $licznik . '</b>&nbsp;&nbsp;</td><td align="right">&nbsp;&nbsp;<b>' . $wynik[count] . '</b>&nbsp;&nbsp</td><td><a href="viewprofile.php?userid=' . $wynik[userid] . '">' . htmlspecialchars($wynik[nick]) . '</a>';
                };
            };
            echo '</td></tr>';
            echo '</td></tr></table>' . "\n";
            ?>
        </td></tr>
</table>

<script type="text/javascript">
    TimeTrack("END", "S9");
</script>
