<?php
namespace Utils\Text;


class TextConverter
{

    public static function addHyperlinkToURL($text){

        $texti = mb_strtolower($text);
        $retval = '';
        $curpos = 0;
        $starthttp = mb_strpos($texti, 'http://', $curpos);
        $endhttp = false;
        while (($starthttp !== false) || ($endhttp >= mb_strlen($text))) {
            $endhttp1 = mb_strpos($text, ' ', $starthttp);
            if ($endhttp1 === false){
                $endhttp1 = mb_strlen($text);
            }
            $endhttp2 = mb_strpos($text, "\n", $starthttp);

            if ($endhttp2 === false){
                $endhttp2 = mb_strlen($text);
            }

            $endhttp3 = mb_strpos($text, "\r", $starthttp);
            if ($endhttp3 === false){
                $endhttp3 = mb_strlen($text);
            }

            $endhttp4 = mb_strpos($text, '<', $starthttp);
            if ($endhttp4 === false){
                $endhttp4 = mb_strlen($text);
            }

            $endhttp5 = mb_strpos($text, '] ', $starthttp);
            if ($endhttp5 === false){
                $endhttp5 = mb_strlen($text);
            }

            $endhttp6 = mb_strpos($text, ')', $starthttp);
            if ($endhttp6 === false){
                $endhttp6 = mb_strlen($text);
            }

            $endhttp7 = mb_strpos($text, '. ', $starthttp);
            if ($endhttp7 === false){
                $endhttp7 = mb_strlen($text);
            }

            $endhttp = min($endhttp1, $endhttp2, $endhttp3,
                $endhttp4, $endhttp5, $endhttp6, $endhttp7);

            $retval .= mb_substr($text, $curpos, $starthttp - $curpos);
            $url = mb_substr($text, $starthttp, $endhttp - $starthttp);
            $retval .= '<a href="' . $url . '" alt="" target="_blank">' . $url . '</a>';

            $curpos = $endhttp;
            if ($curpos >= mb_strlen($text)){
                break;
            }
            $starthttp = mb_strpos(mb_strtolower($text), 'http://', $curpos);
        }

        $retval .= mb_substr($text, $curpos);
        return $retval;
    }

    /**
     * This function is moved from clicompatbase...
     * TODO: what does it do exacly?
     *
     * @param unknown $str
     */
    public static function mb_trim($str){
        $bLoop = true;
        while ($bLoop == true) {
            $sPos = mb_substr($str, 0, 1);

            if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
                $str = mb_substr($str, 1, mb_strlen($str) - 1);
                else
                    $bLoop = false;
        }

        $bLoop = true;
        while ($bLoop == true) {
            $sPos = mb_substr($str, -1, 1);

            if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
                $str = mb_substr($str, 0, mb_strlen($str) - 1);
                else
                    $bLoop = false;
        }

        return $str;
    }

    /**
     * This method converts polish months names from 1->2 grammatical case
     */
    public static function fixPlMonth($string)
    {
        $string = str_ireplace('styczeń', 'stycznia', $string);
        $string = str_ireplace('luty', 'lutego', $string);
        $string = str_ireplace('marzec', 'marca', $string);
        $string = str_ireplace('kwiecień', 'kwietnia', $string);
        $string = str_ireplace('maj', 'maja', $string);
        $string = str_ireplace('czerwiec', 'czerwca', $string);
        $string = str_ireplace('lipiec', 'lipca', $string);
        $string = str_ireplace('sierpień', 'sierpnia', $string);
        $string = str_ireplace('wrzesień', 'września', $string);
        $string = str_ireplace('październik', 'października', $string);
        $string = str_ireplace('listopad', 'listopada', $string);
        $string = str_ireplace('grudzień', 'grudnia', $string);
        return $string;
    }
}
