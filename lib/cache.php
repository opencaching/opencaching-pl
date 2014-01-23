<?php
class cache
{
    public static $status = array (
        1 => 'Ready for search',
        2 => 'Temporarily unavailable',
        3 => 'Archived',
        4 => 'Hidden by approvers to check',
        5 => 'Not yet available',
        6 => 'Blocked by COG',
    );

    private static $type = array (
        1 => array(
            'name' => 'other',
            'icon' => 'unknown.png',
            'translation' => 'cacheType_5'
        ),
        2 => array(
            'name' => 'traditional',
            'icon' => 'traditional.png',
            'translation' => 'cacheType_1'
        ),
        3 => array(
            'name' => 'multicache',
            'icon' => 'multi.png',
            'translation' => 'cacheType_2',
        ),
        4 => array(
            'name' => 'virtual',
            'icon' => 'virtual.png',
            'translation' => 'cacheType_8'
        ),
        5 => array(
            'name' => 'webcam',
            'icon' => 'webcam.png',
            'translation' => 'cacheType_7'
        ),
        6 => array(
            'name' => 'event',
            'icon' => 'event.png',
            'translation' => 'cacheType_6'
        ),
        7 => array(
            'name' => 'quiz',
            'icon' => 'quiz.png',
            'translation' => 'cacheType_3'
        ),
        8 => array(
            'name' => 'moving',
            'icon' => 'moving.png',
            'translation' => 'cachetype_4'
        ),
        9 => array(
            'name' => 'podcast',
            'icon' => 'podcache.png',
            'translation' => 'cacheType_9'
        ),
        10 => array(
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

}
