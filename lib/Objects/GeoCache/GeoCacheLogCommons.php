<?php
namespace lib\Objects\GeoCache;

use Utils\Debug\Debug;
use lib\Objects\BaseObject;

/**
 * Common consts etc. for geocache log
 *
 */

class GeoCacheLogCommons extends BaseObject{

    const LOGTYPE_FOUNDIT = 1;
    const LOGTYPE_DIDNOTFIND = 2;
    const LOGTYPE_COMMENT = 3;
    const LOGTYPE_MOVED = 4;
    const LOGTYPE_NEEDMAINTENANCE = 5;
    const LOGTYPE_MADEMAINTENANCE = 6;
    const LOGTYPE_ATTENDED = 7;
    const LOGTYPE_WILLATTENDED = 8;
    const LOGTYPE_ARCHIVED = 9;
    const LOGTYPE_READYTOSEARCH = 10;
    const LOGTYPE_TEMPORARYUNAVAILABLE = 11;
    const LOGTYPE_ADMINNOTE = 12;

    const ICON_PATH = 'tpl/stdstyle/images/log/'; //path to the dir with log-type icons

    public function __construct()
    {
        parent::__construct();
    }

    public static function GetIconForType($logType, $fileNameOnly = false){

        switch($logType){
            case self::LOGTYPE_FOUNDIT:
                $icon = '16x16-found.png';
                break;
            case self::LOGTYPE_DIDNOTFIND:
                $icon = '16x16-dnf.png';
                break;
            case self::LOGTYPE_COMMENT:
                $icon = '16x16-note.png';
                break;
            case self::LOGTYPE_MOVED:
                $icon = '16x16-moved.png';
                break;
            case self::LOGTYPE_NEEDMAINTENANCE:
                $icon = '16x16-need-maintenance.png';
                break;
            case self::LOGTYPE_MADEMAINTENANCE:
                $icon = '16x16-made-maintenance.png';
                break;
            case self::LOGTYPE_ATTENDED:
                $icon = '16x16-attend.png';
                break;
            case self::LOGTYPE_WILLATTENDED:
                $icon = '16x16-will_attend.png';
                break;
            case self::LOGTYPE_ARCHIVED:
                $icon = '16x16-trash.png';
                break;
            case self::LOGTYPE_READYTOSEARCH:
                $icon = '16x16-published.png';
                break;
            case self::LOGTYPE_TEMPORARYUNAVAILABLE:
                $icon = '16x16-temporary.png';
                break;
            case self::LOGTYPE_ADMINNOTE:
                $icon = '16x16-octeam.png';
                break;
            default:
                Debug::errorLog("Unknown log type: $logType");
                $icon = '16x16-found.png';
                break;

        }

        if(!$fileNameOnly){
            $icon = self::ICON_PATH . $icon;
        }

        return $icon;
    }

    public static function typeTranslationKey($logType){

        switch($logType){
            case self::LOGTYPE_FOUNDIT:         return 'logType1';
            case self::LOGTYPE_DIDNOTFIND:      return 'logType2';
            case self::LOGTYPE_COMMENT:         return 'logType3';
            case self::LOGTYPE_MOVED:           return 'logType4';
            case self::LOGTYPE_NEEDMAINTENANCE: return 'logType5';
            case self::LOGTYPE_MADEMAINTENANCE: return 'logType6';
            case self::LOGTYPE_ATTENDED:        return 'logType7';
            case self::LOGTYPE_WILLATTENDED:    return 'logType8';
            case self::LOGTYPE_ARCHIVED:        return 'logType9';
            case self::LOGTYPE_READYTOSEARCH:   return 'logType10';
            case self::LOGTYPE_TEMPORARYUNAVAILABLE: return 'logType11';
            case self::LOGTYPE_ADMINNOTE:       return 'logType12';
            default:
                Debug::errorLog("Unknown log type: $logType");
                return '';
        }
    }


    /**
     * There are many places where log text is displayed as a tooltip
     * It is needed to remove many chars which can break the tooltip display operation
     *
     * @param String $text - original log text
     * @return String - clean log text
     */
    public static function cleanLogTextForToolTip( $text ){

        //strip all tags but not <li>
        $text = strip_tags($text, "<li>");

        $replace = array(
            //'<p>&nbsp;</p>' => '', //duplicated ? by strip_tags above
            '&nbsp;' => ' ',
            //'<p>' => '', //duplicated ? by strip_tags above
            "\n" => ' ',
            "\r" => '',
            //'</p>' => "", //duplicated ? by strip_tags above
            //'<br>' => "", //duplicated ? by strip_tags above
            //'<br />' => "", //duplicated ? by strip_tags above
            //'<br/>' => "", //duplicated ? by strip_tags above
            '<li>' => " - ",
            '</li>' => "",
            '&oacute;' => 'o',
            '&quot;' => '-',
            //'&[^;]*;' => '', ???
            '&' => '',
            "'" => '',
            '"' => '',
            '<' => '',
            '>' => '',
            '(' => ' -',
            ')' => '- ',
            ']]>' => ']] >',
            '' => ''
        );

        $text = str_ireplace( array_keys($replace), array_values($replace), $text);
        return preg_replace('/[\x00-\x08\x0E-\x1F\x7F\x0A\x0C]+/', '', $text);

    }
}

