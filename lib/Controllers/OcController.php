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

        d($locale, $putenv, $setlocale);

        bindtextdomain("medals", __DIR__.'/../languages');
        bind_textdomain_codeset('medals', 'UTF-8');
        textdomain("medals");
        print gettext('level');
        print '<BR>';
        /* end lang test*/

        $ocConfig = \lib\Objects\OcConfig\OcConfig::Instance();


        $smarty = new \Smarty();
        $smarty->setCompileDir($ocConfig->getDynamicFilesPath().'tmp/templates_c');
        $smarty->debugging = false;
        $smarty->caching = false;
        $smarty->setTemplateDir(__DIR__.'/../../tpl/smarty');
        $smarty->setCacheDir($ocConfig->getDynamicFilesPath().'tmp/smarty_cache');


        $user = new \lib\Objects\User\User($this->request['userId']);
        d($user, $user->getMedals());
         /* @var $medal \lib\Objects\Medals\Medal */
        foreach ($user->getMedals() as $medal) {
            $smartyMedals['medals'][] = array(
                'imgSrc' => $medal->getImage(),
                'name' => $medal->getName(),
                'level' => $medal->getLevel(),
            );
        }




        $smartyMedals['tr']['level'] = _('level');
        $smartyMedals['tr']['user']  = _('user');
        !d($smartyMedals['tr']);




        $smarty->assign("smartyMedals", $smartyMedals);
        $smarty->display('medals.tpl');
        

    }
}
