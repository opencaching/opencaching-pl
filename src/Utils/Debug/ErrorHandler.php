<?php
namespace src\Utils\Debug;

use Throwable;
use src\Models\OcConfig;
use src\Utils\Email\EmailSender;

class ErrorHandler
{
    /** Failsafe flag to avoid duplicate error handling **/
    private static $errorHandled = false;

    /**
     * Install error and exception handlers; must be called once upon startup,
     * as early as possible.
     */
    public static function install ()
    {
        register_shutdown_function([__CLASS__, 'handleFatalError']);
        set_exception_handler([__CLASS__, 'handleException']);
        set_error_handler([__CLASS__, 'handleError']);
    }

    /**
     * Error handler callback; called for all non-fatal PHP errors, warnings
     * and notices that are no exceptions, e.g. division by zero or reference
     * of undefined variable.
     */
    public static function handleError($severity, $message, $filename, $lineno)
    {
        if ($severity != E_STRICT && $severity != E_DEPRECATED && error_reporting() > 0) {
            // Map error / warning / notice to exception, which will either
            // get caught or be handled by self::handleException().

            throw new \ErrorException($message, 0, $severity, $filename, $lineno);
        }
    }

    /**
     * Exception handler callback; called for all exceptions that are not
     * handled by a catch {}.
     */
    public static function handleException(Throwable $e)
    {
        self::$errorHandled = true;
        $msg = sprintf("%s: [%s:%d] %s\n\n", get_class($e), $e->getFile(), $e->getLine(), $e->getMessage());

        if (empty($e->getTrace())) {
            // there is no trace
            $msg .= '- NO TRACE -';
        } else {
            $msg .= $e->getTraceAsString();
        }
        self::processError($msg, $e);
    }

    /**
     * Shutdown callback; called on script termination; handles all fatal
     * PHP errors except E_PARSE (which cannot be caught in PHP code).
     */
    public static function handleFatalError()
    {
        if (!self::$errorHandled &&
            ($error = error_get_last()) &&
            in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])
        ) {
            self::$errorHandled = true;
            self::processError(
                'Fatal error ' . $error['message'] .
                ' at line ' . $error['line'] . ' of ' . $error['file']);
        }
    }

    private static function processError(string $msg, Throwable $exception = null)
    {
        global $debug_page;

        // Try to send an admin email
        $mailFail = false;
        try {
            EmailSender::adminOnErrorMessage($msg);
        } catch (\Exception $e) {
            try {
                foreach (OcConfig::getEmailAddrTechAdminNotification() as $techAdminAddr) {
                    mail($techAdminAddr, "OC site error", $msg);
                }
            } catch (\Exception $e) {
                try {
                    mail("root@localhost", "OC site error", $msg);
                } catch (\Exception $e) {
                    $mailFail = true;
                }
            }
        }

        // Try to log error
        try {
            if ($exception) {
                Debug::logException($exception);
            } else {
                Debug::errorLog($msg);
            }
        } catch (\Exception $e) {
            // logging failed
            error_log('OC ERROR HANDLER: Problem with logging.');
        }

        // Output error message
        if (PHP_SAPI == "cli") {
            echo $msg . "\n";
        } else {
            try {
                if (function_exists('tr')) {
                    $pageError = tr('page_error_1') . ' ';
                    $pageError .= tr($mailFail ? 'page_error_3' : 'page_error_2');
                    $mainPageLinkTitle = tr('page_error_back');
                } else {
                    // sometimes error can occured befor I18n init...
                    $pageError = 'An error occured while processing your request.' . ' ';
                    $pageError .= 'If the problem persists, please '.
                                  '<a href="/articles.php?page=contact">contact</a>' .
                                  'the OC team and describe step by step how to reproduce this error.';
                    $mainPageLinkTitle = 'Go to the main page';
                }
            } catch (\Exception $e) {
                $pageError = 'An error occured while processing your request. ';
                $pageError .=
                    $mailFail
                    ? 'If the problem persists, please <a href="/articles.php?page=contact">contact</a> ' .
                      'the OC team and describe step by step how to reproduce this error.'
                    : 'The OC site admins have been notified.';
                $mainPageLinkTitle = 'Go to the main page';
            }

            if (isset($_SERVER["SCRIPT_FILENAME"])) {
                $showMainPageLink = basename($_SERVER["SCRIPT_FILENAME"]) != 'index.php';
            } else {
                $showMainPageLink = false;
            }

            $errorMsg = ($debug_page ? $msg : '');

            // No calls to tpl_ or View:: here, to avoid generating an error
            // within the error handler (code may be unstable).

            include __DIR__ . '/../../../src/Views/page_error.tpl.php';
        }
    }
}
