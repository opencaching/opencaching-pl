<?php

namespace src\Controllers\Cron;

use DateInterval;
use DateTime;
use src\Controllers\BaseController;
use src\Models\Notify\Notify;
use src\Models\Notify\NotifyEmailSender;
use src\Models\User\User;
use src\Utils\Lock\Lock;

class NotifyController extends BaseController
{
    private const NOTIFY_FLAG = 'notification-run_notify.date';

    private DateTime $lastRun;

    public function isCallableFromRouter($actionName): bool
    {
        // this controller is used by cron only - router shouldn't call it!
        return false;
    }

    public function index()
    {
        $this->processNotifyQueue();
    }

    private function processNotifyQueue()
    {
        $lockHandle = Lock::tryLock($this, Lock::EXCLUSIVE | Lock::NONBLOCKING);

        if (! $lockHandle) {
            exit('Another instance of NotifyController is running or problem with lock file');
        }
        $this->lastRun = $this->getFlagTime();
        $this->touchFlag();

        $notifiesWaiting = Notify::getUniqueUserIdNotifiesList();

        foreach ($notifiesWaiting as $uniqueUser) {
            $itemUser = User::fromUserIdFactory($uniqueUser['user_id']);
            // Check if user wants to receive notifications
            if (! $itemUser->getNotifyCaches()) { // If not - delete waiting notifications for him
                Notify::deleteNotifiesForUserId($itemUser->getUserId());
            } elseif ($this->checkIfShouldSendToUser($itemUser)) {
                $this->sendNotifiesAndClean($itemUser);
            }
            unset($itemUser);
        }
        Lock::unlock($lockHandle);
    }

    /**
     * Check if should send notify for user just now
     */
    private function checkIfShouldSendToUser(User $user): bool
    {
        $right_time = new DateTime();
        $hour_now = $right_time->format('H');

        switch ($user->getWatchmailMode()) {
            case '1': // Send notifications every hour
                $right_time->setTime(intval($hour_now), 0);
                break;
            case '0': // Notify once per day
                if ($user->getWatchmailHour() > $hour_now) {
                    $right_time->sub(new DateInterval('P1D'));
                }
                $right_time->setTime(intval($user->getWatchmailHour()), 0);
                break;
            case '2': // Notify once per week
                if ($user->getWatchmailHour() > $hour_now) {
                    $right_time->sub(new DateInterval('P1D'));
                }
                $right_time->setTime(intval($user->getWatchmailHour()), 0);

                if (intval($user->getWatchmailDay()) >= 1 && intval($user->getWatchmailDay()) <= 7) { // Check for sure
                    while (intval($right_time->format('N')) != intval($user->getWatchmailDay())) {
                        $right_time->sub(new DateInterval('P1D'));
                    }
                }
                break;
        }

        return $this->lastRun < $right_time;
    }

    private function sendNotifiesAndClean(User $user)
    {
        $notifiesList = Notify::getAllNotifiesForUserId($user->getUserId());
        NotifyEmailSender::sendNewCacheNotify($notifiesList, $user);
        Notify::deleteNotifiesForUserId($user->getUserId());
    }

    /**
     * Change mtime of NOTIFY_FLAG - used to check previous run
     */
    private function touchFlag(): bool
    {
        return touch($this->getFlagFilename());
    }

    /**
     * Returns DateTime object of mtime Flag file
     *
     * @return DateTime - last modification time of NOTIFY_FLAG
     */
    private function getFlagTime(): DateTime
    {
        $mTime = new DateTime();

        if (file_exists($this->getFlagFilename())) {
            $mTime->setTimestamp(filemtime($this->getFlagFilename()));
        } else {
            $mTime->setTimestamp(0);
        }

        return $mTime;
    }

    /**
     * Returns notify flag filename with full path
     */
    private function getFlagFilename(): string
    {
        return $this->ocConfig->getDynamicFilesPath() . self::NOTIFY_FLAG;
    }
}
