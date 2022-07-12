<?php

namespace src\Controllers;

use src\Controllers\Core\CoreController;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\Uri;
use src\Utils\View\View;

/**
 * @deprecated User ApiBaseController or ViewBaseController instead in any new code
 */
abstract class BaseController extends CoreController
{
    protected View $view;

    public function __construct()
    {
        parent::__construct();
        $this->view = tpl_getView();

        // there is no DB access init - DB operations should be performed in models/objects
    }

    protected function redirectToLoginPage()
    {
        $this->view->redirect(
            Uri::setOrReplaceParamValue('target', Uri::getCurrentUri(), '/login.php')
        );
    }

    protected function ajaxJsonResponse($response, $statusCode = null)
    {
        if (is_null($statusCode)) {
            $statusCode = HttpCode::STATUS_OK;
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response);

        exit;
    }

    protected function ajaxSuccessResponse($message = null, array $additionalResponseData = null)
    {
        $response = [
            'status' => 'OK',
        ];

        if (! is_null($message)) {
            $response['message'] = $message;
        }

        if (is_array($additionalResponseData)) {
            $response = array_merge($additionalResponseData, $response);
        }

        $this->ajaxJsonResponse($response);
    }

    protected function ajaxErrorResponse($message = null, $statusCode = null, array $additionalResponseData = null)
    {
        $response = [
            'status' => 'ERROR',
        ];

        if (! is_null($message)) {
            $response['message'] = $message;
        }

        if (is_null($statusCode)) {
            $statusCode = HttpCode::STATUS_BAD_REQUEST;
        }

        if (is_array($additionalResponseData)) {
            $response = array_merge($additionalResponseData, $response);
        }
        $this->ajaxJsonResponse($response, $statusCode);
    }

    /**
     * This method can be used to just exit and display error page to user
     *
     * @param string|null $message - simple message to be displayed (in english)
     * @param int|null $httpStatusCode - http status code to return in response
     */
    public function displayCommonErrorPageAndExit(string $message = null, int $httpStatusCode = null)
    {
        $this->view->setTemplate('error/commonFatalError');

        if ($httpStatusCode) {
            switch ($httpStatusCode) {
                case HttpCode::STATUS_NOT_FOUND:
                    header('HTTP/1.0 404 Not Found');
                    break;
                case HttpCode::STATUS_FORBIDDEN:
                    header('HTTP/1.0 403 Forbidden');
                    break;
                default:
                    //TODO...
            }
        }

        $this->view->setVar('message', $message);
        $this->view->buildOnlySelectedTpl();

        exit();
    }

    /**
     * Simple redirect not logged users to login page
     */
    protected function redirectNotLoggedUsers()
    {
        if (! $this->isUserLogged()) {
            $this->redirectToLoginPage();
        }
    }

    /**
     * Check if user is logged. If not - generates 401 AJAX response
     */
    protected function checkUserLoggedAjax()
    {
        if (! $this->isUserLogged()) {
            $this->ajaxErrorResponse('User not logged', HttpCode::STATUS_UNAUTHORIZED);
        }
    }
}
