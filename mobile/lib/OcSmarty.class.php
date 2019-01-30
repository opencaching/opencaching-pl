<?php

use lib\Objects\OcConfig\OcConfig;
use Utils\I18n\I18n;

require_once(__DIR__.'/smarty/Smarty.class.php');

class OcSmarty extends Smarty
{
    public function __construct()
    {
        parent::__construct();

        $this->template_dir = OcConfig::getDynFilesPath() . 'lib/templates/';
        $this->compile_dir = OcConfig::getDynFilesPath() . 'lib/templates_c/';

        $this->loadTranslations();
    }

    private function loadTranslations()
    {
        I18n::init();

        $mobileTranslations = I18n::getPrefixedTranslationsWithFailover('mobile_');

        foreach ($mobileTranslations as $key => $text) {
            $this->assign($key, $text);
        }

        // Now build the language selection list.

        // Not all languages are available yet for mobile.
        $availableMobileTranslations = ['en', 'pl', 'nl', 'ro'];

        // The mobile page also accesses the desktop translation system.
        $availableDesktopTranslations = array_keys(I18n::getLanguagesFlagsData());

        // We can provide only those languages, which are available in both flavors.
        $availableTranslations = array_intersect(
            $availableDesktopTranslations,  // determines the order
            $availableMobileTranslations
        );

        $this->assign('languages', $availableTranslations);
    }
}
