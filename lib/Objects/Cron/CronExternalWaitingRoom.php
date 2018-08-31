<?php
/**
 * Contains \lib\Objects\Cron\CronExternalWaitingRoom class definition
 */
namespace lib\Objects\Cron;

use lib\Controllers\Php7Handler;
use Utils\Lock\Lock;

/**
 * Used for saving and handing over (UUID, Entry Point) pairs in external
 * execution mode. The cron scheduler puts the values in and the started wrapper
 * reads them out with removal
 */
final class CronExternalWaitingRoom
{
    /** Apc(u) key identifying saved waiting room  */
    const APC_KEY = "fe5266a43aa291e360ba7357c91e111f865eb189d4177a0a2b71d484";

    /**
     * Puts the given pair in waiting room array, working in exclusive mode
     *
     * @param string $uuid the UUID to put
     * @param string $etryPoint the entry point to add
     */
    public static function put($uuid, $entryPoint)
    {
        $lockHandle = self::lock();
        $room = Php7Handler::apc_fetch(self::APC_KEY);
        if (!isset($room[$uuid])) {
            $room[$uuid] = $entryPoint;
            Php7Handler::apc_store(self::APC_KEY, $room, 0);
        }
        self::unlock($lockHandle);
    }

    /**
     * Gives the entry point associated in cache with given UUID, removing it
     *
     * @param string $uuid the UUID of entry point to retrieve
     *
     * @return string entry point associated with UUID or null if not found
     */
    public static function get($uuid)
    {
        $result = null;

        $lockHandle = self::lock();
        $room = Php7Handler::apc_fetch(self::APC_KEY);
        if (isset($room[$uuid])) {
            $result = $room[$uuid];
            unset($room[$uuid]);
            Php7Handler::apc_store(self::APC_KEY, $room, 0);
        }
        self::unlock($lockHandle);

        return $result;
    }

    /**
     * Locks the access in exclusive mode
     *
     * @return resource lock handle
     */
    private static function lock() {
        $lockHandle = Lock::tryLock(__CLASS__, Lock::EXCLUSIVE);
        if (! $lockHandle) {
            throw new \RuntimeException("Cannot obtain lock");
        }
        return $lockHandle;
    }

    /**
     * Unlocks the access
     *
     * @param resource $lockHandle handle to unlock
     */
    private static function unlock($lockHandle) {
        if ($lockHandle !== null) {
            Lock::unlock($lockHandle);
        }
    }
}