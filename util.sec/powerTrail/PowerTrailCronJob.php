<?php

require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

use lib\Controllers\PowerTrailController;
use Utils\I18n\I18n;

$powerTrailCronJobController = new PowerTrailCronJobController;
$powerTrailCronJobController->run();

/**
 * Description of PowerTrailCronJob
 *
 * @author Łza
 */
class PowerTrailCronJobController
{

    public function run()
    {
        $this->cleanPowerTrails();
        $this->generatePowerTrailOfTheDay();
    }

    private function generatePowerTrailOfTheDay()
    {
        include __DIR__ . '/../../lib/settings.inc.php';
        include_once __DIR__ . '/../../lib/language.inc.php';
        $langArray = I18n::getSupportedTranslations();


        $oldFileArr = explode('xxkgfj8ipzxx', file_get_contents($dynstylepath . 'ptPromo.inc-' . $lang . '.php'));
        $region = new GetRegions();
        $newPt = powerTrailBase::writePromoPt4mainPage($oldFileArr[1]);
        $regions = $region->GetRegion($newPt['centerLatitude'], $newPt['centerLongitude']);
        foreach ($langArray as $language) {
            $this->makePtContent($newPt, $language, $dynstylepath, $regions);
        }
    }

    /**
     * henerate html cache files used for displaying powerTrail of the day.
     *
     * @param type $newPt
     * @param type $langTr
     * @param type $dynstylepath
     * @param type $regions
     */
    private function makePtContent($newPt, $langTr, $dynstylepath, $regions)
    {
        $fileContent = '<span style="display:none" id="ptPromoId">xxkgfj8ipzxx' . $newPt['id'] . 'xxkgfj8ipzxx</span>';
        $fileContent .= '<table class="ptPromo-table"><tr><td style="padding-left: 15px; padding-right: 10px;">';
        if ($newPt['image'] != '') {
            $fileContent .= '<img height="50" src="' . $newPt['image'] . '" alt="PowerTrail Logo">';
        } else {
            $fileContent .= '<img height="50" src="tpl/stdstyle/images/blue/powerTrailGenericLogo.png" alt="PowerTrail Logo">';
        }
        $fileContent .= '</td><td style="width: 70%; padding-left: 10px; padding-right: 10px;"><a href="powerTrail.php?ptAction=showSerie&ptrail=' . $newPt['id'] . '" class="links">' . $newPt['name'] . '</a>';
        if ($regions) {
            $fileContent .= '<br><span class="content-title-noshade">' . tr2($regions['code1'], $langTr) . ' > ' . $regions['adm3'] . '</span>';
        }
        $fileContent .= '</td><td><strong>' . $newPt['cacheCount'] . '</strong>&nbsp;' . tr2('pt138', $langTr) . ', <strong>' . round($newPt['points'], 2) . '</strong>&nbsp;' . tr2('pt038', $langTr);
        $fileContent .= '</td></tr></table>';
        file_put_contents($dynstylepath . 'ptPromo.inc-' . $langTr . '.php', $fileContent);
    }

    private function cleanPowerTrails()
    {
        $powerTrailController = new PowerTrailController();
        $powerTrailController->cleanPowerTrailsCronjob();
    }

}
