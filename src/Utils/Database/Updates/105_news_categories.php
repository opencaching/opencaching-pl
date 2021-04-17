<?php

// created on 2021-04-17 by sp2ong

namespace src\Utils\Database\Updates;

class C16186129048321 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '849AF90B-0CB4-2799-45D4-4184B7471539',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Insert your update code here, using $this->db for database access.

        // Add new column "category" in news table
        $this->db->simpleQuery(
            "ALTER TABLE `news` ADD `category` VARCHAR(10) NULL DEFAULT NULL
             COMMENT 'category of news allow to select only set of news' AFTER `id`");

        // Add "_news" cattegory to all current news records
        $this->db->simpleQuery("UPDATE news SET category = '_news'");

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

return new C16186129048321;
