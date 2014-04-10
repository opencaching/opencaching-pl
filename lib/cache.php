<?php
final class cache
{
    const TYPE_OTHERTYPE = 1;
    const TYPE_TRADITIONAL = 2;
    const TYPE_MULTICACHE = 3;
    const TYPE_VIRTUAL = 4;
    const TYPE_WEBCAM = 5;
    const TYPE_EVENT = 6;
    const TYPE_QUIZ = 7;
    const TYPE_MOVING = 8;
    const TYPE_GEOPATHFINAL = 9;
    const TYPE_OWNCACHE = 10;

    const SIZE_MICRO = 2;
    const SIZE_SMALL = 3;
    const SIZE_NORMAL = 4;
    const SIZE_LARGE = 5;
    const SIZE_VERYLARGE = 6;
    const SIZE_NOCONTAINER = 7;

    const STATUS_READY = 1;
    const STATUS_UNAVAILABLE = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_WAITAPPROVERS = 4;
    const STATUS_NOTYETAVAILABLE = 5;
    const STATUS_BLOCKED = 6;

    private $cacheTypeIcons = null;

    private $status = array (
        self::STATUS_READY => array(
            'description' => 'Ready for search',
            'translation' => 'cacheStatus_1',
        ),
        self::STATUS_UNAVAILABLE => array(
            'description' => 'Temporarily unavailable',
            'translation' => 'cacheStatus_2',
        ),
        self::STATUS_ARCHIVED => array(
            'description' => 'Archived',
            'translation' => 'cacheStatus_3',
        ),
        self::STATUS_WAITAPPROVERS => array(
            'description' => 'Hidden by approvers to check',
            'translation' => 'cacheStatus_4',
        ),
        self::STATUS_NOTYETAVAILABLE => array(
            'description' => 'Not yet available',
            'translation' => 'cacheStatus_5',
        ),
        self::STATUS_BLOCKED => array(
            'description' => 'Blocked by COG',
            'translation' => 'cacheStatus_6',
        ),
    );

    private $size = array(
        self::SIZE_MICRO => array(
            'id' => self::SIZE_MICRO,
            'translation' => 'cacheSize_2',
        ),
        self::SIZE_SMALL => array(
            'id' => self::SIZE_SMALL,
            'translation' => 'cacheSize_3',
        ),
        self::SIZE_NORMAL => array(
            'id' => self::SIZE_NORMAL,
            'translation' => 'cacheSize_4',
        ),
        self::SIZE_LARGE => array(
            'id' => self::SIZE_LARGE,
            'translation' => 'cacheSize_5',
        ),
        self::SIZE_VERYLARGE => array(
            'id' => self::SIZE_VERYLARGE,
            'translation' => 'cacheSize_6',
        ),
        self::SIZE_NOCONTAINER => array(
            'id' => self::SIZE_NOCONTAINER,
            'translation' => 'cacheSize_7',
        ),
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
        self::TYPE_GEOPATHFINAL => array(
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

    private function __construct()
    {
        $this->cacheTypeIcons = self::getCacheIconsSet();
    }

    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new cache();
        }
        return $inst;
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

    public function getCacheSizes()
    {
        return $this->size;
    }

    public function getCacheTypes()
    {
        return self::$type;
    }

    public function getCacheStatuses()
    {
        return $this->status;
    }
  
}
