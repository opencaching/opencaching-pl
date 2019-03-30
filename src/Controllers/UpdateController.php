<?php

namespace src\Controllers;

use src\Utils\Database\DbUpdates;
use src\Utils\Lock\Lock;
use okapi\Facade;

/**
 * This class runs all updates that are necessary after a code deployment.
 */

class UpdateController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // It MUST be safe to run update any time. (Concurrent runs are prevented
        // by locks). Therefore it is also safe to publish this page via router.
        // Note that the OKAPI update view as well as the OCPL post-commit.php
        // have always been public.

        // However, it may not be safe to run only parts of update. Therefore only
        // the full update is runnable by router:

        return ($actionName == 'index');
    }

    /**
     * Run all updates including OKAPI
     */
    public function index()
    {
        ob_start();

        // OC database update
        echo "Run OC database updates\n";
        echo self::runOcDatabaseUpdate();
        echo "\n";

        // OKAPI update
        Facade::database_update();

        $messages = ob_get_clean();
        $this->view->showPlainText($messages);
    }

    /**
     * Run all OC database updates that are ready to run
     *
     * @return string
     *    multiline English plain text, diagnostic notices, should be displayed
     *    to the operator / developer if the update was run manually.
     */
    public static function runOcDatabaseUpdate()
    {
        $lockHandle = Lock::tryLock('DbUpdate', Lock::EXCLUSIVE | Lock::NONBLOCKING);
        if (!$lockHandle) {
            throw new \Exception('Database update is already running, or problem with lock file.');
        }

        try {
            $messages = '';
            foreach (DbUpdates::getAll() as $uuid => $update) {
                if ($update->shouldRun()) {
                    $messages .= $update->run();
                }
            }

            # Routine updates must run AFTER table structure updates, because
            # there may be new or renamed columns. Routine creation fails if
            # a referenced column does not exist!

            $messages .= DbUpdates::updateRoutines();

            if ($messages == '') {
                $messages = "no updates to run\n";
            }
        } finally {
            Lock::unlock($lockHandle);
        }

        return $messages;
    }
}
