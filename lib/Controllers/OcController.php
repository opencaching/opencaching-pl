<?php

namespace lib\Controllers;

require_once __DIR__ . '/../ClassPathDictionary.php';
require_once __DIR__ . '/../kint/Kint.class.php'; /* magnificant debug tool */

/**
 * Description of OcController
 *
 * @author Łza
 */
class OcController
{
    private $request;

    public function run($request)
    {
        $this->request = $request;

        switch ($request['action']) {
            case 'userMedals':
                return $this->userMedals();
            default:
                break;
        }
    }

    private function userMedals()
    {


        /* lang test */
        /* get locale from browser */
        $userPrefferedLanguages = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $mostPrefferedLanguage = explode(',' , $userPrefferedLanguages[0]);
        $locale = str_replace('-', '_', $mostPrefferedLanguage[0]);


        if(isset($_REQUEST['locale'])){ /* get locale from $_REQUEST*/
            $locale = $_REQUEST['locale'];
        }

        $putenv = putenv("LANG=$locale");
        $setlocale = setlocale(LC_ALL, $locale);

//        d($locale, $putenv, $setlocale);

        bindtextdomain("medals", __DIR__.'/../languages');
        bind_textdomain_codeset('medals', 'UTF-8');
        textdomain("medals");
//        print gettext('level');
//        print '<BR>';
        /* end lang test*/

        $ocConfig = \lib\Objects\OcConfig\OcConfig::Instance();


        $smarty = new \Smarty();
        $smarty->setCompileDir($ocConfig->getDynamicFilesPath().'tmp/templates_c');
        $smarty->debugging = false;
        $smarty->caching = false;
        $smarty->setTemplateDir(__DIR__.'/../../tpl/smarty');
        $smarty->setCacheDir($ocConfig->getDynamicFilesPath().'tmp/smarty_cache');


        $user = new \lib\Objects\User\User($this->request['userId']);
//        d($user, $user->getMedals());
        /* @var $medal \lib\Objects\Medals\Medal */
        foreach ($user->getMedals() as $medal) {
            $medal->checkConditionsForUser($user);
            $smartyMedals['medals'][] = array(
                'imgSrc' => $medal->getImage(),
                'name' => $medal->getName(),
                'profile' => $medal->getMedalProfile(),
                'level' => $medal->getLevel(),
                'levelName' => $medal->getLevelName(),
                'currentLevelInfo' => $medal->getLevelInfo(),
                'nextLevelInfo' => $medal->getLevelInfo($medal->getLevel()+1),
            );

        }

        $smartyMedals['tr'] = array(
            'level' => _('level'),
            'user'  => _('user'),
            'medals' => _('medals'),
            'nextLevelRequirements' => _('Next level Requirements'),
            'currentLevelRequirements' => _('Level achievements'),
            'medalInfo' => _('Medal Profile'),
            'cacheTypes' => _('Geocache types'),
        );


        $smarty->assign('geocacheIcons', \cache::getCacheIconsSet());
        $smarty->assign('user', $user->getUserInformation());
        $smarty->assign("smartyMedals", $smartyMedals);
        $smarty->assign("bgImage", $this->shuffleBackgroundImage());
        $smarty->display('medals.tpl');
        

    }

    /**
     * TODO: move it to tpl.
     */
    private function shuffleBackgroundImage(){
       $month = date('m');
       switch ($month){
           case 1: return 'Bredles_y_l_Odles_cun_Sas_Rigais_dinviern.jpg';
           case 2: return 'Ciampani_dl_Sela_Sellaturme_Sas_Pordoi_Sellajoch_Schutzhutte.jpg';
           case 3: return 'Gamsblut_Sankt_Christina.jpg';
           case 4: return 'Stlupec_sun_Resciesa.jpg';
           case 5: return 'Lilium_Martagon_in_Groden_Jender.jpg';
           case 6: return 'Creusc_sun_Col_dala_Piere_Gherdeina.jpg';
           case 7: return 'Cansla_dla_Charita_Pic_Gherdeina.jpg';
           case 8: return 'Bryce_Canyon_USA_october_2012_e.jpg';
           case 9: return 'Desert_in_Utah_by_Wolfgang_Moroder.jpg';
           case 10: return 'Mont_de_Seuc_y_l_Saslong_da_Cod_dal_Fil.jpg';
           case 11: return 'Saslong_downhill_race_track_in_Val_Gardena.jpg';
           case 12: return 'Lock_Zug_Groden.jpg';
       }

    }
}
