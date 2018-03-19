<?php
namespace Controllers\Cron;

use Controllers\BaseController;
use Utils\Lock\Lock;
use lib\Objects\Notify\Notify;
use lib\Objects\User\User;
use lib\Objects\Notify\NotifyEmailSender;

class NotifyController extends BaseController
{

    const NOTIFY_FLAG = "/tmp/notification-run_notify.date";

    /* @var \DateTime */
    private $lastRun;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
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
            die("Another instance of NotifyController is running or problem with lock file");
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
     *
     * @param User $user
     * @return boolean
     */
    private function checkIfShouldSendToUser(User $user)
    {
        $right_time = new \DateTime();
        $hour_now = $right_time->format('H');

        switch ($user->getWatchmailMode()) {
            case '1': // Send notifications every hour
                $right_time->setTime(intval($hour_now), 0, 0);
                break;
            case '0': // Notify once per day
                if ($user->getWatchmailHour() > $hour_now) {
                    $right_time->sub(new \DateInterval('P1D'));
                }
                $right_time->setTime(intval($user->getWatchmailHour()), 0, 0);
                break;
            case '2': // Notify once per week
                if ($user->getWatchmailHour() > $hour_now) {
                    $right_time->sub(new \DateInterval('P1D'));
                }
                $right_time->setTime(intval($user->getWatchmailHour()), 0, 0);
                if (intval($user->getWatchmailDay()) >= 1 && intval($user->getWatchmailDay()) <= 7) { // Check for sure
                    while (intval($right_time->format('N')) != intval($user->getWatchmailDay())) {
                        $right_time->sub(new \DateInterval('P1D'));
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
     *
     * @return boolean
     */
    private function touchFlag()
    {
        return touch(self::NOTIFY_FLAG);
    }

    /**
     * Returns DateTime object of mtime Flag file
     *
     * @return \DateTime - last modification time of NOTIFY_FLAG
     */
    private function getFlagTime()
    {
        $mTime = new \DateTime();
        if (file_exists(self::NOTIFY_FLAG)) {
            $mTime->setTimestamp(filemtime(self::NOTIFY_FLAG));
        } else {
            $mTime->setTimestamp(0);
        }
        return $mTime;
    }
}