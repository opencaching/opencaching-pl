<?php

// created on 2019-05-22 by kojoty

namespace src\Utils\Database\Updates;

class C15585603230246 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => 'E708387B-FEAC-D57A-A068-51F9CA689E17',
            'run' => 'auto',
        ];
    }

    public function run()
    {
        // Insert your update code here, using $this->db for database access.
        $this->db->simpleQuery("ALTER TABLE caches_additions ADD PRIMARY KEY(cache_id)");
        $this->db->simpleQuery("ALTER TABLE caches_additions DROP INDEX cache_id");

        echo "update done :)";
    }
};

return new C15585603230246;
