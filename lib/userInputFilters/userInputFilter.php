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
     * filter html string using HTMLPurifier, then htmlspecialchars_decode.
     * @param string $dirtyHtml
     * @return string
     */
    public static function purifyHtmlStringAndDecodeHtmlSpecialChars($dirtyHtml) {
        $cleanHtml = self::purifyHtmlString($dirtyHtml);
        return htmlspecialchars_decode($cleanHtml);
    }
}
