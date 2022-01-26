<?php

namespace src\Controllers\Cron;

use okapi\Facade;
use src\Controllers\BaseController;
use src\Controllers\MeritBadgeController;
use src\Models\GeoCache\GeoCache;

class OkapiController extends BaseController
{
    public function isCallableFromRouter(string $actionName): bool
    {
        // This controller is used by cron only - router shouldn't call it!
        return false;
    }

    public function index()
    {
        // The OKAPI signal processing is reentrant - we don't need a lock.
        $this->process_signals();
    }

    private function process_signals()
    {
        // Do 10 signals each. If the external altitude fetcher is slow,
        // this will speed up things by running multiple cronjob instances
        // simultaneously.

        while ($signals = Facade::fetch_signals(10)) {
            foreach ($signals as $signal) {
                switch ($signal['type']) {
                    case 'log-merit-badges':
                        $cacheId = $signal['payload']['cache_id'];
                        $userId = $signal['payload']['user_id'];

                        $ctrlMeritBadge = new MeritBadgeController();
                        $ctrlMeritBadge->updateTriggerLogCache($cacheId, $userId);
                        $ctrlMeritBadge->updateTriggerTitledCache($cacheId, $userId);
                        $ctrlMeritBadge->updateTriggerCacheAuthor($cacheId);

                        Facade::signals_done([$signal]);
                        break;
                    case 'cache-altitude':
                        $cacheId = $signal['payload']['cache_id'];

                        $geoCache = GeoCache::fromCacheIdFactory($cacheId);
                        $geoCache->updateAltitude();

                        Facade::signals_done([$signal]);
                        break;
                }
            }
        }
    }
}
