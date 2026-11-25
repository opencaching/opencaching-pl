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
        // Quick on/off switch for a badge category.
        $this->db->addColumnIfNotExists('badge_categories', 'active', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge category is active');
        // Sort order of badges within the category: 0 = ascending, 1 = descending.
        $this->db->addColumnIfNotExists('badge_categories', 'sort_desc', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether badges in the category are sorted descending');
        // Controls whether the badge is shown on the map in the new user interface component.
        $this->db->addColumnIfNotExists('badges', 'visible_in_map', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge is visible on the map');
        // Long display name of the badge (e.g. for UI / map).
        $this->db->addColumnIfNotExists('badges', 'long_name', 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'Long name of the badge');
        // Badge is displayed only to its owners and to OC Team members.
        $this->db->addColumnIfNotExists('badges', 'display_restricted', 'TINYINT(1) NOT NULL DEFAULT 0', 'Display the badge only to its owners and OC Team members');
        // NOTE: `badges`.`description` is DEPRECATED and will be removed in a future
        // update. Descriptions are now stored in `badges_description` (per language).

        // DEPRECATED: `badges`.`graphic_author` will be removed in a future update.
        // Graphic author is now stored in `badges_owners` (id_owner_type = 3).
        // DEPRECATED: `badges`.`description_author` will be removed in a future update.
        // Description author is now stored in `badges_owners` (id_owner_type = 2).
        // DEPRECATED: `badges`.`attendant` will be removed in a future update.
        // Attendant is now stored in `badges_owners` (id_owner_type = 1).

        // Type of the short description (FK to badges_short_description_types.id).
        $this->db->addColumnIfNotExists('badges', 'short_description_type', 'INT(11) NOT NULL DEFAULT 0', 'Type of the short description');

        // SQL counting active caches for this badge. Must return a single row with column `cnt`.
        $this->db->addColumnIfNotExists('badges', 'count_query', 'TEXT NOT NULL', 'SQL counting active caches for this badge (returns cnt)');
        // SQL returning recommended caches for this badge. Must return `cache_id` and `cache_name` (from caches).
        $this->db->addColumnIfNotExists('badges', 'recommended_query', 'TEXT NOT NULL', 'SQL returning recommended caches (cache_id, cache_name)');
        // SQL returning caches of the week for this badge. Must return `cache_id` and `cache_name` (from caches).
        $this->db->addColumnIfNotExists('badges', 'titled_query', 'TEXT NOT NULL', 'SQL returning caches of the week (cache_id, cache_name)');

        // DEPRECATED: `cfg_period_threshold` will be removed in a future update.
        // Replaced by the boolean flag below (level_presentation_mode).

        // 0 = PERIOD (levels presented by period, e.g. years), 1 = THRESHOLD (levels presented by threshold).
        $this->db->addColumnIfNotExists('badges', 'level_presentation_mode', 'TINYINT(1) NOT NULL DEFAULT 0', 'How levels are presented: 0 = period, 1 = threshold');

        // Whether the badge can be edited by its owner and OC Team members.
        $this->db->addColumnIfNotExists('badges', 'can_edit', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge can be edited by its owner and OC Team members');
        // Whether the badge can currently be obtained/gained (visible but not obtainable when 0).
        $this->db->addColumnIfNotExists('badges', 'is_obtainable', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether the badge can currently be obtained');

        // DEPRECATED: `cfg_show_positions` will be removed in a future update.
        // Replaced by the two boolean flags below (show_positions_list / show_positions_map).

        // Show the "positions list" button for this badge.
        $this->db->addColumnIfNotExists('badges', 'show_positions_list', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether to show the positions list button');
        // Show the "positions map" button for this badge.
        $this->db->addColumnIfNotExists('badges', 'show_positions_map', 'TINYINT(1) NOT NULL DEFAULT 0', 'Whether to show the positions map button');

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

        $this->db->dropColumnIfExists('badges', 'titled_query');
        $this->db->dropColumnIfExists('badges', 'show_positions_map');
        $this->db->dropColumnIfExists('badges', 'show_positions_list');
        $this->db->dropColumnIfExists('badges', 'level_presentation_mode');
        $this->db->dropColumnIfExists('badges', 'is_obtainable');
        $this->db->dropColumnIfExists('badges', 'can_edit');
        $this->db->dropColumnIfExists('badges', 'recommended_query');
        $this->db->dropColumnIfExists('badges', 'count_query');
        $this->db->dropColumnIfExists('badges', 'short_description_type');

        $this->db->dropColumnIfExists('badge_categories', 'sort_desc');
        $this->db->dropColumnIfExists('badge_categories', 'active');
        $this->db->dropColumnIfExists('badges', 'display_restricted');
        $this->db->dropColumnIfExists('badges', 'long_name');
        $this->db->dropColumnIfExists('badges', 'visible_in_map');
        $this->db->dropColumnIfExists('badges', 'active');

        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_owners`');
        $this->db->simpleQuery('DROP TABLE IF EXISTS `badges_owners_types`');
    }
};

return new C111BadgesOwners;
