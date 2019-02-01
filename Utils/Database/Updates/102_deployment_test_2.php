<?php

// created on 2019-01-25 by following5

namespace Utils\Database\Updates;

return new class extends UpdateScript
{
    public function getProperties()
    {
        return [
            'uuid' => '58FB1906-D716-32DC-C3B5-D4C3B3B0422C',  // do not change
            'run' => 'auto',  // must be 'auto' for all regular updates
        ];
    }

    public function run()
    {
        // test for code deployment
        sleep(600);
    }

    public function rollback()
    {
    }
};
