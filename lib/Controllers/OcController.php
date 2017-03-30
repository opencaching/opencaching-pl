<?php

namespace lib\Controllers;

use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;


require_once __DIR__ . '/../ClassPathDictionary.php';

/**
 * Description of OcController
 *
 * @author Łza
 */
class OcController
{
    private $request;

    public function run($request)
    {
        $this->request = $request;

        switch ($request['action']) {
            default:
                break;
        }
    }

    public function removeLog($request)
    {
        $logId = 0;
        if (isset($request['logid'])) {
            $logId = intval($request['logid']);
        }

        $logEnteryController = new LogEnteryController();
        $result = $logEnteryController->removeLogById($logId);

        return array (
            'removeLogResult' => $result,
            'errors' => $logEnteryController->getErrors(),
        );
    }
}
