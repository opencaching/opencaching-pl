<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/

class OrgCacheOwners
{
    private $db;
    private $verbose;
    function __construct(dataBase $db)
    {
        $this->db = $db;
    }

    public function populateForCache($cache_id)
    {
        $this->verbose = false;
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
                    and cl.cache_id = :1
                order by cl.id";
        $this->db->multiVariableQuery($sql, $cache_id);
        $this->process();

    }

    public function populateAll()
    {
        $this->verbose = true;

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
                order by cl.id";

        set_time_limit(360);
        $this->db->simpleQuery($sql);
        $this->process();
        set_time_limit(60);

    }

    private function process()
    {
        $results = $this->db->dbResultFetchAll();
        foreach($results as $key => $r){
            if ($this->verbose){
               echo $r['cache_id'].','.$r['id'].',';
            }
            $matches = array();
            $matched = preg_match(
                    '/viewprofile\\.php\\?userid=([0-9]+)["\'](.*)viewprofile\\.php\\?userid=([0-9]+)["\']/i',
                    $r['text'], $matches);
            if (!$matched){
                if ($this->verbose){
                    echo 'does-not-match,,'.$r['text']."\n";
                }
                continue;
            }
            $org_user_id = $matches[1];
            $new_user_id = $matches[3];
            $sql = 'update caches set org_user_id = :1 where cache_id = :2 and org_user_id is null';
            $this->db->multiVariableQuery($sql, $org_user_id, $r['cache_id']);
            if ($this->verbose){
                if ($this->db->rowCount() > 0){
                    echo "updated,$org_user_id,\n";
                } else {
                    echo "skipped,,\n";
                }
            }
        }
    }
}

?>
