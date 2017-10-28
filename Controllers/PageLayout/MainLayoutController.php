<?php

namespace Controllers\PageLayout;

use Controllers\BaseController;
use Controllers\Cron\OnlineUsersController;
use Utils\DateTime\Year;
use Utils\I18n\I18n;
use Utils\Uri\Uri;
use Controllers\ConfigController;
use lib\Objects\Admin\GeoCacheApproval;
use lib\Objects\Admin\ReportCommons;

class MainLayoutController extends BaseController
{

    const MAIN_TEMPLATE = 'common/mainLayout';

    private $legacyLayout = false;

    public static function init()
    {
        $main = new self();
        $main->initMainLayout();
    }

    public static function initLegacy()
    {
        $main = new self();
        $main->legacyLayout = true;
        $main->initMainLayout();
    }

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {}


    private function initMainLayout()
    {
        global $config; //TODO: refactor


        if($this->isUserLogged()){
            $this->view->setVar('_isUserLogged', true);
            $this->view->setVar('_username', $this->loggedUser->getUserName());
        }else{
            $this->view->setVar('_isUserLogged', false);
            $this->view->setVar('_target',Uri::getCurrentUri(true));
        }


        $this->view->setVar('_siteName', $config['siteName']);
        $this->view->setVar('_keywords', $config['header']['keywords']);
        $this->view->setVar('_favicon', '/images/'.$config['headerFavicon']);
        $this->view->setVar('_appleLogo', $config['header']['appleLogo']);

        $this->view->setVar('_title', "TODO-title"); //TODO!
        $this->view->setVar('_backgroundSeason', $this->view->getSeasonCssName());

        if(!$this->legacyLayout){
            $this->view->addLocalCss(Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/common/mainLayout.css'));
        }

        if(Year::isPrimaAprilisToday()){
            $logo = $config['headerLogo1stApril'];
            $logoTitle = tr('oc_on_all_pages_top_1A');
            $logoSubtitle = tr('oc_subtitle_on_all_pages_1A');
        }else if(date('m') == 12 || date('m') == 1){
            $logo = $config['headerLogoWinter'];
            $logoTitle = tr('oc_on_all_pages_top_' . $config['ocNode']);
            $logoSubtitle = tr('oc_subtitle_on_all_pages_' . $config['ocNode']);
        }else{
            $logo = $config['headerLogo'];
            $logoTitle = tr('oc_on_all_pages_top_' . $config['ocNode']);
            $logoSubtitle = tr('oc_subtitle_on_all_pages_' . $config['ocNode']);
        }

        $this->view->setVar('_mainLogo', '/images/'.$logo);
        $this->view->setVar('_logoTitle', $logoTitle);
        $this->view->setVar('_logoSubtitle', $logoSubtitle);

        $this->view->setVar('_languageFlags',
            I18n::getLanguagesFlagsData($this->view->getLang()));


        $this->view->setVar('_qSearchByOwnerEnabled', $config['quick_search']['byowner']);
        $this->view->setVar('_qSearchByFinderEnabled', $config['quick_search']['byfinder']);
        $this->view->setVar('_qSearchByUserEnabled', $config['quick_search']['byuser']);

        $onlineUsers = OnlineUsersController::getOnlineUsers();
        if(!empty($onlineUsers)){
            $this->view->setVar('_displayOnlineUsers', $config['mainLayout']['displayOnlineUsers']);
            $this->view->setVar('_onlineUsers', OnlineUsersController::getOnlineUsers());
        }else{
            $this->view->setVar('_displayOnlineUsers', false);
        }

        if($this->isUserLogged() && $this->loggedUser->isAdmin()){
            $this->view->setVar('_adminMenu', $this->getAdminMenu());
        }else{
            $this->view->setVar('_adminMenu',null);
        }

        $this->view->setVar('_footerMenu', ConfigController::getFooterMenu());

        if(isset($config['license_html'])){
            $this->view->setVar('licenseHtml', $config['license_html']);
        }else{
            $this->view->setVar('licenseHtml', '');
        }
    }

    private function getAdminMenu()
    {

        $menu = ConfigController::getAdminMenu();

        if(isset($menu['reports'])){
            $new_reports = ReportCommons::getReportsCountByStatus(ReportCommons::STATUS_NEW);
            $active_reports = ReportCommons::getReportsCountByStatus(ReportCommons::STATUS_OPEN);



            $menu['reports']" (" . $new_reports . "/" . $active_reports . ")";

        }

        $new_pendings = GeoCacheApproval::getWaitingForApprovalCount();
        $in_review_count = GeoCacheApproval::getInReviewCount();


        /*

         $adminidx = mnu_MainMenuIndexFromPageId($menu, "admin/reports_list");

        $menu[$adminidx]['visible'] = false;

        $zgloszeniaidx = mnu_MainMenuIndexFromPageId($menu[$adminidx]["submenu"], "admin/reports_list");
        if ($active_reports > 0){
            $menu[$adminidx]["submenu"][$zgloszeniaidx]['menustring'] .=
            " (" . $new_reports . "/" . $active_reports . ")";
        }

        $zgloszeniaidx = mnu_MainMenuIndexFromPageId($menu[$adminidx]["submenu"], "viewpendings");
        if ($new_pendings > 0){
            $waitingForAssigne = $new_pendings - $in_review_count;
        } else {
            $waitingForAssigne = 0;
        }

        $menu[$adminidx]["submenu"][$zgloszeniaidx]['menustring'] .=
            " (" . $waitingForAssigne . "/" . $new_pendings .  ")";

        */

        return $menu;

    }

}
