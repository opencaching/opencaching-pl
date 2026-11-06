<?php

// created on 2026-11-06 by developer

namespace src\Utils\Database\Updates;

class C111BadgesOwners extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '922CA1C2-3E72-4AA9-B13D-7272CF48387D',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        // Insert your update code here, using $this->db for database access.

        // The update will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!

        // Currently badge owners are stored as HTML text (e.g. '<a href="...">Bartek111</a>').
        // This migration introduces proper DB relations (badges_owners + badges_owners_types).
        // Old text columns will be removed later, once all badges have been re-linked in the database.
        $this->db->createTableIfNotExists(
            'badges_owners_types',
            [
                '`id` int(11) NOT NULL AUTO_INCREMENT',
                '`name` varchar(50) NOT NULL',
                'PRIMARY KEY (`id`)',
            ],
            ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb3']
        );

        $this->db->createTableIfNotExists(
            'badges_owners',
            [
                '`id_badge` int(11) NOT NULL',
                '`id_owner` int(11) NOT NULL',
                '`id_owner_type` int(11) NOT NULL',
                '`position` int(11) NOT NULL DEFAULT 0',
                'UNIQUE KEY `badge_owner_type` (`id_badge`,`id_owner`,`id_owner_type`)',
            ],
            ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb3']
        );

        // Initial owner types.
        $this->db->simpleQuery(
            "INSERT INTO `badges_owners_types` (`id`, `name`) VALUES
             (1, 'Badge keeper'),
             (2, 'Description author'),
             (3, 'Graphic author')"
        );

        // Quick on/off switch for a badge - there is currently no such toggle.
        $this->db->addColumnIfNotExists('badges', 'active', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge is active');
        // Controls whether the badge is shown on the map in the new user interface component.
        $this->db->addColumnIfNotExists('badges', 'visible_in_map', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge is visible on the map');

        // Long display name of the badge (e.g. for UI / map).
        $this->db->addColumnIfNotExists('badges', 'long_name', 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'Long name of the badge');

        // Sort order for badges within a category: 0 = ascending, 1 = descending.
        // This distinction is introduced for periodic (annual) proficiencies: every year
        // there will be the same badge types, but shifted by one year. This field makes
        // it easier to control their ordering - the order is adjusted only on new badges,
        // not on all existing ones.
        // Inspiration for this column comes from Strava and Garmin Connect.
        $this->db->addColumnIfNotExists('badge_categories', 'sort_desc', 'TINYINT(1) NOT NULL DEFAULT 0', 'Sort badges in this category descending (1) instead of ascending (0)');
    }

    public function rollback()
    {
        // If possible and feasible, provide code here which reverses the
        // changes made by run(). Otherwiese please REMOVE the rollback method.
        // This will disable the "rollback" action on the Admin.DbUpdate page.

        // The rollback will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!

        $this->db->dropColumnIfExists('badge_categories', 'sort_desc');
        $this->db->dropColumnIfExists('badges', 'long_name');
        $this->db->dropColumnIfExists('badges', 'visible_in_map');
        $this->db->dropColumnIfExists('badges', 'active');

        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_owners`');
        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_owners_types`');
    }
};

return new C111BadgesOwners;

