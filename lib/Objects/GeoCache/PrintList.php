<?php

namespace lib\Objects\GeoCache;

use Utils\Uri\Uri;

/**
 * This class handle old-style print-list used as a clippboard for list of caches to print
 * The purpose of this is to unify the code in fast refactoring.
 *
 * It should probably be improved.
 *
 */
class PrintList {


    public static function HandleRequest($cacheId)
    {

        if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {

            // add cache to print (do not duplicate items)
            if ( !isset($_SESSION['print_list']) || !is_array($_SESSION['print_list']) ){
                $_SESSION['print_list'] = array();
            }

            $_SESSION['print_list'][$cacheId] = $cacheId;
        }

        if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {

            // remove cache from print list
            self::RemoveCache($cacheId);
        }
    }

    public static function AddCacheUrl($cacheId)
    {
        return Uri::setOrReplaceParamValue('print_list', 'y');
    }

    public static function RemoveCacheUrl($cacheId)
    {
        return Uri::setOrReplaceParamValue('print_list', 'n');
    }

    /**
     * Return link to:
     *  - add cache if it is not at the list
     *  - remove cache from the list if it is currently listed
     *
     * @param unknown $cacheId
     */
    public static function AddOrRemoveCacheUrl($cacheId)
    {
        if(self::IsOnTheList($cacheId)){
            return self::RemoveCacheUrl($cacheId);
        }else{
            return self::AddCacheUrl($cacheId);
        }
    }

    public static function IsOnTheList($cacheId)
    {
        return
            isset($_SESSION['print_list']) &&
            is_array($_SESSION['print_list']) &&
            isset($_SESSION['print_list'][$cacheId]);
    }

    public static function GetContent()
    {
        if( isset($_SESSION['print_list']) && is_array($_SESSION['print_list']) ){
            return $_SESSION['print_list'];
        }else{
            return array();
        }
    }

    public static function Flush()
    {
        $_SESSION['print_list'] = array();
    }

    public static function RemoveCache($cacheId)
    {
        if (isset($_SESSION['print_list']) &&
            is_array($_SESSION['print_list']) &&
            isset($_SESSION['print_list'][$cacheId]) ){

            unset($_SESSION['print_list'][$cacheId]);
        }
    }

}


