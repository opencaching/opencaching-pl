<?php
        
require_once __dir__.'/../htmlpurifier/library/HTMLPurifier.auto.php';

/**
 * class designed to contain user input filters.
 *
 * @author Andrzej Łza Woźniak, 2014-11-17
 */
class userInputFilter 
{
    /**
     * filter html string using HTMLPurifier.
     * refer to http://htmlpurifier.org/ for details and documentation.
     *
     * @param string $dirtyHtml
     * @return string
     */
    public static function purifyHtmlString($dirtyHtml)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
        $config->set('Filter.YouTube', true);
        $config->set('HTML.SafeObject', true);
        $config->set('HTML.MaxImgLength', null);
        $config->set('CSS.MaxImgLength', null);
        $config->set('Attr.AllowedFrameTargets', array("_blank"));
        $purifier = new HTMLPurifier($config);
        $cleanHtml = $purifier->purify($dirtyHtml);
        return $cleanHtml;
    }

    /**
     * filter html string using HTMLPurifier
     * @param string $dirtyHtml
     * @return string
     */
    public static function purifyHtmlStringAndDecodeHtmlSpecialChars($dirtyHtml) {
        if (isset($_GET['use_purifier'])){
            $upo = $_GET['use_purifier'];
            switch($upo){
                case '0': 
                    return self::purifyHtmlStringAndDecodeHtmlSpecialChars_old($dirtyHtml);
                case '1': 
                case '':
                    return self::purifyHtmlStringAndDecodeHtmlSpecialChars_new($dirtyHtml);
                    
            }
        }
        
        // current working implementation - the old way
        return htmlspecialchars_decode($dirtyHtml);
    }
    
    /**
     * the oldest version of the function - for backward compatibility
     */
    private static function purifyHtmlStringAndDecodeHtmlSpecialChars_old($dirtyHtml) {
        return htmlspecialchars_decode($dirtyHtml);
    }

    /**
     * the newest possible version of the function - all the experiments should be done here
     */
    private static function purifyHtmlStringAndDecodeHtmlSpecialChars_new($dirtyHtml) {
        $dirtyHtml = htmlspecialchars_decode($dirtyHtml);
        $cleanHtml = self::purifyHtmlString($dirtyHtml);
        return $cleanHtml;
    }
    
}
