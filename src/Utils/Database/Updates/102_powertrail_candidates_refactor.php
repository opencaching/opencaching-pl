<?php

// created on 2019-06-03 by kojoty

namespace src\Utils\Database\Updates;

class C1559544567139 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => 'F167AD0C-446C-93FD-1A09-8EB4481C0934',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // rename table: PowerTrail_cacheCandidate -> gp_cache_candidates

        // remove link

        // rename date -> submitted_date

        // add column state
        "ALTER TABLE `PowerTrail_cacheCandidate` ADD `state` ENUM('active','accepted','refused','canceled','expired') NOT NULL DEFAULT 'active' AFTER `date`";

        // add columns: submited_by, updated_by, updated_date

        // rename PowerTrailId -> gp_id

        // rename cacheId -> cache_id

    }

};

return new C1559544567139;
