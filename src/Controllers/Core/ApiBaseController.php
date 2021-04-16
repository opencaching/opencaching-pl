<?php
namespace src\Controllers\Core;

/**
 * Controller base class to use as a parent of API controllers
 * (API controller means the controller contains only public methods called by AJAX)
 */
abstract class ApiBaseController extends CoreController
{
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_STATUS_FORBIDDEN = 403;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_CONFLICT = 409;
    const HTTP_STATUS_INTERNAL_ERROR = 500;

    /**
     * Every ctrl should have index method
     * which is called by router as a default action
     */
    public function index()
    {
        // API requests must be strict and method mus be present
        $this->ajaxErrorResponse('Bad request', self::HTTP_STATUS_BAD_REQUEST);
        exit();
    }

    /**
     * This method is called by router to be sure that given action is allowed
     * to be called by router (it is possible that ctrl has public method which
     * shouldn't be accessible on request).
     *
     * @param string $actionName
     *            - method which router will call
     * @return boolean - TRUE if given method can be call from router
     */
    public function isCallableFromRouter($actionName)
    {
        // for API requests it's always TRUE
        return TRUE;
    }

    protected function ajaxJsonResponse($response, $statusCode = null): void
    {
        if (is_null($statusCode)) {
            $statusCode = self::HTTP_STATUS_OK;
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        print(json_encode($response));
        exit();
    }

    protected function ajaxSuccessResponse($message = null, array $additionalResponseData = null): void
    {
        $response = [
            'status' => 'OK'
        ];

        if (! is_null($message)) {
            $response['message'] = $message;
        }

        if (is_array($additionalResponseData)) {
            $response = array_merge($additionalResponseData, $response);
        }

        $this->ajaxJsonResponse($response);
    }

    protected function ajaxErrorResponse($message = null, $statusCode = null, array $additionalResponseData = null): void
    {
        $response = [
            'status' => 'ERROR'
        ];
        if (! is_null($message)) {
            $response['message'] = $message;
        }
        if (is_null($statusCode)) {
            $statusCode = self::HTTP_STATUS_BAD_REQUEST;
        }
        if (is_array($additionalResponseData)) {
            $response = array_merge($additionalResponseData, $response);
        }
        $this->ajaxJsonResponse($response, $statusCode);
    }

    /**
     * Check if user is logged.
     * If not - generates 401 (unauthorized) AJAX response
     */
    protected function checkUserLoggedAjax(): void
    {
        if (! $this->isUserLogged()) {
            $this->ajaxErrorResponse('User not logged', self::HTTP_UNAUTHORIZED);
            exit();
        }
    }
}
