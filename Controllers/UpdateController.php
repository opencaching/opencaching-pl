<?php

namespace Controllers;

use Utils\Database\DbUpdates;
use Utils\Lock\Lock;
use Utils\I18n\I18n;
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
        $this->updateOkapiSettings();
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
            throw new Exception('Database update is already running, or problem with lock file.');
        }

        try {
            $messages = '';
            foreach (DbUpdates::getAll() as $uuid => $update) {
                if ($update->shouldRun()) {
                    $messages .= $update->run();
                }
            }
            if ($messages == '') {
                $messages = "no updates to run\n";
            }
        } finally {
            Lock::unlock($lockHandle);
        }

        return $messages;
    }

    /**
     * Create settings file for OKAPI
     */
    private function updateOkapiSettings()
    {
        // See explanation in /okapi_settings.php.

        $settings = [
            'OC_NODE_ID' => $this->ocConfig->getOcNodeId(),
            'SITELANG' => I18n::getDefaultLang(),
            'VAR_DIR' => $this->ocConfig->getDynamicFilesPath(),
            //'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => $this->ocConfig->isCacheAccesLogEnabled(),
            'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => false,
        ];

        $code = "<?php\n\nreturn " . var_export($settings, true) . ";\n";

        file_put_contents(__DIR__.'/../var/okapi_settings.inc.php', $code);
    }
}
