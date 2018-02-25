<?php
namespace Controllers;

use Utils\Debug\Debug;
use Utils\Debug\OcException;
use Exception;
use lib\Controllers\Php7Handler;

/**
 * This controller is for error handling.
 * It should be used to display error pages, log notice to error log etc.
 */
class ErrorController extends BaseController
{
    const COMMON_ERROR_MESSAGE = "Unknow technical dificulties";

    protected function __construct(){
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return false;
    }

    public function index()
    {}

    /**
     * Register exception/error handlers
     * - this should be called as early as possible
     */
    public static function registerErrorHandlers()
    {
        set_exception_handler([self::class, 'commonExceptionHandler']);
        set_error_handler([self::class, 'commonErrorHandler']);
    }

    /**
     * this is default error handler for PHP
     * (called by PHP - registered in function: registerErrorHandlers())
     */
    public static function commonErrorHandler($errno, $errstr, $errfile, $errline){

        Debug::errorLog($errstr);

        $ctrl = new self();
        $ctrl->displayCommonErrorPageAndExit(self::COMMON_ERROR_MESSAGE, 500);
        return false;

    }

    /**
     * this is default exception handler for PHP
     * (called by PHP - registered in function: registerErrorHandlers())
     */
    public static function commonExceptionHandler(/* TODO: PHP7: Throwable */$e){
        $ctrl = new self();

        // check what kind of exception we have
        if( $e instanceof OcException){
            Debug::logOcException($e);

            if( $e->displayToUser() ){
                $ctrl->displayCommonErrorPageAndExit($e->getMessage(), $e->getCode());
                exit;
            }else{
                $ctrl->displayCommonErrorPageAndExit(self::COMMON_ERROR_MESSAGE, $e->getCode());
            }

        }else if( $e instanceof Exception){
            Debug::logException($e);

            $ctrl->displayCommonErrorPageAndExit(self::COMMON_ERROR_MESSAGE, $e->getCode());
            exit;

        }else if(Php7Handler::isThrowableInstance($e)){
            Debug::logThrowable($e);

            $ctrl->displayCommonErrorPageAndExit(self::COMMON_ERROR_MESSAGE, $e->getCode());
            exit;
        }

        Debug::errorLog("Another type of exception?!: Class:".get_class($e));
        exit;
    }
}
