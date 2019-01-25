<?php

// created on 2019-01-25 by following5

namespace Utils\Database\Updates;

return new class extends UpdateScript
{
    public function getProperties()
    {
        return [
            'uuid' => 'EBC06680-3262-1718-D41E-28EA261325DE',  // do not change
            'run' => 'auto',  // must be 'auto' for all regular updates
        ];
    }

    public function run()
    {
        // Dummy update, just for testing the code deployment
    }

    public function rollback()
    {
        // Dummy update, just for testing the code deployment
    }
};
