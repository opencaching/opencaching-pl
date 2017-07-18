<?php

namespace Utils\Text;


class SmilesInText
{

    // TODO: UTF-8 compatible str_replace (with arrays)
    public static function process($text){
        return str_replace( self::$smileytext, self::getSmileyImages(), $text);
    }

    private static function getSmileyImages()
    {
        global $absolute_server_URI;

        return array(
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-smile.gif" alt=":)" title=":)" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-smile.gif" alt=":-)" title=":-)" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-wink.gif" alt=";)" title=";)" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-wink.gif" alt=";-)" title=";-)" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-laughing.gif" alt=":D" title=":D" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-cool.gif" alt="8)" title="8)" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-innocent.gif" alt="O:)" title="O:)" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-surprised.gif" alt=":-o" title=":-o" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-surprised.gif" alt=":o" title=":o" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-frown.gif" alt=":(" title=":(" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-frown.gif" alt=":-(" title=":-(" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-embarassed.gif" alt="::|" title="::|" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-cry.gif" alt=":,-(" title=":,-(" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-kiss.gif" alt=":*" title=":*" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-kiss.gif" alt=":-*" title=":-*" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-tongue-out.gif" alt=":P" title=":P" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-tongue-out.gif" alt=":-P" title=":-P" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-undecided.gif" alt=":-/" title=":-/" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-undecided.gif" alt=":/" title=":/" width="18px" height="18px" />',
            '<img src="' . $absolute_server_URI . 'lib/tinymce4/plugins/emotions/img/smiley-yell.gif" alt="XO" title="XO" width="18px" height="18px" />'
        );

    }



    private static $smileytext = array(
        " :) ",
        " :-) ",
        " ;) ",
        " ;-) ",
        " :D ",
        " 8) ",
        " O:) ",
        " :-o ",
        " :o ",
        " :( ",
        " :-( ",
        " ::| ",
        " :,-( ",
        " :* ",
        " :-* ",
        " :P ",
        " :-P ",
        " :-/ ",
        " :/ ",
        " XO "
    );

    private static $smileyshow = array(
        '1',
        '0',
        '1',
        '0',
        '1',
        '1',
        '1',
        '0',
        '1',
        '1',
        '0',
        '1',
        '1',
        '1',
        '0',
        '1',
        '0',
        '0',
        '1',
        '1'
    );

}
