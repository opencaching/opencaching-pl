
<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
    <tr>
        <td>
        <?php

use Utils\Database\XDb;
use Utils\Cache\OcMemCache;
use lib\Objects\OcConfig\OcConfig;
use Utils\I18n\I18n;

# This page took >60 seconds to render! Added daily caching.

$result = OcMemCache::getOrCreate(
    "articles_s7-" . I18n::getCurrentLang(),
    86400,
function ()
{
    ob_start();

    $rsUs["count"] = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) `count` FROM
            (
                SELECT COUNT(caches.user_id) FROM `caches`
                WHERE `status`=1 GROUP BY `user_id`
            ) `users_with_founds`', 0);

    $fCt["count"] = XDb::xSimpleQueryValue(
        'SELECT COUNT(*) `count` FROM `caches` WHERE `status`=1', 0);

    $countriesQuoted = array_map( '\Utils\Database\XDb::xQuote', OcConfig::getSitePrimaryCountriesList() );
    $rsfCR = XDb::xSql(
        "SELECT COUNT(*) `count`, `cache_location`.`adm3` region, `cache_location`.`code3` code_region
        FROM `cache_location`
            INNER JOIN caches ON cache_location.cache_id=caches.cache_id
        WHERE `cache_location`.`code1` IN (".implode(",", $countriesQuoted) .")
            AND `caches`.`status`=1 AND `caches`.`type`<>6
            AND `cache_location`.`adm3`!=''
        GROUP BY `cache_location`.`code3`
        ORDER BY count DESC");

    echo '<table width="97%"><tr><td align="center"><center><b> ' . tr('Stats_t6_01') . '</b> <br />' . tr('Stats_t6_02') . '<br /> ' . tr('Stats_t6_03') . ': ';
    echo $rsUs["count"];
    echo ' .::. ' . tr('Stats_t6_04') . ': ';
    echo $fCt["count"];
    echo '<br /><br />(' . tr('Stats_t6_05') . ')</center></td></tr></table><br><table border="1" bgcolor="white" width="97%">' . "\n";
    echo '
    <tr class="bgcolor2">
        <td width="20%" align="right">
            &nbsp;&nbsp;<b>' . tr('Stats_t6_06') . '</b>&nbsp;&nbsp;
        </td>
        <td align="right">
            &nbsp;&nbsp;<b>' . tr('Stats_t6_07') . '</b>&nbsp;&nbsp;
        </td>
    </tr><tr><td height="2"></td></tr>';

    while ($line = XDb::xFetchArray($rsfCR)) {
        echo '<tr class="bgcolor2">
                <td align="right">
                    &nbsp;&nbsp;<b>' . $line["count"] . '</b>&nbsp;&nbsp;
                </td>
                <td align="right">
                    &nbsp;&nbsp;<b><a class=links href=articles.php?page=s9&region=' . $line['code_region'] . '>' . $line['region'] . '</a></b>&nbsp;&nbsp;
                </td>';
    }

    echo '</table>' . "\n";

    XDb::xFreeResults($rsfCR);

    return ob_get_clean();
});

print $result;

        ?>
        </td></tr>
</table>
</div>
