<?php
namespace src\Controllers\Core;

use src\Utils\Uri\Uri;
use src\Utils\View\View;

/**
 * Controller base class to use as a parent of VIEW controllers
 * (VIEW controller means the controller which returns the view - HTML code
 *  and is called directly by browser - not by AJAX)
 */
abstract class ViewBaseController extends CoreController
{

    /** @var View $view */
    protected $view = null;

    public function __construct()
    {
        parent::__construct();
        $this->view = tpl_getView();
    }

    protected function redirectToLoginPage(): void
    {
        $this->view->redirect(Uri::setOrReplaceParamValue('target', Uri::getCurrentUri(), '/login.php'));
        exit();
    }

    /**
     * This method can be used to just exit and display error page to user
     *
     * @param string $message
     *            - simple message to be displayed (in english)
     * @param integer $httpStatusCode
     *            - http status code to return in response
     */
    public function displayCommonErrorPageAndExit($message = null, $httpStatusCode = null): void
    {
        $this->view->setTemplate('error/commonFatalError');
        if ($httpStatusCode) {
            switch ($httpStatusCode) {
                case 404:
                    header("HTTP/1.0 404 Not Found");
                    break;
                case 403:
                    header("HTTP/1.0 403 Forbidden");
                    break;
                default:
                // TODO...
            }
        }

        $this->view->setVar('message', $message);
        $this->view->buildOnlySelectedTpl();
        exit();
    }

    /**
     * Simple redirect not logged users to login page
     */
    protected function redirectNotLoggedUsers(): void
    {
        if (! $this->isUserLogged()) {
            $this->redirectToLoginPage();
            exit();
        }
    }
}
