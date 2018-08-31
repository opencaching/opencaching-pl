<?php
/**
 * Contains \Utils\Cron\CronExternalWrapper class definition
 */
namespace Utils\Cron;

use lib\Objects\Cron\CronCommons;
use lib\Objects\Cron\CronExternalWaitingRoom;

/**
 * Used as a wraper class to execute scheduled task in separate process.
 */
class CronExternalWrapper
{
    /**
     * Retrieves from CronExternalWaitingRoom entry point associated with UUID
     * passed in WWW request parameter and then executes it returning the result
     * or error in response header and output or error message as a response body
     *
     * @return integer 0 if task succeded, 1 if task returned failure or 2 in
     *      case of errors
     */
    public static function wrap()
    {
        if (!empty($_REQUEST["uuid"])) {
            $entryPoint = CronExternalWaitingRoom::get($_REQUEST["uuid"]);
        }
        $taskResult = null;
        $taskError = false;
        $errorMsg = null;
        if (!empty($entryPoint)) {
            header(CronCommons::WRAPPER_HEADER_ERROR . ': true');
            ob_start();
            try {
                $taskResult = eval('return '.$entryPoint.'();');
            } catch (\Throwable $e) {
                /* PHP 7 */
                $taskResult = false;
                $errorMsg = $e->getMessage();
                $taskError = true;
            } catch (\Exception $e) {
                /* PHP 5 only */
                $taskResult = false;
                $errorMsg = $e->getMessage();
                $taskError = true;
            }
            header(
                CronCommons::WRAPPER_HEADER_RESULT . ': '
                . (
                    ($taskResult !== null)
                    ? (
                        boolval($taskResult)
                        ? "true"
                        : "false"
                      )
                    : "null"
                )
            );
            if ($taskError) {
                ob_clean();
                print $errorMsg;
            } else {
                header_remove(CronCommons::WRAPPER_HEADER_ERROR);
                print ob_get_clean();
            }
            flush();
        }
        return ( ($taskResult != null) ? (boolval($taskResult) ? 0 : 1) : 2 );
    }
}