<?php

require_once __DIR__ . '/../../lib/ClassPathDictionary.php';

use lib\Controllers\PowerTrailController;

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
        $langArray = available_languages();


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
        $fileContent .= '<table style="width: 100%;"><tr><td style="padding-left: 10px;padding-right: 10px;">';
        if ($newPt['image'] != '') {
            $fileContent .= '<img height="50" src="' . $newPt['image'] . '" alt="">';
        } else {
            $fileContent .= '<img height="50" src="tpl/stdstyle/images/blue/powerTrailGenericLogo.png" alt="">';
        }
        $fileContent .= '</td><td style="width: 50%; font-size: 13px; padding-left: 10px; padding-right: 10px; vertical-align: center;"><a href="powerTrail.php?ptAction=showSerie&ptrail=' . $newPt['id'] . '">' . $newPt['name'] . '</a>';
        $fileContent .= '<td style="font-size: 13px; vertical-align: center;"><b>' . $newPt['cacheCount'] . '</b>&nbsp;' . tr2('pt138', $langTr) . ', <b>' . round($newPt['points'], 2) . '</b>&nbsp;' . tr2('pt038', $langTr);
        if ($regions) {
            $fileContent .= '</td><td style="font-size: 12px; vertical-align: center;">' . tr2($regions['code1'], $langTr) . '>' . $regions['adm3'];
        }
        $fileContent .= '</td></tr></table>';
        file_put_contents($dynstylepath . 'ptPromo.inc-' . $langTr . '.php', $fileContent);
    }

    private function cleanPowerTrails()
    {
        $powerTrailController = new PowerTrailController();
        $powerTrailController->cleanPowerTrailsCronjob();
    }

}
