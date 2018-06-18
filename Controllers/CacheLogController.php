<?php
namespace Controllers;

use lib\Controllers\LogEntryController;
use lib\Objects\GeoCache\GeoCacheLog;
use Utils\EventHandler\EventHandler;

class CacheLogController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public method can be called by router
        return true;
    }

    public function index()
    {
        // there is nothing to do here yet...
    }

    public function removeLog()
    {
        if (! $this->loggedUser) {
            echo "User not authorized!";
            return;
        }

        if (!isset($_REQUEST['logid'])) {
            echo "Remove unknown log?!";
            return;
        }

        $logId = intval($_REQUEST['logid']);

        $logEntryController = new LogEntryController();
        $result = $logEntryController->removeLogById($logId);

        echo json_encode( array (
            'removeLogResult' => $result,
            'errors' => $logEntryController->getErrors())
            );

    }

    /**
     * Reverts (undelete) cache log
     * Called via AJAX like /CacheLog/revertLogAjax/{logid}
     *
     * @param int $logId
     */
    public function revertLogAjax($logId)
    {
        $this->checkUserLoggedAjax();
        $log = GeoCacheLog::fromLogIdFactory($logId);
        if (is_null($log)) {
            $this->ajaxErrorResponse('Incorrect logId', 400);
        }
        if (! $this->loggedUser->isAdmin()) {
            $this->ajaxErrorResponse('User is not authorized to revert log', 401);
        }
        if (! $log->canBeReverted()) {
            $this->ajaxErrorResponse('This log cannot be reverted', 400);
        }
        $log->revertLog();
        $log->getGeoCache()->recalculateCacheStats();
        $log->getUser()->recalculateAndUpdateStats();
        EventHandler::logRemove($log);
        $this->ajaxSuccessResponse('OK');
    }

}