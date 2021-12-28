<?php
namespace src\Utils\Database;

use Exception;
use okapi\Facade;
use src\Utils\Generators\Uuid;

/**
 * Static container of DbUpdate objects, each representing one of the scripts
 * in the Updates directory, + some DB updating logic.
 */

class DbUpdates
{
    /**
     * @var array|null $updates dict $uuid => DbUpdate object;
     *    ordered ascending by filename; read access only via get() / getAll()
     */
    private static ?array $updates = null;

    /**
     * @return string
     *    next unused version number (first part of filename) for a new
     *    update script to be created; number 900+ is reserved for
     *    updates that are run always and last
     * @throws Exception
     */
    private static function getNextVersionNumberString(): string
    {
        $lastRegularUpdate = '';
        foreach (self::getAll() as $update) {
            if ($update->getName() < '900_') {
                $lastRegularUpdate = $update->getName();
            }
            // Numbers 900+ are reserved for always-run-last updates.
        }
        return sprintf('%03d', substr($lastRegularUpdate, 0, 3) + 1);
    }

    /**
     * Creates a new update script from Template.php, with preliminary name.
     * @throws Exception
     */
    public static function create($developerName): string
    {
        $developerName = str_replace(['"', "'", "\\"], '', $developerName);
        $creationDate = date('Y-m-d');
        $uuid = Uuid::create();
        $className = 'C' . str_replace('.', '', microtime(true));

        $updatesDir = self::getUpdatesDir();
        $template = file_get_contents($updatesDir . '/template.php');
        $template = str_replace('{creation_date}', $creationDate, $template);
        $template = str_replace('{developer_name}', $developerName, $template);
        $template = str_replace('{update_uuid}', $uuid, $template);
        $template = str_replace('{class_name}', $className, $template);

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
     * @throws Exception
     */
    public function delete($uuid): bool
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
     * @throws Exception
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
     * @throws Exception
     */
    public static function getAll(): ?array
    {
        if (self::$updates === null) {
            self::makeUpdatesDict();
        }
        return self::$updates;
    }

    /**
     * @throws Exception
     */
    private static function makeUpdatesDict()
    {
        self::$updates = [];

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
    public static function sort()
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
    public static function getUpdatesDir(): string
    {
        return __DIR__ . '/Updates';
    }


    /**
     * Update DB triggers, procedures and functions
     * @throws Exception
     */
     public static function updateRoutines(): string
     {
        $routines = self::getRoutineFileNames();
        $messages = '';

        foreach ($routines as $fileName => $lastRun) {
            if (!$lastRun ||
                $lastRun['fileTime'] != self::routineFileModified($fileName)
            ) {
                $messages .= self::runRoutines($fileName);
            }
        }
        return $messages;
    }

    /**
     * @throws Exception
     */
    public static function runRoutines($fileName): string
    {
        $queries = self::getRoutineContents($fileName);
        if (substr($queries, 0, 10) != 'DELIMITER ') {
            throw new Exception('DELIMITER statement is missing in '.$fileName);
        }
        OcDb::instance(OcDb::ADMIN_ACCESS)->simpleQueries($queries);

        Facade::cache_set(
            'run '.$fileName,
            ['fileTime' => self::routineFileModified($fileName), 'runTime' => time()],
            365 * 24 * 3600
              // At least once a year, all routines are re-installed.
        );
        return 'run '.$fileName."\n";
    }

    /**
     * @return array dictionary: .sql file path or name => file time and last run time
     */
    public static function getRoutineFileNames(): array
    {
        $routineFilePaths = glob(self::getRoutinesPath('*.sql'));
        $routines = [];

        foreach ($routineFilePaths as $filePath) {
            $fileName = basename($filePath);
            $routines[$fileName] = Facade::cache_get('run '.$fileName);
        }
        return $routines;
    }

    public static function getRoutineContents($fileName)
    {
        return file_get_contents(self::getRoutinesPath($fileName));
    }

    private static function routineFileModified($fileName)
    {
        return filemtime(self::getRoutinesPath($fileName));
    }

    private static function getRoutinesPath($fileName): string
    {
        return __DIR__ . '/../../../resources/sql/routines/' . $fileName;
    }
}
