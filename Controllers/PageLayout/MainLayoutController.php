<?php

namespace Controllers\PageLayout;

use Controllers\BaseController;
use Utils\DateTime\Year;
use Utils\I18n\I18n;
use Utils\Uri\Uri;
use lib\Objects\Admin\GeoCacheApproval;
use lib\Objects\Admin\ReportCommons;
use lib\Objects\GeoCache\PrintList;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\UserAuthorization;
use Utils\Cache\OcMemCache;
use Utils\I18n\CrowdinInContextMode;

/**
 * This controller prepares common data used by almost every page at oc
 * It should be call internal from VIEW class or something so there is no need to call it intentionally.
 *
 */
class MainLayoutController extends BaseController
{

    const MAIN_TEMPLATE = 'common/mainLayout';
    const MINI_TEMPLATE = 'common/miniLayout';


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

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // this controller is for internal use only and shouldn't be call by router
        return false;
    }

    public function index()
    {}


    /**
     * Prepare everything what is neede for display almost every page at OC
     */
    private function initMainLayout()
    {
        global $config; //TODO: refactor


        if ($this->isUserLogged()) {
            $this->view->setVar('_isUserLogged', true);
            $this->view->setVar('_username', $this->loggedUser->getUserName());
            // GDPR check and prepare template
            if (new \DateTime() > new \DateTime("2018-05-25 00:00:00") && ! $this->loggedUser->areRulesConfirmed()) {
                $this->view->setShowGdprPage(true);
                $this->view->setVar('_currentUri', urlencode(Uri::getCurrentUri(true)));
                $this->view->setVar('_wikiLinkRules', $this->ocConfig->getWikiLink('rules'));
                $this->view->setVar('_wikiLinkPrivacyPolicy',
                    (empty($this->ocConfig->getWikiLinks()['privacyPolicy'])) ? null : $this->ocConfig->getWikiLink('privacyPolicy'));
                $this->view->setVar('_ocTeamEmail', $this->ocConfig->getOcteamEmailAddress());
            }
        } else {
            $this->view->setVar('_isUserLogged', false);
            $this->view->setVar('_target',Uri::getCurrentUri(true));
        }

        $this->view->setVar('_siteName', $config['siteName']);
        $this->view->setVar('_favicon', '/images/'.$config['headerFavicon']);
        $this->view->setVar('_appleLogo', $config['header']['appleLogo']);

        $this->view->setVar('_title', "TODO-title"); //TODO!
        $this->view->setVar('_backgroundSeason', $this->view->getSeasonCssName());

        $this->view->setVar('_showVideoBanner', $this->view->showVideoBanner());
        if ($this->view->showVideoBanner()) {
            $this->view->setVar('_topBannerTxt', $this->ocConfig->getTopBannerTxt());
            $this->view->setVar('_topBannerVideo', $this->ocConfig->getTopBannerVideo());
            $this->view->loadJQuery();
            $this->view->addLocalCss(
                    Uri::getLinkWithModificationTime('/tpl/stdstyle/js/slick/slick.css'));
            $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/js/slick/slick-theme.css'));
            $this->view->addLocalJs(
                    Uri::getLinkWithModificationTime('/tpl/stdstyle/js/slick/slick.min.js'));

        }

        if (!$this->legacyLayout) {
            $this->view->addLocalCss(Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/common/mainLayout.css'));
        }

        if (Year::isPrimaAprilisToday()) {
            // add rythm JS
            $this->view->addLocalJs(
                Uri::getLinkWithModificationTime(
                    '/tpl/stdstyle/common/primaAprilis/rythm.min.js'));
                //'https://cdnjs.cloudflare.com/ajax/libs/rythm.js/2.2.3/rythm.min.js');

            $this->view->addLocalJs(Uri::getLinkWithModificationTime(
                '/tpl/stdstyle/common/primaAprilis/rythmOc.js'));

            $this->view->addLocalJs(Uri::getLinkWithModificationTime(
                '/vendor/js-cookie/js-cookie/src/js.cookie.js'));

        }

        if (Year::isPrimaAprilisToday()) {
            $this->view->loadJQuery();
            $logo = $config['headerLogo'];
            $logoTitle = 'discoCaching';
            $logoSubtitle = 'The first discoCaching site!';
        } else if (date('m') == 12 || date('m') == 1) {
            $logo = $config['headerLogoWinter'];
            $logoTitle = tr('oc_on_all_pages_top_' . $config['ocNode']);
            $logoSubtitle = tr('oc_subtitle_on_all_pages_' . $config['ocNode']);
        } else {
            $logo = $config['headerLogo'];
            $logoTitle = tr('oc_on_all_pages_top_' . $config['ocNode']);
            $logoSubtitle = tr('oc_subtitle_on_all_pages_' . $config['ocNode']);
        }

        $this->view->setVar('_mainLogo', '/images/'.$logo);
        $this->view->setVar('_logoTitle', $logoTitle);
        $this->view->setVar('_logoSubtitle', $logoSubtitle);

        $this->view->setVar('_languageFlags',
            I18n::getLanguagesFlagsData(I18n::getCurrentLang()));

        $this->view->setVar('_crowdinInContextEnabled', CrowdinInContextMode::enabled());
        // CrowdinInContext mode is enabled by setting var in uri
        $this->view->setVar('_crowdinInContextActionUrl', Uri::setOrReplaceParamValue(CrowdinInContextMode::VAR_NAME, true));


        $this->view->setVar('_qSearchByOwnerEnabled', $config['quick_search']['byowner']);
        $this->view->setVar('_qSearchByFinderEnabled', $config['quick_search']['byfinder']);
        $this->view->setVar('_qSearchByUserEnabled', $config['quick_search']['byuser']);

        $onlineUsers = self::getOnlineUsers();
        if (! empty($onlineUsers)) {
            $this->view->setVar('_displayOnlineUsers', $config['mainLayout']['displayOnlineUsers']);
            $this->view->setVar('_onlineUsers', $onlineUsers->listOfUsers);
        } else {
            $this->view->setVar('_displayOnlineUsers', false);
        }

        $this->initMenu();

        if (isset($config['license_html'])) {
            $this->view->setVar('licenseHtml', $config['license_html']);
        } else {
            $this->view->setVar('licenseHtml', '');
        }
    }

    private function initMenu()
    {

        if (! $this->isUserLogged()) {
            // user not authorized
            $this->view->setVar('_isAdmin', false);
            $this->view->setVar('_nonAuthUserMenu',
                $this->getMenu(OcConfig::MENU_NON_AUTH_USER));
        } else {
            // user authorized
            $this->view->setVar('_authUserMenu',
                $this->getMenu(OcConfig::MENU_AUTH_USER));

            // custom user menu
            $this->view->setVar('_customUserMenu',
                $this->getMenu(OcConfig::MENU_CUSTOM_USER));


            if ($this->loggedUser->hasOcTeamRole()) {
                $this->view->setVar('_isAdmin', true);
                $this->view->setVar('_adminMenu',
                    $this->getMenu(OcConfig::MENU_ADMIN_PREFIX));
            } else {
                $this->view->setVar('_isAdmin', false);
                $this->view->setVar('_adminMenu',null);
            }
        }

        $this->view->setVar('_menuBar',
            $this->getMenu(OcConfig::MENU_HORIZONTAL_BAR));

        $this->view->setVar('_footerMenu',
            $this->getMenu(OcConfig::MENU_FOOTER_PREFIX));

        $this->view->setVar('_additionalMenu',
            $this->getMenu(OcConfig::MENU_ADDITIONAL_PAGES));

    }

    /**
     * Prepare the admin menu. Admin menu has counters for special menu entries
     * so it needs special processing.
     *
     * @return array - $key->$url style of menu
     */
    private function adminMenuHandler(&$key, &$url)
    {
        switch ($key) {
            case 'mnu_reports':
                // add new/active reports counters
                $new_reports = ReportCommons::getReportsCountByStatus(ReportCommons::STATUS_NEW);
                $active_reports = ReportCommons::getReportsCountByStatus(ReportCommons::STATUS_OPEN);
                $key = tr($key) . " ($new_reports/$active_reports)";

                break;
            case 'mnu_pendings':

                $new_pendings = GeoCacheApproval::getWaitingForApprovalCount();
                if ($new_pendings > 0) {
                    $in_review_count = GeoCacheApproval::getInReviewCount();
                    $waitingForAssigne = $new_pendings - $in_review_count;
                    $key = tr($key) . " ($waitingForAssigne/$new_pendings)";
                } else {
                    $key = tr($key);
                }
                break;
            default:
                $key = tr($key); // by default menu key is just a translation
        }
    }

    /**
     * Prepare the horizontal menu bar.
     *
     * @return array - $key->$url style of menu
     */
    private function horizontalMenuHandler(&$key, &$url)
    {
        switch ($key) {
            case 'mnu_clipboard':
                // add number of caches in clipboard
                if (! empty(PrintList::GetContent()) ) {
                    $cachesInClipboard = count(PrintList::GetContent());
                    $key = tr($key)." ($cachesInClipboard)";
                } else {
                    $key = ''; //empty link
                    $url = '';
                }
                break;
            default:
                $key = tr($key); // by default menu key is just a translation
        }
    }

    /**
     * Prepare the auth user menu.
     *
     * @return array - $key->$url style of menu
     */
    private function authUserMenuHandler(&$key, &$url)
    {
        switch ($key) {
            case 'mnu_geoPaths':
                // disable geopaths link if disabled in config
                if (! OcConfig::isPowertrailsEnabled() ) {
                    $url = '';
                    break;
                }
            default:
                $key = tr($key); // by default menu key is just a translation
        }
    }

    private function getMenu($menuPrefix)
    {
        $menu = [];
        foreach (OcConfig::getMenu($menuPrefix) as $key => $url) {

            switch ($menuPrefix) {
                case OcConfig::MENU_ADMIN_PREFIX:
                    $this->adminMenuHandler($key, $url);
                    break;

                case OcConfig::MENU_HORIZONTAL_BAR:
                    $this->horizontalMenuHandler($key, $url);
                    break;

                case OcConfig::MENU_AUTH_USER:
                    $this->authUserMenuHandler($key, $url);
                    break;

                default:
                    $key = tr($key);
                    if (!is_array($url)) {
                        $url = htmlspecialchars($url);
                    }
            }

            if (empty($url)) {
                continue;
            }

            $menu[$key] = $url;
        }
        return $menu;
    }

    /**
     * Returns online users list stored in cache
     * @return array|mixed
     */
    private function getOnlineUsers()
    {
        global $config;

        if (! $config['mainLayout']['displayOnlineUsers']) {
            // skip this action if online users list is disabled in config
            return null;
        }

        return OcMemCache::getOrCreate(__METHOD__, 5*60, function() {
            $obj = new \stdClass();
            $obj->listOfUsers = UserAuthorization::getOnlineUsersFromDb();
            $obj->validAt = time();
            return $obj;
        });
    }

}