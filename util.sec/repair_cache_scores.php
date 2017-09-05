<?php
use Utils\Database\OcDb;

require_once __DIR__ . '/../lib/ClassPathDictionary.php';

class RepairCacheScores
{

    function run()
    {
        $db = OcDb::instance();

        $sql = "SELECT cache_id FROM caches";
        $params = array();
        if (isset($_GET['cache_id'])) {
            $sql .= ' WHERE cache_id=:cache_id';
            $params['cache_id']['value'] = intval($_GET['cache_id']);
            $params['cache_id']['data_type'] = 'integer';
        }

        $s = $db->paramQuery($sql, $params);
        $caches = $db->dbResultFetchAll($s);

        set_time_limit(3600);
        $total_touched = 0;
        foreach ($caches as $cache) {
            $cache_id = $cache['cache_id'];

            $db->multiVariableQuery("DELETE FROM scores WHERE cache_id = :1 AND user_id NOT IN (
                    SELECT user_id FROM cache_logs WHERE deleted=0 AND cache_id = :1
                )", $cache_id);

            // recalculate scores
            $s = $db->multiVariableQuery("
                SELECT AVG(score) AS avg_score, COUNT(score) AS votes FROM scores WHERE cache_id = :1", $cache_id);
            $row = $db->dbResultFetch($s);
            if ($row == false) {
                $votes = 0;
                $average = 0;
            } else {
                $votes = $row['votes'];
                if ($votes > 0) {
                    $average = round($row['avg_score'], 4);
                } else {
                    $average = 0;
                }
            }
            unset($row);

            // repair founds etc.
            $founds = $db->multiVariableQueryValue("SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = :1 AND (type=1 OR type=7)", 0, $cache_id);
            $notfounds = $db->multiVariableQueryValue("SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = :1 AND (type=2 OR type=8)", 0, $cache_id);
            $notes = $db->multiVariableQueryValue("SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = :1 AND type=3", 0, $cache_id);
            $watchers = $db->multiVariableQueryValue("SELECT count(*) FROM cache_watches WHERE cache_id = :1", 0, $cache_id);
            $ignorers = $db->multiVariableQueryValue("SELECT count(*) FROM cache_ignore WHERE cache_id = :1", 0, $cache_id);
            $last_found = $db->multiVariableQueryValue("SELECT MAX(`cache_logs`.`date`) FROM `cache_logs` WHERE `cache_logs`.`type`IN (1, 7) AND `cache_logs`.`cache_id`= :1 AND `cache_logs`.`deleted` = 0", null, $cache_id);
            $sql = "
                UPDATE caches
                SET
                    votes=:new_votes,
                    score=:new_score,
                    founds=:new_founds,
                    notfounds=:new_notfounds,
                    notes=:new_notes,
                    watcher=:new_watchers,
                    ignorer_count=:new_ignorers,
                    last_found = :new_last_found
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
                        OR last_found!=:new_last_found
                    )
            ";
            $params = array();
            $params['new_votes']['value'] = intval($votes);
            $params['new_votes']['data_type'] = 'integer';
            $params['new_score']['value'] = strval($average);
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
            $params['new_last_found']['value'] = (is_null($last_found)) ? null : strval($last_found);
            $params['new_last_found']['data_type'] = (is_null($last_found)) ? 'null' : 'string';
            $params['cache_id']['value'] = intval($cache_id);
            $params['cache_id']['data_type'] = 'integer';
            $s = $db->paramQuery($sql, $params);
            if ($db->rowCount($s) > 0) {
                echo "<b>cache_id=$cache_id</b> ";
                echo "ratings=$votes rating=$average ";
                echo "founds=$founds notfounds=$notfounds ";
                echo "notes=$notes watchers=$watchers ";
                echo "ignorers=$ignorers last_found=$last_found<br>";
                $total_touched ++;
            }
        }
        set_time_limit(60);
        echo "-----------------------------------<br>total_touched=$total_touched";
    }
}

$rcs = new RepairCacheScores();
$rcs->run();
