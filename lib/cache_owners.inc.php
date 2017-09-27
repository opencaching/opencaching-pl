<?php

use Utils\Database\OcDb;

class OrgCacheOwners
{

    private $db;
    private $verbose;

    function __construct(OcDb $db)
    {
        $this->db = $db;
    }

    public function populateForCache($cache_id)
    {
        $this->verbose = false;

        $stmt = $this->db->multiVariableQuery(
            "SELECT cl.id, cl.cache_id, cl.text
            FROM cache_logs cl join caches c using (cache_id)
            WHERE cl.user_id = -1 AND cl.type = 3
                AND cl.text_html >= 1 AND
                (
                    cl.text like '%Przeprowadzono procedurę adopcji skrzynki%'
                    OR cl.text like '%The adoption process has been completed%'
                    OR cl.text like '%Adopţia s-a efectuat%'
                    OR cl.text like '%De adoptie is voltooid%'
                )
                AND c.org_user_id is null
                AND c.status in (1,2,3)
                AND cl.cache_id = :1
            ORDER by cl.id",
            $cache_id);

        $this->process( $stmt );
    }

    public function populateAll()
    {
        $this->verbose = true;

        header('Content-Type: text/plain');
        echo "cache_id,log_id,status,user_id,log_text\n";

        set_time_limit(360);
        $stmt = $this->db->simpleQuery(
            "SELECT cl.id, cl.cache_id, cl.text
            FROM cache_logs cl join caches c using (cache_id)
            WHERE cl.user_id = -1
                AND cl.type = 3
                AND cl.text_html = 1
                AND (
                    cl.text like '%Przeprowadzono procedurę adopcji skrzynki%'
                    OR cl.text LIKE '%The adoption process has been completed%'
                    OR cl.text LIKE '%Adopţia s-a efectuat%'
                    OR cl.text LIKE '%De adoptie is voltooid%'
                )
                AND c.org_user_id is null
                AND c.status IN (1,2,3)
            ORDER BY cl.id ");

            $this->process( $stmt );
        set_time_limit(60);
    }

    private function process(PDOStatement $stmt)
    {
        $results = $this->db->dbResultFetchAll($stmt);

        foreach ($results as $key => $r) {
            if ($this->verbose) {
                echo $r['cache_id'] . ',' . $r['id'] . ',';
            }
            $matches = array();
            $matched = preg_match(
                    '/viewprofile\\.php\\?userid=([0-9]+)["\'](.*)viewprofile\\.php\\?userid=([0-9]+)["\']/i', $r['text'], $matches);
            if (!$matched) {
                if ($this->verbose) {
                    echo 'does-not-match,,' . $r['text'] . "\n";
                }
                continue;
            }
            $org_user_id = $matches[1];
            $new_user_id = $matches[3];

            $stmt = $this->db->multiVariableQuery(
                'UPDATE caches SET org_user_id = :1 WHERE cache_id = :2 AND org_user_id IS null',
                $org_user_id, $r['cache_id']);

            if ($this->verbose) {
                if ($this->db->rowCount( $stmt ) > 0) {
                    echo "updated,$org_user_id,\n";
                } else {
                    echo "skipped,,\n";
                }
            }
        }
    }

}

