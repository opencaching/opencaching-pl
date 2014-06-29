<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/
    $rootpath = '../';
    require_once($rootpath.'lib/clicompatbase.inc.php');
    require_once($rootpath.'lib/db.php');

class PopulateOrgCacheOwners
{

    function run()
    {
        $db = new dataBase();
        $db2 = new dataBase();
        $db2->beginTransaction();
        header('Content-Type: text/plain');
        echo "cache_id,log_id,status,user_id,log_text\n";

        $sql = "select cl.id, cl.cache_id, cl.text
                from cache_logs cl join caches c using (cache_id) 
                where 
                        cl.user_id = -1
                    and cl.type = 3
                    and cl.text_html = 1
                    and (cl.text like '%Przeprowadzono procedurę adopcji skrzynki%'
                            or cl.text like '%The adoption process has been completed%'
                            or cl.text like '%Adopţia s-a efectuat%'
                            or cl.text like '%De adoptie is voltooid%'
                    )
                    and c.org_user_id is null
                    and c.status in (1,2,3)
                order by cl.id
                limit 10";

        set_time_limit(360);
        $db->simpleQuery($sql);
        while($r = $db->dbResultFetch()){
            echo $r['cache_id'].','.$r['id'].',';
            $matches = array();
            $matched = preg_match(
                '/viewprofile\\.php\\?userid=([0-9]+)["\'](.*)viewprofile\\.php\\?userid=([0-9]+)["\']/i', 
                $r['text'], $matches);
            if (!$matched){
                echo 'does-not-match,,'.$r['text']."\n";
                continue;
            }
            $org_user_id = $matches[1];
            $new_user_id = $matches[3];
            $sql = 'update caches set org_user_id = :1 where cache_id = :2 and org_user_id is null';
            $db2->multiVariableQuery($sql, $org_user_id, $r['cache_id']);
            if ($db2->rowCount() > 0){
                echo "updated,$org_user_id,\n";
            } else {
                echo "skipped,,\n";
            }
        }
        $db2->rollback();
        set_time_limit(60);
        unset($db);
        
    }

}

$pco = new PopulateOrgCacheOwners();
$pco->run();

?>
