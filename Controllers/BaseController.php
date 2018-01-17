<?php

namespace Controllers;

use lib\Objects\ApplicationContainer;
use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;
use Utils\View\View;
use Utils\Uri\Uri;

require_once(__DIR__.'/../lib/common.inc.php');

abstract class BaseController
{
    /**
     * Every ctrl should have index method
     * which is called by router as a default action
     */
    abstract public function index();

    /**
     * This method is called by router to be sure that given action is allowed
     * to be called by router (it is possible that ctrl has public method which
     * shouldn't be accessible on request).
     *
     * @param string $actionName - method which router will call
     * @return boolean - TRUE if given method can be call from router
     */
    abstract public function isCallableFromRouter($actionName);

/** @var View $view */
    protected $view = null;

    /** @var ApplicationContainer $applicationContainer */
    private $applicationContainer = null;

    /** @var User */
    protected $loggedUser = null;

    /** @var OcConfig $ocConfig */
    protected $ocConfig = null;

    protected function __construct()
    {
        $this->view = tpl_getView();

        $this->applicationContainer = ApplicationContainer::Instance();
        $this->loggedUser = $this->applicationContainer->getLoggedUser();
        $this->ocConfig = $this->applicationContainer->getOcConfig();

        // there is no DB access init - DB operations should be performed in models/objects
    }


    protected function redirectToLoginPage()
    {
        $this->view->redirect(
            Uri::setOrReplaceParamValue('target', Uri::getCurrentUri(), '/login.php'));
        exit;
    }

    protected function isUserLogged(){
        return !is_null($this->loggedUser);
    }

    protected function ajaxSuccessResponse($message=null){
        $response = [
            'status' => 'OK'
        ];

        if(!is_null($message)){
            $response['message'] = $message;
        }

        header('Content-Type: application/json');
        print (json_encode($response));

        exit;
    }

    protected function ajaxErrorResponse($message=null, $statusCode=null){
        $response = [
            'status' => 'ERROR'
        ];

        if(!is_null($message)){
            $response['message'] = $message;
        }

        if(is_null($statusCode)){
            $statusCode = 500;
        }

        http_response_code($statusCode);

        header('Content-Type: application/json; charset=UTF-8');
        print json_encode(
                array(
                'message' => $message,
                'status' => 'ERROR')
        );

        exit;
    }

    /**
     * This method can be used to just exit and display error page to user
     *
     * @param unknown $message - simple message to be displayed (in english)
     * @param unknown $httpStatusCode - http status code to return in response
     */
    public function displayCommonErrorPageAndExit($message=null, $httpStatusCode=null)
    {
        $this->view->setTemplate('error/commonFatalError');
        if($httpStatusCode){
            switch($httpStatusCode){
                case 404:
                    header("HTTP/1.0 404 Not Found");
                    break;
                case 403:
                    header("HTTP/1.0 403 Forbidden");
                    break;
                default:
                    //TODO...
            }
        }

        $this->view->setVar('message', $message);
        $this->view->buildOnlySelectedTpl();
        exit;
    }
}
