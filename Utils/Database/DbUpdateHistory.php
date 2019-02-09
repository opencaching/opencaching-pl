<?php
namespace Utils\Database;

use Exception;
use src\Models\OcConfig\OcConfig;
use Utils\Database\OcDb;
use Utils\Generators\Uuid;

/**
 * Static container of the list of all DB updates which have been deployed
 * to the site. For high robustness, this (critical!) list is maintained
 * redundantly at two places:
 *
 *   - in the database table db_update_history
 *   - in the directory $dynbasepath/db_update_history
 *
 * Both are loaded and synchronized with the first call to a DbUpdateHistory
 * method. If an entry is missing in one of both (which means that data was
 * corrupted), it is added.
 *
 * Note that DB updates are identified only by their UUID. The *name*
 * of the updates is included in the history only for diagnostic purposes
 * and to enable future optimizations. It may be outdated, if an update
 * file was renamed manually. Do not use it as indentifier.
 *
 * This class is used only by DbUpdate and DbUpdates classes.
 */

class DbUpdateHistory
{
    /**
     * @var array|null dictionary UUID => ['wasRunAt' => ..., 'name' => ...]
     */
    private static $history = null;

    /**
     * @var string directory path for the update history, including trailing '/'
     */
    private static $path = null;

    public static function addOrReplace($uuid, $name)
    {
        self::init();
        self::$history[$uuid] = ['name' => $name, 'wasRunAt' => date('Y-m-d H:m:s')];

        // Write to database first, as we should be inside a transaction.
        // If the DB operation fails, the script is aborted and everything
        // is cleanly rolled back - file is not created.

        self::writeToDb($uuid);
        self::writeToFile($uuid);
    }

    public static function remove($uuid)
    {
        self::init();
        self::verifyUuid($uuid);

        // Remove file first, as we should be inside a transaction. If the file
        // operation fails, the script is aborted and everything is cleanly
        // rolled back - database entry is not deleted. If the file is deleted
        // and the DB operation fails, the file will be restored by
        // DbUpdates::makeUpdatesDict().

        unlink(self::$path . $uuid);
        OcDb::instance()->multiVariableQuery(
            "DELETE FROM db_update_history WHERE uuid = :1",
            $uuid
        );
        unset(self::$history[$uuid]);
    }

    /**
     * @return string|null date/time when the update's run() method was last
     *                     called, or null if not run or rolled back.
     */
    public static function wasRunAt($uuid)
    {
        self::init();
        if (isset(self::$history[$uuid]['wasRunAt'])) {
            return self::$history[$uuid]['wasRunAt'];
        }
        return null;
    }

    public static function contains($uuid)
    {
        self::init();
        return isset(self::$history[$uuid]);
    }

    public static function getName($uuid)
    {
        self::init();
        self::verifyUuid($uuid);
        return self::$history[$uuid]['name'];
    }

    public static function rename($uuid, $newName)
    {
        self::init();
        self::verifyUuid($uuid);
        if ($newName != self::$history[$uuid]['name']) {

            self::$history[$uuid]['name'] = $newName;

            # If at least one of the following operations fails, we get a teporary
            # inconsistency of the name information, as this is called after
            # the file was renamed. But the failure will abort the script, and
            # upon next access to DB updates, DbUpdates::makeUpdatesDict() will
            # fix the inconsistency. The names stored here are not critical anyway,
            # see intro.

            self::writeToDb($uuid);
            self::writeToFile($uuid);
        }
    }

    private static function verifyUuid($uuid)
    {
        if (!isset(self::$history[$uuid])) {
            throw new Exception("Unknown UUID '".$uuid."'");
        }
    }

    private static function writeToDb($uuid)
    {
        OcDb::instance()->multiVariableQuery(
            "INSERT INTO db_update_history (uuid, name, wasRunAt)
            VALUES (:1, :2, :3)
            ON DUPLICATE KEY UPDATE name = :2, wasRunAt = :3",
            $uuid,
            self::$history[$uuid]['name'],
            self::$history[$uuid]['wasRunAt']
        );
    }

    private static function writeToFile($uuid)
    {
        file_put_contents(
            self::$path . $uuid,
            self::$history[$uuid]['wasRunAt'] . ' ' . self::$history[$uuid]['name']
        );
    }

    private static function init()
    {
        static $initialized = false;

        if (!$initialized) {
            $db = OcDb::instance();

            // create history storages, if not exist

            $db->createTableIfNotExists(
                'db_update_history', [
                    'uuid varchar(36) NOT NULL',
                    "name varchar(60) NOT NULL COMMENT 'redundant information / cache'",
                    'wasRunAt datetime NOT NULL'
                ],
                ['engine' => 'InnoDB']
            );
            $db->addPrimaryKeyIfNotExists('db_update_history', 'uuid');

            self::$path = OcConfig::instance()->getDynFilesPath() . 'db_update_history';
            if (!file_exists(self::$path)) {
                mkdir(self::$path);
            }
            self::$path .= '/';

            // load both history instances

            self::$history = $db->dbResultFetchAllAsDict(
                $db->simpleQuery(
                    "SELECT uuid, name, wasRunAt FROM db_update_history"
                )
            );
            $fileHistory = glob(self::$path . Uuid::getMask());

            // Restore consistency between the redundant history storages in the
            // (unlikely) case that one of both got corrupted; see intro.

            // add missing entries to DB history
            foreach ($fileHistory as $filePath) {
                $uuid = basename($filePath);
                if (!isset(self::$history[$uuid])) {
                    $fileData = file_get_contents($filePath);
                    list($date, $time, $name) = explode(' ', $fileData);
                    self::$history[$uuid] = ['name' => $name, 'wasRunAt' => $date.' '.$time];
                    self::writeToDb($uuid);
                }
            }

            // add missing entries to file history
            foreach (self::$history as $uuid => $props) {
                if (!in_array(self::$path . $uuid, $fileHistory)) {
                    self::writeToFile($uuid);
                }
            }

            $initialized = true;
        }
    }

}
