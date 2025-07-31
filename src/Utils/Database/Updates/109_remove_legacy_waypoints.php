<?php

// created on 2025-07-10 by rborowski

namespace src\Utils\Database\Updates;

return new class () extends UpdateScript {
    public function getProperties() : array
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '838F9C48-09A3-44FD-AC2B-C094A47770CC',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run() : void
    {
        // Insert your update code here, using $this->db for database access.

        // The update will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!

        $this->db->simpleQueries("ALTER TABLE `caches` DROP COLUMN `wp_nc`, DROP COLUMN `wp_ge`, DROP COLUMN `wp_qc`;");
    }

}; 