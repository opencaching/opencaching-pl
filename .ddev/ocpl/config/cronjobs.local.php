<?php

// the devel dump is a 2023 snapshot, this would gradually archive every cache unavailable since then
unset($cronjobs['schedule']['AutoArchiveCachesJob']);

// calls external elevation APIs
unset($cronjobs['schedule']['AltitudeUpdateJob']);
