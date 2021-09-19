<?php

// created on 2021-09-05 by kojoty

namespace src\Utils\Database\Updates;

class C1630794421576 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '0BB2968A-26FF-720B-2D0F-1A96F556DC8A',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Insert your update code here, using $this->db for database access.

        // Add comment to rr_comment column
        $this->db->simpleQuery(
            "ALTER TABLE `cache_desc` CHANGE `rr_comment` `rr_comment` TINYTEXT
             COMMENT 'OcTeam notes displayed in geocache description'");

        // Add new column for reactivation description
        $this->db->simpleQuery(
            "ALTER TABLE `cache_desc` ADD `reactivation_rule`
                TINYTEXT
                DEFAULT NULL
                COMMENT 'Geocache reactivation rules defined by geocache user'
                AFTER `rr_comment`");

        // Drop unused column in cache decsr. table
        $this->db->simpleQuery("ALTER TABLE `cache_desc` DROP `desc_htmledit`");

        // The update will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!
    }

};

return new C1630794421576;
