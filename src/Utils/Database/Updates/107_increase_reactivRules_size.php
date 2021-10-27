<?php

// created on 2021-10-27 by kojoty

namespace src\Utils\Database\Updates;

class C16353677532734 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => 'A394FF94-0D7F-C712-77B9-D957AB2A1EB4',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Increasing the size of rr_comment and reactivation_rule columns from TINYTEXT(255 bytes) to TEXT (65k bytes)

        $this->db->simpleQuery(
            "ALTER TABLE `cache_desc`
             CHANGE `rr_comment` `rr_comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
                COMMENT 'OcTeam notes displayed in geocache description',
             CHANGE `reactivation_rule` `reactivation_rule` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
                COMMENT 'Geocache reactivation rules defined by geocache user'"
        );
    }
};

return new C16353677532734;
