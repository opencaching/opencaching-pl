<?php

// created on 2019-10-15 by kojoty

/**
 * Remove tables related to depreciated ocXML interface
 */
namespace src\Utils\Database\Updates;

class C15711731985956 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '4542B3D9-453C-70A0-B212-0CFDDCAED7B8',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Insert your update code here, using $this->db for database access.

        $this->db->dropTableIfExists ("xmlsession_data");
        $this->db->dropTableIfExists ("xmlsession");

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

return new C15711731985956;
