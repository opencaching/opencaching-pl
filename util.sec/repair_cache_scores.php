<?php

use Utils\Database\OcDb;
$rootpath = '../';

require_once __DIR__ . '/../lib/ClassPathDictionary.php';

class RepairCacheScores
{

    function run()
    {
        $sql = "SELECT cache_id, status FROM caches";

        $db = OcDb::instance();

        $params = array();
        if (isset($_GET['cache_id'])) {
            $sql .= ' where cache_id=:cache_id';
            $params['cache_id']['value'] = intval($_GET['cache_id']);
            $params['cache_id']['data_type'] = 'integer';
        }

        $s = $db->paramQuery($sql, $params);
        $caches = $db->dbResultFetchAll($s);

        set_time_limit(3600);
        $total_touched = 0;
        foreach ($caches as $cache) {
            $cache_id = $cache['cache_id'];
            // usuniecie falszywych ocen
            //echo "cache_logs.cache_id=".sql_escape($rs['cache_id']).", user.username=".sql_escape($rs['user_id'])."<br />";
            //$sql = "DELETE FROM scores WHERE cache_id = '".sql_escape($rs['cache_id'])."' AND user_id = '".sql_escape($rs['user_id'])."'";
            //mysql_query($sql);

            $db->multiVariableQuery(
                    "delete from scores where cache_id = :1 and user_id not in (
                    select user_id from cache_logs where deleted=0 and cache_id = :2
                )", $cache_id, $cache_id);

            // zliczenie ocen po usunieciu
            $s = $db->multiVariableQuery(
                    "SELECT avg(score) as avg_score, count(score) as votes FROM scores WHERE cache_id = :1", $cache_id);
            $row = $db->dbResultFetch($s);
            if ($row == false) {
                $liczba = 0;
                $srednia = 0;
            } else {
                $liczba = $row['votes'];
                if ($liczba > 0) {
                    $srednia = round($row['avg_score'], 4);
                } else {
                    $srednia = 0;
                }
            }
            unset($row);

            // repair founds
            $founds = $db->multiVariableQueryValue(
                    "SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = :1 AND (type=1 OR type=7)", 0, $cache_id);
            $notfounds = $db->multiVariableQueryValue(
                    "SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = :1 AND (type=2 OR type=8)", 0, $cache_id);
            $notes = $db->multiVariableQueryValue(
                    "SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = :1 AND type=3", 0, $cache_id);
            $watchers = $db->multiVariableQueryValue(
                    "SELECT count(*) FROM cache_watches WHERE cache_id = :1", 0, $cache_id);
            $ignorers = $db->multiVariableQueryValue(
                    "SELECT count(*) FROM cache_ignore WHERE cache_id = :1", 0, $cache_id);

            $sql = "
                UPDATE caches
                SET
                    votes=:new_votes,
                    score=:new_score,
                    founds=:new_founds,
                    notfounds=:new_notfounds,
                    notes=:new_notes,
                    watcher=:new_watchers,
                    ignorer_count=:new_ignorers
                WHERE
                    cache_id=:cache_id
                    AND (
                        votes is null
                        OR score is null
                        OR founds is null
                        OR notfounds is null
                        OR notes is null
                        OR watcher is null
                        OR ignorer_count is null
                        OR votes!=:new_votes
                        OR abs(score-:new_score)>0.0001
                        OR founds!=:new_founds
                        OR notfounds!=:new_notfounds
                        OR notes!=:new_notes
                        OR watcher!=:new_watchers
                        OR ignorer_count!=:new_ignorers
                    )
            ";

            $params = array();
            $params['new_votes']['value'] = intval($liczba);
            $params['new_votes']['data_type'] = 'integer';
            $params['new_score']['value'] = strval($srednia);
            $params['new_score']['data_type'] = 'string';
            $params['new_founds']['value'] = intval($founds);
            $params['new_founds']['data_type'] = 'integer';
            $params['new_notfounds']['value'] = intval($notfounds);
            $params['new_notfounds']['data_type'] = 'integer';
            $params['new_notes']['value'] = intval($notes);
            $params['new_notes']['data_type'] = 'integer';
            $params['new_watchers']['value'] = intval($watchers);
            $params['new_watchers']['data_type'] = 'integer';
            $params['new_ignorers']['value'] = intval($ignorers);
            $params['new_ignorers']['data_type'] = 'integer';
            $params['cache_id']['value'] = intval($cache_id);
            $params['cache_id']['data_type'] = 'integer';
            $s = $db->paramQuery($sql, $params);
            if ($db->rowCount($s) > 0) {
                echo "<b>cache_id=$cache_id</b><br>";
                echo "ratings=$liczba<br>rating=$srednia<br>";
                echo "founds=$founds<br>notfounds=$notfounds<br>";
                echo "notes=$notes<br>watchers=$watchers<br>";
                echo "ignorers=$ignorers<br>";
                $total_touched++;
            }
        }

        set_time_limit(60);
        echo "-----------------------------------<br>total_touched=$total_touched<br>";
    }

}

$rcs = new RepairCacheScores();
$rcs->run();

