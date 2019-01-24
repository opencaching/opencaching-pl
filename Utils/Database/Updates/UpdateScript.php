<?php

namespace Utils\Database\Updates;

use Exception;
use Utils\Database\OcDb;
use Utils\I18n\I18n;

/**
 * Base class for all database updates
 */

class UpdateScript
{
    /** @var OcDb $db */
    protected $db;

    protected $simulate = false;  // true => don't update translations, but show what would be changed

    public function __construct()
    {
        $this->db = OcDb::instance(OcDb::ADMIN_ACCESS);
    }

    # Do NOT declare rollback() here! The update system would no longer
    # detect if an update script does not implement rollback.

    /**
     * Copy all translations for a language from <language>.php to DB tables;
     * create language columns if missing.
     *
     * TODO: Handle translations in cache_atttrib.
     */
    public function updateTranslations($lang)
    {
        foreach (I18n::getTranslationTables() as $table) {

            $existingLangs = $this->db->dbFetchOneColumnArray(
                $this->db->simpleQuery(
                    "SHOW COLUMNS FROM `".$table."` WHERE field LIKE '__'"
                ),
                'field',
                false
            );
            $after = end($existingLangs);
            unset($existingLangs);

            $this->db->addColumnIfNotExists(
                $table,
                $lang,
                'varchar('.($table == 'countries' ? 128 : 60).') not null',
                '',
                $after
            );
            $idColumn = I18n::getTranslationIdColumnName($table);

            $ids = $this->db->dbFetchOneColumnArray(
                $this->db->simpleQuery(
                    "SELECT `".$idColumn."` FROM `".$table."`"
                ),
                $idColumn
            );
            foreach ($ids as $id) {
                $key = I18n::getTranslationKey($table, $id);

                if ($translation = I18n::translatePhrase($key, $lang, null, true)) {

                    $oldText = $this->db->multiVariableQueryValue(
                        "SELECT `".$lang."` FROM `".$table."`
                        WHERE `".$idColumn."` = :1",
                        '',
                        $id
                    );
                    if ($translation != $oldText && ($oldText == '' || $table != 'log_types')) {
                        // TODO: log_types; see issue #1794

                        if ($this->simulate) {
                            echo $table.".".$lang.".".$id.": ".$oldText . " => ".$translation."\n";
                        } else {
                            $this->db->multiVariableQuery(
                                "UPDATE `".$table."` SET `".$lang."` = :1
                                WHERE `".$idColumn."` = :2",
                                $translation,
                                $id
                            );
                        }
                    }
                }
            }

            // Special handling for language and countries tables
            // TODO: Move all this data to the settings or static initialization files.

            // There are no global defaults for this; we simply initialize it from PL.
            if ($this->db->columnExists($table, 'list_default_pl') &&
                !$this->db->columnExists($table, 'list_default_'.$lang)
            ) {
                $this->db->addColumnIfNotExists($table, 'list_default_'.$lang, 'int(1) NOT NULL DEFAULT 0');
                $this->db->simpleQuery(
                    "UPDATE `".$table."` SET `list_default_".$lang."` = list_default_pl"
                );
            }

            if ($this->db->columnExists($table, 'sort_pl')) {
                $this->db->addColumnIfNotExists($table, 'sort_'.$lang, 'varchar(128) NOT NULL NOT NULL');
                $this->db->simpleQuery(
                    "UPDATE `".$table."` SET `sort_".$lang."` = `".$lang."`
                    WHERE `sort_".$lang."` = ''"
                );
            }
        }
    }

    /** This is (only) for test-updates, see e.g. 101_test_OcDb. */
    protected static function startTest()
    {
        ob_start();
    }

    /** This is (only) for test-updates, see e.g. 101_test_OcDb. */
    protected static function finishTest()
    {
        $warnings = ob_get_clean();
        if ($warnings) {
            echo $warnings;
        } else {
            echo "Ok\n";
        }
    }
}
