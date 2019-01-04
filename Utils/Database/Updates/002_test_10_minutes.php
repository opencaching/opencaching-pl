<?php

namespace Utils\Database\Updates;

return new class extends UpdateScript
{
    public function getProperties()
    {
        return [
            'uuid' => '4B2ABA15-70C9-036B-00F1-412D6E1183C5',  // do not change
            'run' => 'manual',  // do not change
        ];
    }

    public function run()
    {
        // Test if a long database update is not aborted by some timeout.
        // max_execution_time was set in DbUpdate.

        sleep(600);
        echo "Ok\n";
    }
};
