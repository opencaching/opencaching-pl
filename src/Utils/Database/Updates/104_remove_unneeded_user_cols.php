<?php

namespace src\Utils\Database\Updates;

class C16176474938219 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '7CD508C4-F5E5-3F41-9BBC-4BFD7E935FC9',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Insert your update code here, using $this->db for database access.
        $this->db->simpleQueries("ALTER TABLE `user` DROP `cache_ignores`, DROP `admin`;");
        echo "..done";

        // The update will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!
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

return new C16176474938219;
