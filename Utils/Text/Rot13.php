<?php
namespace Utils\Text;


class Rot13
{
    /**
     * perform str_rot13 without renaming HTML-Tags
     */
    public static function withoutHtml($str){
        $delimiter[0][0] = '&'; // start-char
        $delimiter[0][1] = ';'; // end-char
        $delimiter[1][0] = '<';
        $delimiter[1][1] = '>';
        $delimiter[2][0] = '[';
        $delimiter[2][1] = ']';

        $retval = '';

        while (mb_strlen($retval) < mb_strlen($str)) {
            $nNextStart = false;
            $sNextEndChar = '';
            foreach ($delimiter AS $del) {
                $nThisStart = mb_strpos($str, $del[0], mb_strlen($retval));

                if ($nThisStart !== false)
                    if (($nNextStart > $nThisStart) || ($nNextStart === false)) {
                        $nNextStart = $nThisStart;
                        $sNextEndChar = $del[1];
                    }
            }

            if ($nNextStart === false) {
                $retval .= str_rot13(mb_substr($str, mb_strlen($retval), mb_strlen($str) - mb_strlen($retval)));
            } else {
                // crypted part
                $retval .= str_rot13(mb_substr($str, mb_strlen($retval), $nNextStart - mb_strlen($retval)));

                // uncrypted part
                $nNextEnd = mb_strpos($str, $sNextEndChar, $nNextStart);

                if ($nNextEnd === false)
                    $retval .= mb_substr($str, $nNextStart, mb_strlen($str) - mb_strlen($retval));
                    else
                        $retval .= mb_substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
            }
        }

        return $retval;
    }

}

