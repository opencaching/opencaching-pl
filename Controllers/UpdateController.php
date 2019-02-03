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
        global $oc_nodeid, $sql_errormail, $emailaddr, $debug_page, $dynbasepath;
        global $OKAPI_server_URI, $absolute_server_URI;
        global $dbserver, $dbname, $dbusername, $dbpasswd;
        global $picdir, $picurl;
        global $config;

        // See explanation in /okapi_settings.php.

        $settings = [
            'ADMINS' => ($config['okapi']['admin_emails'] ? $config['okapi']['admin_emails'] : array($sql_errormail, 'rygielski@mimuw.edu.pl', 'following@online.de')),
            'DATA_LICENSE_URL' => $config['okapi']['data_license_url'],
            'FROM_FIELD' => $emailaddr,
            'SITELANG' => I18n::getDefaultLang(),
            'SITE_URL' => isset($OKAPI_server_URI) ? $OKAPI_server_URI : $absolute_server_URI,
            'IMAGES_DIR' => rtrim($picdir, '/'),
            'IMAGES_URL' => rtrim($picurl, '/').'/',
            'IMAGE_MAX_UPLOAD_SIZE' => (int) $config['limits']['image']['filesize'] * 1024 * 1024,
            'IMAGE_MAX_PIXEL_COUNT' => $config['limits']['image']['height'] * $config['limits']['image']['width'],
            'OC_NODE_ID' => $oc_nodeid,
            'OC_COOKIE_NAME' => $config['cookie']['name'].'_auth',
            //'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => isset($enable_cache_access_logs) ? $enable_cache_access_logs : false
            'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => false,
            'VAR_DIR' => rtrim($dynbasepath, '/'),
            'TILEMAP_FONT_PATH' => $config['okapi']['tilemap_font_path'],
        ];
        $json = json_encode($settings);

        file_put_contents(__DIR__.'/../var/okapi_settings.json', $json);
    }
}
