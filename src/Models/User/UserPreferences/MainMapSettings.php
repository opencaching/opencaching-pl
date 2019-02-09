<?php
namespace src\Models\User\UserPreferences;


class MainMapSettings extends UserPreferencesBaseData
{
    const KEY = 'mainMapSettings';

    public function __construct($key)
    {
        parent::__construct($key);
    }

    public function getDefaults()
    {
        return [
            'filters' => [
                'exArchived'    => true,
                'exFound'       => true,
                'exIgnored'     => true,
                'exMyOwn'       => false,
                'exNoGeokret'   => false,
                'exTempUnavail' => true,
                'exTypeEvent'   => false,
                'exTypeMoving'  => false,
                'exTypeMulti'   => false,
                'exTypeOther'   => false,
                'exTypeOwn'     => false,
                'exTypeQuiz'    => false,
                'exTypeTraditional' => false,
                'exTypeVirtual' => false,
                'exTypeWebcam'  => false,
                'ftfHunter'     => false,
                'exNoYetFound'  => false,
                'powertrailOnly' => false,
                'exWithoutRecommendation' => false,
                'rating' => "1-5|X",
                'size2' => "any",
             ],
            'map' => 'OSM',                 /* last used map */
        ];
    }


}
