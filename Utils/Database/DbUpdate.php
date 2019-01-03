<?php
namespace Utils\Database;

use Exception;
use Utils\Generators\Uuid;
use Utils\Database\Updates\UpdateScript;

/**
 * This class represents one database update. It contains all data from the
 * update's PHP script, and information about the deployment status from local
 * Git repo.
 */

class DbUpdate
{
    const REGEX_VALID_UPDATE_NAME = '\d{3}_[A-Za-z0-9_]{3,56}';

    /** @var array dictionary name => UpdateScript obj, cache of update scripts */
    private static $scripts = [];

    /** @var OcDb */
    private $db;

    /** The properties of the update */
    private $uuid;
    private $name;
    private $script;
    private $runtype;

    // Constructor

    public function __construct($name)
    {
        $this->db = OcDb::instance();
        $this->name = $name;

        # To avoid class redeclaration errors (also with anonymous classes!),
        # we include each update script only once and cache the returned object
        # in self::$scripts.
        #
        # This is compatible with script renaming, as PHP bases the anonymous
        # class names on script file names. (And anyway, the $scripts cache
        # is only temporary for one request.)

        # It takes less than a second to load 1000 typical update scripts from
        # disk (uncached). So preloading ALL scripts is fine.

        if (!isset(self::$scripts[$name])) {
            self::$scripts[$name] = include $this->getFilePath();
        }
        $this->script = self::$scripts[$name];
        $props = $this->script->getProperties();

        if (!Uuid::isValidUpperCaseUuid($props['uuid'])) {
            throw new Exception('Invalid uuid in ' . $this->getFileName());
        }
        $this->uuid = $props['uuid'];

        if (!in_array($props['run'], ['manual', 'auto', 'always'])) {
            throw new Exception("Invalid run setting '".$props['run']."' in " . $this->getFileName());
        }
        $this->runtype = $props['run'];
    }

    // Getters

    public function getUuid()     { return $this->uuid; }
    public function getName()     { return $this->name; }
    public function getFileName() { return $this->name . '.php'; }
    public function getFilePath() { return DbUpdates::getUpdatesDir() . '/' . $this->getFileName(); }
    public function getRuntype()  { return $this->runtype; }
    public function hasRollback() { return method_exists($this->script, 'rollback'); }

    public function isInGitMasterBranch()
    {
        static $scriptsInMaster = null;

        if ($scriptsInMaster === null) {
            exec(
                "git ls-tree master --name-only -r ".DbUpdates::getUpdatesDir(),
                $scriptsInMaster
            );
            foreach ($scriptsInMaster as &$script) {
                $script = pathinfo($script)['filename'];
            }
        }
        return in_array($this->name, $scriptsInMaster);
    }

    public function wasRunAt()
    {
        return DbUpdateHistory::wasRunAt($this->uuid);
    }

    public function shouldRun()
    {
        if ($this->getRuntype() == 'auto' && !$this->wasRunAt()) {
            return true;
        }
        if ($this->getRuntype() == 'always') {
            return true;
        }
        return false;
    }

    public function getScriptContents()
    {
        return file_get_contents($this->getFilePath());
    }

    // Actions

    /**
     * @return string diagnostic messages or ''; see execute()
     */
    public function run()
    {
        return $this->execute('run');
    }

    /**
     * @return string diagnostic messages or ''; see execute()
     */
    public function rollback()
    {
        return $this->execute('rollback');
    }

    # The following methods do only formal sanity and consistency checks.
    # It is up to the calling controller (or to the user) to decide if an
    # action makes *sense*, with respect to the development and deployment
    # workflow.

    /**
     * @return string
     *    multiline English plain text, diagnostic notices, should be displayed
     *    to the operator / developer if the update was run manually.
     *    '' if nothing was run.
     */
    private function execute($action)
    {
        if (!method_exists($this->script, $action)) {
            throw new Exception("missing method '".$action."' in" . $this->getFileName());
        }

        $oldTimeLimit = ini_get('max_execution_time');
        set_time_limit(1800);  // allow 30 minutes for expensive updates

        ob_start();
        echo $action . " " . $this->name . "\n";

        $this->db->beginTransaction();
        $this->script->$action();

        if ($action == 'run') {
            DbUpdateHistory::addOrReplace($this->uuid, $this->name);
        } elseif ($action == 'rollback') {
            DbUpdateHistory::remove($this->uuid);
        }

        $this->db->commit();

        set_time_limit($oldTimeLimit);

        // PHP notices/warnings output is HTML; we return plaintext
        return strip_tags(ob_get_clean());
    }

    /**
     * @return bool success or failure
     */
    public function rename($newName)
    {
        $oldPath = $this->getFilePath();
        $newPath = dirname($oldPath) . '/' . $newName.'.php';
        if (file_exists($newPath)) {
            return false;
        }

        if (preg_match('/^'.self::REGEX_VALID_UPDATE_NAME.'$/', $newName)) {

            // try to move via git
            exec('git mv '.$oldPath.' '.$newPath);

            if (file_exists($oldPath)) {
                // Looks like the file was not staged/committed to Git yet.
                rename($oldPath, $newPath);
            }
            if (!file_exists($oldPath)) {
                $this->name = $newName;
                if (DbUpdateHistory::contains($this->uuid)) {
                    DbUpdateHistory::rename($this->uuid, $newName);
                }
                DbUpdates::sort();
                return true;
            }
        }

        return false;
    }
}
