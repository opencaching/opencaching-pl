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

        // Owner types. translation_key is only a key from lib/languages/en.php.
        $this->db->createTableIfNotExists(
            'badges_owners_types',
            [
                '`id` int(11) NOT NULL AUTO_INCREMENT',
                '`translation_key` varchar(255) NOT NULL',
                'PRIMARY KEY (`id`)',
            ],
            ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb3']
        );

        $this->db->simpleQuery(
            "INSERT IGNORE INTO `badges_owners_types` (`id`, `translation_key`) VALUES
             (1, 'badges_owner_type_keeper'),
             (2, 'badges_owner_type_description_author'),
             (3, 'badges_owner_type_graphic_author')"
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

        // Quick on/off switch for a badge - there is currently no such toggle.
        $this->db->addColumnIfNotExists('badges', 'active', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge is active');
        // Controls whether the badge is shown on the map in the new user interface component.
        $this->db->addColumnIfNotExists('badges', 'visible_in_map', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge is visible on the map');
        // Long display name of the badge (e.g. for UI / map).
        $this->db->addColumnIfNotExists('badges', 'long_name', 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'Long name of the badge');
        // Badge is displayed only to its owners and to OC Team members.
        $this->db->addColumnIfNotExists('badges', 'display_restricted', 'TINYINT(1) NOT NULL DEFAULT 0', 'Display the badge only to its owners and OC Team members');
        // Type of the short description (FK to badges_short_description_types.id).
        $this->db->addColumnIfNotExists('badges', 'short_description_type', 'INT(11) NOT NULL DEFAULT 0', 'Type of the short description');

        // Short description types. translation_key is only a key from lib/languages/en.php.
        $this->db->createTableIfNotExists(
            'badges_short_description_types',
            [
                '`id` int(11) NOT NULL AUTO_INCREMENT',
                '`translation_key` varchar(255) NOT NULL',
                'PRIMARY KEY (`id`)',
            ],
            ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb3']
        );

        $this->db->simpleQuery(
            "INSERT IGNORE INTO `badges_short_description_types` (`id`, `translation_key`) VALUES
             (1, 'badge_short_desc_find_caches'),
             (2, 'badge_short_desc_log_geopaths'),
             (3, 'badge_short_desc_log_service'),
             (4, 'badge_short_desc_log_service_others'),
             (5, 'badge_short_desc_move_mobile_caches'),
             (6, 'badge_short_desc_move_mobile_caches_others'),
             (7, 'badge_short_desc_complete_matrix')"
        );

        // Translated badge descriptions (one row per badge and language).
        $this->db->createTableIfNotExists(
            'badges_description',
            [
                '`id_badge` int(11) NOT NULL',
                '`language` char(2) NOT NULL',
                '`description` text NOT NULL',
                'PRIMARY KEY (`id_badge`, `language`)',
                'UNIQUE KEY `badge_language` (`id_badge`, `language`)',
            ],
            ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb3']
        );
    }

    public function rollback()
    {
        // If possible and feasible, provide code here which reverses the
        // changes made by run(). Otherwiese please REMOVE the rollback method.
        // This will disable the "rollback" action on the Admin.DbUpdate page.

        // The rollback will be run inside a transaction. It will also run
        // with set_time_limit(0), so don't create any endless loops!

        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_description`');
        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_short_description_types`');

        $this->db->dropColumnIfExists('badges', 'short_description_type');
        $this->db->dropColumnIfExists('badge_categories', 'sort_desc');
        $this->db->dropColumnIfExists('badges', 'display_restricted');
        $this->db->dropColumnIfExists('badges', 'long_name');
        $this->db->dropColumnIfExists('badges', 'visible_in_map');
        $this->db->dropColumnIfExists('badges', 'active');

        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_owners`');
        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_owners_types`');
    }
};

return new C111BadgesOwners;
