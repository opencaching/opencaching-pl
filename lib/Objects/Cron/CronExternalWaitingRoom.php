<?php

namespace lib\Objects\Cron;

use lib\Controllers\Php7Handler;
use Utils\Lock\Lock;

final class CronExternalWaitingRoom
{
    const APC_KEY = "fe5266a43aa291e360ba7357c91e111f865eb189d4177a0a2b71d484";
    
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
    
    private static function lock() {
        $lockHandle = Lock::tryLock(__CLASS__, Lock::EXCLUSIVE);
        if (! $lockHandle) {
            throw new \RuntimeException("Cannot obtain lock");
        }
        return $lockHandle;
    }
    
    private static function unlock($lockHandle) {
        if ($lockHandle !== null) {
            Lock::unlock($lockHandle);
        }
    }
}