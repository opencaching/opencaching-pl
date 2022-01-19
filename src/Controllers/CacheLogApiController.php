<?php

namespace src\Controllers;

use Exception;
use src\Controllers\Core\ApiBaseController;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Uri\HttpCode;

class CacheLogApiController extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->checkUserLoggedAjax();
    }

    /**
     * Remove cache log
     * Called via AJAX like /CacheLogApi/removeLog/{logId}
     *
     * @param int $logId
     */
    public function removeLog($logId)
    {
        $log = $this->getLogFromLogId($logId);

        try {
            $log->removeLog();
        } catch (Exception $ex) {
            $this->ajaxErrorResponse('Can\'t remove log', HttpCode::STATUS_BAD_REQUEST);
        }

        $this->ajaxSuccessResponse();
    }

    /**
     * Reverts (undelete) cache log
     * Called via AJAX like /CacheLogApi/revertLog/{logId}
     *
     * @param int $logId
     */
    public function revertLog($logId)
    {
        $log = $this->getLogFromLogId($logId);

        try {
            $log->revertLog();
        } catch (Exception $ex) {
            $this->ajaxErrorResponse('Can\'t revert log', HttpCode::STATUS_BAD_REQUEST);
        }

        $this->ajaxSuccessResponse();
    }

    private function getLogFromLogId($logId): GeoCacheLog
    {
        if (! $logId || ! is_numeric($logId)) {
            $this->ajaxErrorResponse('Improper logId', HttpCode::STATUS_BAD_REQUEST);
        }

        $log = GeoCacheLog::fromLogIdFactory($logId);

        if (! $log) {
            $this->ajaxErrorResponse('Incorrect logId', HttpCode::STATUS_BAD_REQUEST);
        }

        return $log;
    }
}
