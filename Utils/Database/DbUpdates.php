<?php
namespace Utils\Database;

use Exception;
use Utils\Generators\Uuid;

/**
 * Static container of DbUpdate objects, each representing one of the scripts
 * in the Updates directory.
 */

class DbUpdates
{
    /**
     * @var array|null $updates dict $uuid => DbUpdate object;
     *    ordered ascending by filename; read access only via get() / getAll()
     */
    private static $updates = null;

    /**
     * @return string
     *    next unused version number (first part of filename) for a new
     *    update script to be created; number 900+ is reserved for
     *    updates that are run always and last
     */
    private static function getNextVersionNumberString()
    {
        foreach (self::getAll() as $uuid => $update) {
            if ($update->getName() < '900_') {
                $lastRegularUpdate = $update->getName();
            }
            // Numbers 900+ are reserved for always-run-last updates.
        }
        return sprintf('%03d', substr($lastRegularUpdate, 0, 3) + 1);
    }

    /**
     * Creates a new update script from Template.php, with preliminary name.
     * @return DbUpdate
     */
    public static function create($developerName)
    {
        $developerName = str_replace(['"', "'", "\\"], '', $developerName);
        $creationDate = date('Y-m-d');
        $uuid = Uuid::create();

        $updatesDir = self::getUpdatesDir();
        $template = file_get_contents($updatesDir . '/template.php');
        $template = str_replace('{creation_date}', $creationDate, $template);
        $template = str_replace('{developer_name}', $developerName, $template);
        $template = str_replace('{update_uuid}', $uuid, $template);

        $name = self::getNextVersionNumberString() . '_new';
        $path = $updatesDir . '/' . $name . '.php';
        file_put_contents($path, $template);

        if (self::$updates !== null) {
            // This also tests if template.php is healthy.
            self::$updates[$uuid] = new DbUpdate($name);
            self::sort();
        }

        return $uuid;
    }

    /**
     * @return bool success or failure
     */
    public function delete($uuid)
    {
        $update = self::get($uuid);
        if (!$update) {
            return true;
        }
        $path = $update->getFilePath();

        // try to delete via git
        exec('git rm -f ' . $path);

        if (file_exists($path)) {
            // Looks like the file was not staged/committed to Git yet.
            unlink($path);
        }
        if (!file_exists($path)) {
            if (self::$updates !== null) {
                unset(self::$updates[$uuid]);
            }

            # No DbUpdateHistory::remove()! Either it was not run yet (or
            # rolled back), then it is not in the history. Or it was run
            # (and not rolled back); then we KEEP it in the history, because
            # it still IS deployed. This is a safeguard against accidentally
            # deleting and then re-adding an update.

            return true;
        }
        return false;
    }

    /**
     * @returns DbUpdate|null
     */
    public static function get($uuid)
    {
        $updates = self::getAll();
        if (isset($updates[$uuid])) {
            return $updates[$uuid];
        }
        return null;
    }

    /**
     * @returns array dictionary $uuid => DbUpdate object of all available updates
     */
    public static function getAll()
    {
        if (self::$updates === null) {
            self::makeUpdatesDict();
        }
        return self::$updates;
    }

    private static function makeUpdatesDict()
    {
        self::$updates = [];

        $db = OcDb::instance();

        // Get list of available update scripts
        $scriptPaths = glob(self::getUpdatesDir() . '/*.php');

        foreach ($scriptPaths as $scriptPath) {
            if (preg_match(
                '~/('.DbUpdate::REGEX_VALID_UPDATE_NAME.')\.php$~',
                $scriptPath,
                $matches
            )) {
                $name = $matches[1];
                $update = new DbUpdate($name);
                $uuid = $update->getUuid();

                // copy & paste protection
                if (isset(self::$updates[$uuid])) {
                    throw new Exception(
                        'Duplicate UUID in ' . self::$updates[$uuid]->getFileName() .
                        ' and ' . $name.'.php'
                    );
                }

                self::$updates[$uuid] = $update;

                // update name cache, if the script was renamed manually
                if (DbUpdateHistory::contains($uuid)) {
                    DbUpdateHistory::rename($uuid, $name);
                }

            } elseif (preg_match(
                '~/([0-9][^/]+\.php)$~', $scriptPath, $matches
            )) {
                throw new Exception("Invalid db-update filename: '" . $matches[1] ."'");
            }
        }

        // glob() did already sort alphanumerically, but we want it case-insensitive:
        self::sort();
    }

    /**
     * Sort the updates ascending and case-insensitive by filename
     */
    public function sort()
    {
        if (self::$updates !== null) {
            uasort(self::$updates, function($a, $b) {
                return strcasecmp($a->getFileName(), $b->getFileName());
            });
        }
    }

    /**
     * @return string the directory path where update scripts are located
     */
    public static function getUpdatesDir()
    {
        return __DIR__ . '/Updates';
    }
}
