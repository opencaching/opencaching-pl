<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/
    $rootpath = '../';
    require_once($rootpath.'lib/clicompatbase.inc.php');
    require_once($rootpath.'lib/db.php');

class RepairUserScores
{

    function run()
    {
        $db = new dataBase();
        $db->switchDebug(false);

        $sql = "SELECT user_id FROM user where user_id >= 0 ";

        $params = array();
        if (isset($_GET['user_id'])){
            $sql .= ' and user_id=:user_id';
            $params['user_id']['value'] = intval($_GET['user_id']);
            $params['user_id']['data_type'] = 'integer';
        }

        
        $db->paramQuery($sql, $params);
        $users = $db->dbResultFetchAll();
        set_time_limit(3600);
        $total_touched = 0;
        foreach($users as $user)
        {
            $user_id = $user['user_id'];

            // repair founds
            $founds_count = $db->multiVariableQueryValue(
                "SELECT count(id) FROM cache_logs WHERE deleted=0 AND user_id = :1 AND type=1", 0, $user_id);
            $notfounds_count = $db->multiVariableQueryValue(
                "SELECT count(id) FROM cache_logs WHERE deleted=0 AND user_id = :1 AND type=2", 0, $user_id);
            $log_notes_count = $db->multiVariableQueryValue(
                "SELECT count(id) FROM cache_logs WHERE deleted=0 AND user_id = :1 AND type=3", 0, $user_id);
            $cache_watches = $db->multiVariableQueryValue(
                "SELECT count(id) FROM cache_watches WHERE user_id = :1", 0, $user_id);
            $cache_ignores = $db->multiVariableQueryValue(
                "SELECT count(id) FROM cache_ignore WHERE user_id = :1", 0, $user_id);
            $hidden_count = $db->multiVariableQueryValue(
                "select count(cache_id) from caches where status in (1,2,3) and user_id = :1", 0, $user_id);
            
            $sql = "
                UPDATE user
                SET
                    hidden_count=:new_hidden_count,
                    cache_ignores=:new_cache_ignores,
                    log_notes_count=:new_log_notes_count,
                    founds_count=:new_founds_count,
                    notfounds_count=:new_notfounds_count,
                    cache_watches=:new_cache_watches
                WHERE
                    user_id=:user_id
                    AND (
                        hidden_count is null
                        OR cache_ignores is null
                        OR log_notes_count is null
                        OR founds_count is null
                        OR notfounds_count is null
                        OR cache_watches is null
                        OR hidden_count!=:new_hidden_count
                        OR cache_ignores!=:new_cache_ignores
                        OR log_notes_count!=:new_log_notes_count
                        OR founds_count!=:new_founds_count
                        OR notfounds_count!=:new_notfounds_count
                        OR cache_watches!=:new_cache_watches
                    )
            ";

            $params = array();
            $params['new_hidden_count']['value'] = intval($hidden_count);
            $params['new_hidden_count']['data_type'] = 'integer';
            $params['new_cache_ignores']['value'] = intval($cache_ignores);
            $params['new_cache_ignores']['data_type'] = 'integer';
            $params['new_log_notes_count']['value'] = intval($log_notes_count);
            $params['new_log_notes_count']['data_type'] = 'integer';
            $params['new_founds_count']['value'] = intval($founds_count);
            $params['new_founds_count']['data_type'] = 'integer';
            $params['new_notfounds_count']['value'] = intval($notfounds_count);
            $params['new_notfounds_count']['data_type'] = 'integer';
            $params['new_cache_watches']['value'] = intval($cache_watches);
            $params['new_cache_watches']['data_type'] = 'integer';
            $params['user_id']['value'] = intval($user_id);
            $params['user_id']['data_type'] = 'integer';
            $db->paramQuery($sql, $params);
            if ($db->rowCount() > 0){
                echo "<b>user_id=$user_id</b><br>";
                echo "hidden_count=$hidden_count<br>cache_ignores=$cache_ignores<br>";
                echo "log_notes_count=$log_notes_count<br>founds_count=$founds_count<br>";
                echo "notfounds_count=$notfounds_count<br>cache_watches=$cache_watches<br>";
                $total_touched++;
            }
            $db->closeCursor();
        }

        set_time_limit(60);
        unset($db);
        echo "-----------------------------------<br>total_touched=$total_touched<br>";
    }

}

$rus = new RepairUserScores();
$rus->run();

?>
