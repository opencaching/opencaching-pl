<?php
namespace lib\Objects\User\UserPreferences;


class UserMapSettings extends UserPreferencesBaseData
{
    const KEY = 'mapSettings';

    public function __construct($key)
    {
        parent::__construct($key);
    }

    public function getDefaults()
    {
        return [
            'filters' => [
                'be_ftf' => false,          /* only_ ftf */
                'h_arch' => true,           /* hide archived */
                'h_e' => false,             /* hide events */
                'h_found' => false,         /* hide founds */
                'h_ignored' => true,        /* hide ignored */
                'h_m' => false,             /* hide multi. */
                'h_noattempt' => false,     /* hide ? */
                'h_nogeokret' => false,     /* hide caches without geokrets */
                'h_o' => false,             /* hide ?? */
                'h_own' => false,           /* hide owning caches */
                'h_owncache' => false,      /* hide owncaches */
                'h_q' => false,             /* hide quizes */
                'h_t' => false,             /* hide ?? */
                'h_temp_unavail' => false,  /* hide temporary unavailable */
                'h_u' => false,             /* hide unknown */
                'h_v' => false,             /* hide virtuals */
                'h_w' => false,             /* hide webcams */
                'min_score' => -3,          /* minimum score of cache */
                'powertrail_only' => false, /* */
            ],
            'map' => 'OSM',                 /* last used map */
        ];
    }


}

