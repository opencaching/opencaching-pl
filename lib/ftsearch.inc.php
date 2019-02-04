<?php

use Utils\Database\XDb;

/* begin conversion rules */

$ftsearch_simplerules[] = array('qu', 'k');
$ftsearch_simplerules[] = array('ts', 'z');
$ftsearch_simplerules[] = array('tz', 'z');
$ftsearch_simplerules[] = array('alp', 'alb');
$ftsearch_simplerules[] = array('y', 'i');
$ftsearch_simplerules[] = array('ai', 'ei');
$ftsearch_simplerules[] = array('ou', 'u');
$ftsearch_simplerules[] = array('th', 't');
$ftsearch_simplerules[] = array('ph', 'f');
$ftsearch_simplerules[] = array('oh', 'o');
$ftsearch_simplerules[] = array('ah', 'a');
$ftsearch_simplerules[] = array('eh', 'e');
$ftsearch_simplerules[] = array('aux', 'o');
$ftsearch_simplerules[] = array('eau', 'o');
$ftsearch_simplerules[] = array('eux', 'oe');
$ftsearch_simplerules[] = array('^ch', 'sch');
$ftsearch_simplerules[] = array('ck', 'k');
$ftsearch_simplerules[] = array('ie', 'i');
$ftsearch_simplerules[] = array('ih', 'i');
$ftsearch_simplerules[] = array('ent', 'end');
$ftsearch_simplerules[] = array('uh', 'u');
$ftsearch_simplerules[] = array('sh', 'sch');
$ftsearch_simplerules[] = array('ver', 'wer');
$ftsearch_simplerules[] = array('dt', 't');
$ftsearch_simplerules[] = array('hard', 'hart');
$ftsearch_simplerules[] = array('egg', 'ek');
$ftsearch_simplerules[] = array('eg', 'ek');
$ftsearch_simplerules[] = array('cr', 'kr');
$ftsearch_simplerules[] = array('ca', 'ka');
$ftsearch_simplerules[] = array('ce', 'ze');
$ftsearch_simplerules[] = array('x', 'ks');
$ftsearch_simplerules[] = array('ve', 'we');
$ftsearch_simplerules[] = array('va', 'wa');

/* end conversion rules */

function ftsearch_hash(&$str)
{
    $astr = ftsearch_split($str, true);

    foreach ($astr AS $k => $s) {
        if (strlen($s) > 2)
            $astr[$k] = sprintf("%u", crc32($s));
        else
            unset($astr[$k]);
    }
    return $astr;
}

// str = long text
function ftsearch_split(&$str, $simple)
{
    global $ftsearch_ignores;

    // punctuations
    $str = mb_ereg_replace('\\?', ' ', $str);
    $str = mb_ereg_replace('\\)', ' ', $str);
    $str = mb_ereg_replace('\\(', ' ', $str);
    $str = mb_ereg_replace('\\.', ' ', $str);
    $str = mb_ereg_replace('´', ' ', $str);
    $str = mb_ereg_replace('`', ' ', $str);
    $str = mb_ereg_replace('\'', ' ', $str);
    $str = mb_ereg_replace('/', ' ', $str);
    $str = mb_ereg_replace(':', ' ', $str);
    $str = mb_ereg_replace(',', ' ', $str);
    $str = mb_ereg_replace("\r\n", ' ', $str);
    $str = mb_ereg_replace("\n", ' ', $str);
    $str = mb_ereg_replace("\r", ' ', $str);

    $ostr = '';
    while ($ostr != $str) {
        $ostr = $str;
        $str = mb_ereg_replace('  ', ' ', $str);
    }

    $astr = mb_split(' ', $str);
    $str = '';

    ftsearch_load_ignores();

    for ($i = count($astr) - 1; $i >= 0; $i--) {
        // ignore?
        if ($ftsearch_ignores != "" && array_search(mb_strtolower($astr[$i]), $ftsearch_ignores) !== false)
        {
            unset($astr[$i]);
        }
        else {
            if ($simple)
                $astr[$i] = ftsearch_text2simple($astr[$i]);

            if ($astr[$i] == '')
                unset($astr[$i]);
        }
    }

    return $astr;
}

function ftsearch_load_ignores()
{
    global $ftsearch_ignores;
    global $ftsearch_ignores_loaded;

    if ($ftsearch_ignores_loaded != true) {
        $rs = XDb::xSql('SELECT `word` FROM `search_ignore`');
        while ($r = XDb::xFetchArray($rs))
            $ftsearch_ignores[] = $r['word'];
        XDb::xFreeResults($rs);

        $ftsearch_ignores_loaded = true;
    }
}

// str = single word
function ftsearch_text2simple($str)
{
    global $ftsearch_simplerules;

    $str = ftsearch_text2sort($str);

    // apply rules
    foreach ($ftsearch_simplerules AS $rule) {
        $str = mb_ereg_replace($rule[0], $rule[1], $str);
    }

    // replace duplicate chars
    for ($c = ord('a'); $c <= ord('z'); $c++) {
        $old_str = '';
        while ($old_str != $str) {
            $old_str = $str;
            $str = mb_ereg_replace(chr($c) . chr($c), chr($c), $str);
        }
        $old_str = '';
    }

    return $str;
}

// str = single word
function ftsearch_text2sort($str)
{
    $str = mb_strtolower($str);

    // German
    $str = mb_ereg_replace('ä', 'ae', $str);
    $str = mb_ereg_replace('ö', 'oe', $str);
    $str = mb_ereg_replace('ü', 'ue', $str);
    $str = mb_ereg_replace('Ä', 'ae', $str);
    $str = mb_ereg_replace('Ö', 'oe', $str);
    $str = mb_ereg_replace('Ü', 'ue', $str);
    $str = mb_ereg_replace('ß', 'ss', $str);

    // accents etc.
    $str = mb_ereg_replace('a', 'a', $str);
    $str = mb_ereg_replace('á', 'a', $str);
    $str = mb_ereg_replace('â', 'a', $str);
    $str = mb_ereg_replace('e', 'e', $str);
    $str = mb_ereg_replace('é', 'e', $str);
    $str = mb_ereg_replace('ë', 'e', $str);
    $str = mb_ereg_replace('É', 'e', $str);
    $str = mb_ereg_replace('ô', 'o', $str);
    $str = mb_ereg_replace('ó', 'o', $str);
    $str = mb_ereg_replace('o', 'o', $str);
    $str = mb_ereg_replace('e', 'e', $str);
    $str = mb_ereg_replace('ě', 'e', $str);
    $str = mb_ereg_replace('u', 'u', $str);
    $str = mb_ereg_replace('ç', 'c', $str);
    $str = mb_ereg_replace('c', 'c', $str);
    $str = mb_ereg_replace('ć', 'c', $str);
    $str = mb_ereg_replace('î', 'i', $str);
    $str = mb_ereg_replace('i', 'i', $str);
    $str = mb_ereg_replace('i', 'i', $str);
    $str = mb_ereg_replace('í', 'i', $str);
    $str = mb_ereg_replace('ł', 'l', $str);
    $str = mb_ereg_replace('š', 's', $str);
    $str = mb_ereg_replace('Š', 's', $str);
    $str = mb_ereg_replace('u', 'u', $str);
    $str = mb_ereg_replace('ý', 'y', $str);
    $str = mb_ereg_replace('ž', 'z', $str);
    $str = mb_ereg_replace('Ž', 'Z', $str);

    $str = mb_ereg_replace('A', 'ae', $str);
    $str = mb_ereg_replace('a', 'ae', $str);
    $str = mb_ereg_replace('o', 'oe', $str);

    // other
    $str = mb_ereg_replace('[^A-Za-z ]', '', $str);

    return $str;
}

function ftsearch_refresh()
{
    ftsearch_refresh_all_caches();
    ftsearch_refresh_all_cache_desc();
    ftsearch_refresh_all_pictures();
    ftsearch_refresh_all_cache_logs();
}

function ftsearch_refresh_all_caches()
{
    $rs = XDb::xSql(
        'SELECT `caches`.`cache_id`
        FROM `caches`
            LEFT JOIN `search_index_times` ON `caches`.`cache_id`=`search_index_times`.`object_id`
            AND 2=`search_index_times`.`object_type`
        WHERE `caches`.`status`!=5 AND `caches`.`status`!=6
            AND `caches`.`status`!= 4
            AND ISNULL(`search_index_times`.`object_id`)
        UNION DISTINCT
            SELECT `caches`.`cache_id`
            FROM `caches`
                INNER JOIN `search_index_times` ON `search_index_times`.`object_type`=2
                AND `caches`.`cache_id`=`search_index_times`.`object_id`
            WHERE `caches`.`last_modified`>`search_index_times`.`last_refresh`
                AND `caches`.`status`!=5 AND `caches`.`status`!=6
                AND `caches`.`status`!=4');

    while ($r = XDb::xFetchArray($rs))
        ftsearch_refresh_cache($r['cache_id']);
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_cache($cache_id)
{
    $rs = XDb::xSql("SELECT `name`, `last_modified` FROM `caches` WHERE `cache_id`= ? LIMIT 1", $cache_id);
    if ($r = XDb::xFetchArray($rs)) {
        ftsearch_set_entries(2, $cache_id, $cache_id, $r['name'], $r['last_modified']);
    }
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_all_cache_desc()
{
    $rs = XDb::xSql(
            'SELECT `cache_desc`.`id`
            FROM `cache_desc`
                INNER JOIN `caches` ON `caches`.`cache_id`=`cache_desc`.`cache_id`
                LEFT JOIN `search_index_times` ON `cache_desc`.`id`=`search_index_times`.`object_id`
                    AND 3=`search_index_times`.`object_type`
            WHERE `caches`.`status`!=5 AND `caches`.`status`!=6
                AND `caches`.`status`!= 4 AND ISNULL(`search_index_times`.`object_id`)
            UNION DISTINCT
                SELECT `cache_desc`.`id`
                FROM `cache_desc`
                    INNER JOIN `caches` ON `caches`.`cache_id`=`cache_desc`.`cache_id`
                    INNER JOIN `search_index_times` ON `search_index_times`.`object_type`=3
                        AND `cache_desc`.`id`=`search_index_times`.`object_id`
                WHERE `cache_desc`.`last_modified`>`search_index_times`.`last_refresh`
                    AND `caches`.`status`!=5 AND `caches`.`status`!=6
                    AND `caches`.`status`!= 4');
    while ($r = XDb::xFetchArray($rs))
        ftsearch_refresh_cache_desc($r['id']);
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_cache_desc($id)
{
    $rs = XDb::xSql("SELECT `cache_id`, `desc`, `last_modified` FROM `cache_desc` WHERE `id`= ? LIMIT 1", $id);
    if ($r = XDb::xFetchArray($rs)) {
        $r['desc'] = ftsearch_strip_html($r['desc']);
        ftsearch_set_entries(3, $id, $r['cache_id'], $r['desc'], $r['last_modified']);
    }
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_all_pictures()
{
    $rs = XDb::xSql(
        'SELECT `pictures`.`id`
        FROM `pictures`
            LEFT JOIN `search_index_times` ON `pictures`.`id`=`search_index_times`.`object_id`
                AND 6=`search_index_times`.`object_type`
        WHERE ISNULL(`search_index_times`.`object_id`)
        UNION DISTINCT
            SELECT `pictures`.`id`
            FROM `pictures`
                INNER JOIN `search_index_times` ON `search_index_times`.`object_type`=6
                    AND `pictures`.`id`=`search_index_times`.`object_id`
            WHERE `pictures`.`last_modified`>`search_index_times`.`last_refresh`');

    while ($r = XDb::xFetchArray($rs))
        ftsearch_refresh_picture($r['id']);
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_picture($id)
{
    $rs = XDb::xSql(
        "SELECT `caches`.`cache_id`, `pictures`.`title`, `pictures`.`last_modified`
        FROM `pictures`
            INNER JOIN `caches` ON `pictures`.`object_type`=2
                AND `caches`.`cache_id`=`pictures`.`object_id`
        WHERE `pictures`.`id`= ?
        UNION DISTINCT
            SELECT `cache_logs`.`cache_id` , `pictures`.`title`, `pictures`.`last_modified`
            FROM `pictures` INNER JOIN `cache_logs` ON `pictures`.`object_type`=1
                AND `cache_logs`.`id`=`pictures`.`object_id`
            WHERE `cache_logs`.`deleted`=0 AND `pictures`.`id`= ?
            LIMIT 1", $id, $id);
    if ($r = XDb::xFetchArray($rs)) {
        ftsearch_set_entries(6, $id, $r['cache_id'], $r['title'], $r['last_modified']);
    }
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_all_cache_logs()
{
    $rs = XDb::xSql(
        'SELECT `cache_logs`.`id`
        FROM `cache_logs`
            LEFT JOIN `search_index_times` ON `cache_logs`.`id`=`search_index_times`.`object_id`
                AND 1=`search_index_times`.`object_type`
        WHERE ISNULL(`search_index_times`.`object_id`)
        UNION DISTINCT
        SELECT `cache_logs`.`id`
        FROM `cache_logs`
            INNER JOIN `search_index_times` ON `search_index_times`.`object_type`=1
            AND `cache_logs`.`id`=`search_index_times`.`object_id`
        WHERE `cache_logs`.`last_modified`>`search_index_times`.`last_refresh`
            AND `cache_logs`.`deleted`=0');

    while ($r = XDb::xFetchArray($rs))
        ftsearch_refresh_cache_logs($r['id']);
    XDb::xFreeResults($rs);
}

function ftsearch_refresh_cache_logs($id)
{
    $rs = XDb::xSql(
        "SELECT `cache_id`, `text`, `last_modified`
        FROM `cache_logs`
        WHERE `id`= ?
            AND `cache_logs`.`deleted` = ? ", $id, 0);
    if ($r = XDb::xFetchArray($rs)) {
        $r['text'] = ftsearch_strip_html($r['text']);
        ftsearch_set_entries(1, $id, $r['cache_id'], $r['text'], $r['last_modified']);
    }
    XDb::xFreeResults($rs);
}

function ftsearch_delete_entries($object_type, $object_id, $cache_id)
{
    XDb::xSql(
        "DELETE FROM `search_index`
        WHERE `object_type`= ?
            AND `cache_id`= ?",
        $object_type, $cache_id);

    XDb::xSql(
        "DELETE FROM `search_index_times`
        WHERE `object_type`= ?
            AND `object_id`= ?",
        $object_type, $object_id);
}

function ftsearch_set_entries($object_type, $object_id, $cache_id, &$text, $last_modified)
{
    ftsearch_delete_entries($object_type, $object_id, $cache_id);

    $ahash = ftsearch_hash($text);
    foreach ($ahash AS $k => $h) {
        XDb::xSql(
            "INSERT DELAYED INTO `search_index` (`object_type`, `cache_id`, `hash`, `count`)
            VALUES ( ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `count`=`count`+1",
            $object_type, $cache_id, $h, 1);
    }
    XDb::xSql(
        "INSERT INTO `search_index_times` (`object_id`, `object_type`, `last_refresh`)
        VALUES (?,?,?) ON DUPLICATE KEY UPDATE `last_refresh`= ? ",
        $object_id, $object_type, $last_modified, $last_modified);
}

function ftsearch_strip_html($text)
{
    $text = str_replace("\n", ' ', $text);
    $text = str_replace("\r", ' ', $text);
    $text = str_replace('<br />', ' ', $text);
    $text = str_replace('<br/>', ' ', $text);
    $text = str_replace('<br />', ' ', $text);
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');

    return $text;
}
