<?php
class cache
{
    const TYPE_OTHERTYPE = 1;
    const TYPE_TRADITIONAL = 2;
    const TYPE_MULTICACHE = 3;
    const TYPE_VIRTUAL = 4;
    const TYPE_WEBCAM = 5;
    const TYPE_EVENT = 6;
    const TYPE_QUIZ = 7;
    const TYPE_MOVING = 8;
    const TYPE_PODCAST = 9;
    const TYPE_OWNCACHE = 10;

    private $cacheTypeIcons = null;

    public static $status = array (
        1 => 'Ready for search',
        2 => 'Temporarily unavailable',
        3 => 'Archived',
        4 => 'Hidden by approvers to check',
        5 => 'Not yet available',
        6 => 'Blocked by COG',
    );

    private static $type = array (
        self::TYPE_OTHERTYPE => array(
            'name' => 'other',
            'icon' => 'unknown.png',
            'translation' => 'cacheType_5'
        ),
        self::TYPE_TRADITIONAL => array(
            'name' => 'traditional',
            'icon' => 'traditional.png',
            'translation' => 'cacheType_1'
        ),
        self::TYPE_MULTICACHE => array(
            'name' => 'multicache',
            'icon' => 'multi.png',
            'translation' => 'cacheType_2',
        ),
        self::TYPE_VIRTUAL => array(
            'name' => 'virtual',
            'icon' => 'virtual.png',
            'translation' => 'cacheType_8'
        ),
        self::TYPE_WEBCAM => array(
            'name' => 'webcam',
            'icon' => 'webcam.png',
            'translation' => 'cacheType_7'
        ),
        self::TYPE_EVENT => array(
            'name' => 'event',
            'icon' => 'event.png',
            'translation' => 'cacheType_6'
        ),
        self::TYPE_QUIZ => array(
            'name' => 'quiz',
            'icon' => 'quiz.png',
            'translation' => 'cacheType_3'
        ),
        self::TYPE_MOVING => array(
            'name' => 'moving',
            'icon' => 'moving.png',
            'translation' => 'cachetype_4'
        ),
        self::TYPE_PODCAST => array(
            'name' => 'podcast',
            'icon' => 'podcache.png',
            'translation' => 'cacheType_9'
        ),
        self::TYPE_OWNCACHE => array(
            'name' => 'own-cache',
            'icon' => 'owncache.png',
            'translation' => 'cacheType_10',
        ),
    );

    private static $iconPath = 'tpl/stdstyle/images/cache/';
    private static $iconSmallStr = '16x16-';
    private static $iconFoundStr = '-found';
    private static $iconArchivedStr = '-a';
    private static $iconTmpUnavStr = '-n';

    function __construct(){
        $this->cacheTypeIcons = self::getCacheIconsSet();
    }

    /**
     * prepare array contain set of icons for diffrent cachetypes
     */
    public static function getCacheIconsSet() {
        $cacheTypeIcons = self::$type;
        foreach ($cacheTypeIcons as $cacheTypeId => $cacheType) {
            $cacheTypeIcons[$cacheTypeId]['iconSet'] = array (
                1 => array ( //active, published
                    'iconFound' => self::$iconPath.self::getFoundCacheIcon($cacheType['icon']),
                    'iconSmall' => self::$iconPath.self::$iconSmallStr.$cacheType['icon'],
                    'iconSmallFound' => self::$iconPath.self::getFoundCacheIcon(self::$iconSmallStr.$cacheType['icon']),
                    'iconSmallOwner' => self::$iconPath.self::getOwnerCacheIcon(self::$iconSmallStr.$cacheType['icon']),
                ),
                2 => array( //tempUnavailable
                    'iconFound' => self::$iconPath.self::getFoundCacheIcon($cacheType['icon'],self::$iconTmpUnavStr),
                    'iconSmall' => self::$iconPath.self::$iconSmallStr.$cacheType['icon'],
                    'iconSmallFound' => self::$iconPath.self::getFoundCacheIcon(self::$iconSmallStr.$cacheType['icon'],self::$iconTmpUnavStr),
                    'iconSmallOwner' => self::$iconPath.self::getOwnerCacheIcon(self::$iconSmallStr.$cacheType['icon'],self::$iconTmpUnavStr),
                ),
                3 => array( // archived
                    'iconFound' => self::$iconPath.self::getFoundCacheIcon($cacheType['icon'],self::$iconArchivedStr),
                    'iconSmall' => self::$iconPath.self::$iconSmallStr.$cacheType['icon'],
                    'iconSmallFound' => self::$iconPath.self::getFoundCacheIcon(self::$iconSmallStr.$cacheType['icon'],self::$iconArchivedStr),
                    'iconSmallOwner' => self::$iconPath.self::getOwnerCacheIcon(self::$iconSmallStr.$cacheType['icon'],self::$iconArchivedStr),
                ),
            );
        }
        return $cacheTypeIcons;
    }

    private static function getFoundCacheIcon($cacheIcon,$statusStr='') {
        $tmp = explode('.', $cacheIcon);
        $tmp[0] = $tmp[0].$statusStr.'-found';
        return implode('.', $tmp);
    }

    private static function getOwnerCacheIcon($cacheIcon,$statusStr='') {
        $tmp = explode('.', $cacheIcon);
        $tmp[0] = $tmp[0].$statusStr.'-s-owner';
        return implode('.', $tmp);
    }

    public function getCacheTypeIcons()
    {
        return $this->cacheTypeIcons;
    }

}
