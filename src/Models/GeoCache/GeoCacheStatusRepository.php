<?php

namespace src\Models\GeoCache;

use src\Utils\Database\OcDb;

class GeoCacheStatusRepository
{
    private const AVAILABLE_LANGUAGE_COLUMNS = ['pl', 'en', 'nl', 'de', 'fr', 'ro'];

    /** @var OcDb */
    private $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?: OcDb::instance();
    }

    /**
     * Load cache status labels from the selected language column.
     * Each row contains `id` and `status_name`.
     */
    public function findAll(string $language): array
    {
        $language = strtolower($language);
        if (! in_array($language, self::AVAILABLE_LANGUAGE_COLUMNS, true)) {
            $language = 'pl';
        }

        $statement = $this->db->simpleQuery(
            'SELECT `id`, `' . $language . '` AS `status_name`
             FROM `cache_status`
             ORDER BY `id`'
        );

        return $this->db->dbResultFetchAll($statement);
    }
}
