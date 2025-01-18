<?php

// created on 2025-01-18 by stefan1214/stefopl

namespace src\Utils\Database\Updates;

class C17372103015411 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '05D20C85-ECDC-E743-7B6B-0FDB81FADE42',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Insert your update code here, using $this->db for database access.

        // The update will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!

        $this->db->simpleQuery(
            "INSERT INTO caches_additions (cache_id, altitude) SELECT c.cache_id, NULL FROM caches c LEFT JOIN caches_additions ca ON c.cache_id = ca.cache_id WHERE ca.cache_id IS NULL"
        );

    }

    public function rollback()
    {
        // If possible and feasible, provide code here which reverses the
        // changes made by run(). Otherwiese please REMOVE the rollback method.
        // This will disable the "rollback" action on the Admin.DbUpdate page.

        // The rollback will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!
    }
};

return new C17372103015411;
