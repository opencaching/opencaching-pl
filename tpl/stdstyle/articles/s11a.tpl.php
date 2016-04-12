<?php
use Utils\Database\XDb;
?>
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
    <tr>
        <td><?php
            global $lang, $rootpath;

            if (!isset($rootpath))
                $rootpath = './';

            //include template handling
            require_once($rootpath . 'lib/common.inc.php');

            setlocale(LC_TIME, 'pl_PL.UTF-8');

            echo '<center><table width="97%" border="0"><tr><td align="center"><center><b>{{Stats_s3a_01}}<br/><b>';
            echo '<br /><br />({{Stats_s3a_02}})</center></td></tr></table><br><table border="1" bgcolor="white" width="30%">' . "\n";


            echo '
<tr class="bgcolor2">
    <td align="right">
        <b>{{Stats_s3a_03}}:</b>&nbsp;&nbsp;
    </td>
</tr><tr><td height="2"></td></tr>';

            $rs = XDb::xSql(
                "SELECT `code`, `name` FROM `nuts_codes`
                WHERE (" . $config['provinceNutsCondition'] . ") ORDER BY `name` ASC");

            while( $record = XDb::xFetchArray($rs) ){

                echo '<tr class="bgcolor2">
            <td align="right">
                <b><a class=links href=articles.php?page=s11&region=' . $record[code] . '>' . $record[name] . '</a></b>&nbsp;&nbsp;
            </td>';
            }

            echo '</table>' . "\n";

            XDb::xFreeResults($rs);
            ?>
        </td></tr>
</table>

<script type="text/javascript">
    TimeTrack("END", "S11a");
</script>
