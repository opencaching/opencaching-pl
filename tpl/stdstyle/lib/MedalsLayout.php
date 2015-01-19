<?php

namespace tpl\stdstyle\lib;

/**
 * Containter for medals Layout data.
 *
 * @author Łza
 */
class MedalsLayout
{

    private $path = 'tpl/stdstyle/medals/';

    /**
     * template:
     * medalId => array (
     *      medalLevel1 => medalIconFilename1,
     *      medalLevel2 => medalIconFilename2,
     * ),
     * @var array
     */
    private $medalImages = array(
        1 => array( /* malopolska */
            1 => 'medal_11.png',
            2 => 'medal_12.png',
            3 => 'medal_13.png',
            4 => 'medal_14.png',
        ),
        2 => array( /* krakow */
            1 => 'medal_21.png',
            2 => 'medal_22.png',
        ),
        3 => array( /* Traditional Caches */
            1 => 'medal_31.png',
            2 => 'medal_32.png',
            3 => 'medal_33.png',
            4 => 'medal_34.png',
            5 => 'medal_35.png',
            6 => 'medal_36.png',
            7 => 'medal_37.png',
            8 => 'medal_38.png',
            9 => 'medal_39.png',
            10 => 'medal_310.png',
        ),
        4 => array( /* kotlina jeleniogorska */
            0 => 'GDKJ.jpg',
        ),
        5 => array( /* Lubelski Geocaching */
            1 => 'LubelskiGeocachingPopularny.jpg',
            2 => 'LubelskiGeocachingBrazowy.jpg',
            3 => 'LubelskiGeocachingSrebrny.jpg',
            4 => 'LubelskiGeocachingZloty.jpg',
            5 => 'LubelskiGeocachingHonorowy,jpg',
        ),
        6 => array( /* Max Altitude */
            1 => '1.png',
            2 => '2.png',
            3 => '3.png',
            4 => '4.png',
            5 => '5.png',
            6 => '6.png',
            7 => '7.png',
            8 => '8.png',
            9 => '9.png',
            10 => '10.png',),
    );

    public function getImage($medalType, $medalLevel)
    {
        return $this->path . $this->medalImages[$medalType][$medalLevel];
    }

}
