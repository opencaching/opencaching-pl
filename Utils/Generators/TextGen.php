<?php
namespace Utils\Generators;
/**
 * This class is used to generate automatic texts
 *
 */

class TextGen
{
    /**
     * Generates semi-random text of given lenght
     * @param int $textLen
     * @return string
     */
    public static function randomText($textLen)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $text = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $textLen; $i++) {
            $n = rand(0, $alphaLength);
            $text[] = $alphabet[$n];
        }
        return implode($text);
    }
}

