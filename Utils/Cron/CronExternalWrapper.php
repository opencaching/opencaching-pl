<?php

namespace Utils\Cron;

use lib\Objects\Cron\CronCommons;
use lib\Objects\Cron\CronExternalWaitingRoom;

class CronExternalWrapper
{
    public static function wrap()
    {
        if (!empty($_REQUEST["uuid"])) {
            $entryPoint = CronExternalWaitingRoom::get($_REQUEST["uuid"]);
        }
        $taskResult = null;
        $taskError = false;
        $errorMsg = null;
        if (!empty($entryPoint)) {
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
                header(CronCommons::WRAPPER_HEADER_ERROR . ': true');
                ob_clean();
                print $errorMsg;
            } else {
                print ob_get_clean();
            }
            flush();
        }
        return ( ($taskResult != null) ? (boolval($taskResult) ? 0 : 1) : 2 );
    }
}