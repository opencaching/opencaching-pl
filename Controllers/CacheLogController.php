<?php
namespace Controllers;


use lib\Controllers\LogEntryController;

class CacheLogController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //there is nothing to do here yet...
    }

    public function removeLog()
    {
        if(!$this->loggedUser){
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

}


