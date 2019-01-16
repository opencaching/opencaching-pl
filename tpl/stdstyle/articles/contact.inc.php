<?php

use Utils\I18n\I18n;

class contactDataProcessor
{

    public static function processContacts($contactData, $headerLevel)
    {
        $result = '';
        foreach ($contactData as $contact) {
            $result .= self::processContactItem($contact, $headerLevel);
        }
        return $result;
    }

    private static function processContactItem($contact, $headerLevel)
    {
        $result = '';

        if (isset($contact['groupName'])) {
            $result .= "<h$headerLevel>" . self::translate($contact['groupName'], $contact) . "</h$headerLevel>\n";
        }
        if (isset($contact['emailAddress'])) {
            $result .= "<p><b>E-mail: " . $contact['emailAddress'] . "</b></p>\n";
        }
        if (isset($contact['groupDescription'])) {
            $groupDescription = $contact['groupDescription'];
            if (is_array($groupDescription)) {
                foreach ($groupDescription as $groupItem) {
                    $result .= "<p>" . self::translate($groupItem, $contact) . "</p>\n";
                }
            } else {
                $result .= "<p>" . self::translate($groupDescription, $contact) . "</p>\n";
            }
        }
        if (isset($contact['subgroup'])) {
            $result .= self::processContacts($contact['subgroup'], $headerLevel + 1);
        }

        return $result;
    }

    private static function translate($str, $context = null)
    {
        if(I18n::isTranslationAvailable($str)){
            $str = I18n::translatePhrase($str, null, true);
        }
        $str = self::resolve($str, $context);
        return $str;
    }

    private static function resolve($str, $context)
    {
        if (is_array($context)) {
            foreach ($context as $varname => $varvalue) {
                if (is_string($varvalue)) {
                    $str = mb_ereg_replace('{' . $varname . '}', $varvalue, $str);
                }
            }
        }
        return $str;
    }

}

tpl_set_var('contact_text', contactDataProcessor::processContacts($contactData, 1));
