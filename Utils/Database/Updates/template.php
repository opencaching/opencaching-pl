<?php

// created on {{creation_date}} by {{developer_name}}

namespace Utils\Database\Updates;

return new class extends UpdateScript
{
    public function getProperties()
    {
        return [
            'uuid' => '{{update_uuid}}',  // do not change
            'run' => 'auto',  // must be 'auto' for all regular updates
        ];
    }

    public function run()
    {
        // Insert your update code here, using $this->db for database access.

        // The update will be run inside a transaction.
    }

    public function rollback()
    {
        // If possible and feasible, provide code here which reverses the
        // changes made by run().

        // The rollback will be run inside a transaction.

        // IMPORTANT: If you do NOT write a rollback, please COMPLETELY REMOVE
        // the rollback() method. This will disable the "rollback" action on
        // the Admin.DbUpdate page.
    }
};
