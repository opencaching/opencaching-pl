<?php

namespace src\Controllers;

use src\Controllers\Core\ViewBaseController;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheDesc;
use src\Utils\I18n\Languages;
use stdClass;

class CacheDescController extends ViewBaseController
{
    public function __construct()
    {
        parent::__construct();

        // this controller is only for logged users
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return true;
    }

    public function index()
    {
        $this->displayCommonErrorPageAndExit('Is this a broken link?');
    }

    private function getAndCheckGeocache(string $cacheWp): GeoCache
    {
        // find cache object
        $geocache = GeoCache::fromWayPointFactory($cacheWp);

        if (! $geocache) {
            // there is no such geocache
            $this->displayCommonErrorPageAndExit('There is no such geocache?');
        }

        if (! $geocache->isOwnedBy($this->loggedUser) && ! $this->loggedUser->hasOcTeamRole()) {
            // only owner or OCTeam can edit cache desc.
            $this->displayCommonErrorPageAndExit('Not an owner tries to edit?');
        }

        return $geocache;
    }

    public function edit(string $cacheWp, string $descLang = null): void
    {
        // find cache object and check the privileges to edit it
        $geocache = $this->getAndCheckGeocache($cacheWp);

        // find right description
        $desc = GeoCacheDesc::fromCacheIdFactory($geocache->getCacheId(), $descLang);

        if (! $desc) {
            // there is no such geocache description - seems author want to add the new one
            $desc = GeoCacheDesc::getEmptyDesc($geocache);
        }

        // this page is part of editCache so use the same css here
        $this->view->addLocalCss('/views/editCache/editCache.css');
        $this->view->loadJQuery();

        $this->view->setVar('languages', $this->getLanguagesObj($desc));
        $this->view->setVar('cache', $geocache);
        $this->view->setVar('desc', $desc);

        // returnUrl is used to redirect to the page where the user was before editing the cache description
        $returnUrl = $_GET['returnUrl'] ?? $_SERVER['HTTP_REFERER'] ?? null;
        if ($returnUrl) {
            $parsed = parse_url($returnUrl);
            $returnUrl = ($parsed['path'] ?? '')
                . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
        }
        $this->view->setVar('returnUrl', $returnUrl);

        $this->view->setTemplate('cacheDescEdit/cacheDescEdit');
        $this->view->buildView();
    }

    private function getLanguagesObj(GeoCacheDesc $desc): array
    {
        $langs = [];
        $otherLangs = $desc->getListOfDescriptionLangs(true);

        foreach (Languages::getLanguages() as $lang) {
            if (in_array($lang['langCode'], $otherLangs)) {
                // skip langs for existent descriptions except this one
                continue;
            }

            $l = new stdClass();
            $l->code = $lang['langCode'];
            $l->localizedName = $lang['localizedName'];
            $l->default = $lang['defaultLang'];
            $l->selected = ($desc->getLang() == $lang['langCode']);
            $langs[] = $l;
        }

        return $langs;
    }

    /**
     * Save result of the cacheDescEdit form to DB
     *
     * @param string waypoint of the cache
     * @param int $descId
     */
    public function save(string $cacheWp, string $descLang): void
    {
        // find cache object and check the privileges to edit it
        $geocache = $this->getAndCheckGeocache($cacheWp);

        // find right description
        $desc = GeoCacheDesc::fromCacheIdFactory($geocache->getCacheId(), $descLang);

        if (! $desc) {
            // there is no such geocache description - seems author want to add the new one
            $desc = GeoCacheDesc::getEmptyDesc($geocache);
        }

        // now parse input data
        $newLang = $_POST['descLang'] ?? null;

        if (! $newLang) {
            $this->displayCommonErrorPageAndExit('No language param?');
        }

        if (! Languages::isLanguageSupported($newLang)) {
            $this->displayCommonErrorPageAndExit('Not supported language: ' . $newLang);
        }

        $desc->setLanguage($newLang);

        // desc / shortDesc / hints are an optional param
        $desc->setShortDesc($_POST['shortDesc'] ?? $desc->getShortDescRaw());

        if (strlen($_POST['descTxt'] ?? '') > GeoCacheDesc::MAX_DESC_SIZE) {
            $this->displayCommonErrorPageAndExit('The description is too long. Max size=' . GeoCacheDesc::MAX_DESC_SIZE);
        }
        $desc->setDesc($_POST['descTxt'] ?? $desc->getDescriptionRaw());

        $desc->setHint($_POST['hints'] ?? $desc->getHint());

        $reactivRules = $_POST['reactivRules'] ?? $desc->getReactivationRules();

        if ($reactivRules == 'Custom rulset') {
            // Custom rules option is selected - read rules textarea
            $reactivRules = $_POST['reactivRulesCustom'] ?? '';
        }
        $desc->setReactivationRule($reactivRules);

        // data is parsed - now prepare to save it to DB

        // if the language is changed check if this is not duplication of existing lang
        if ($desc->getLang() != $descLang) {
            // language is changed
            if ($desc->isLangDuplicated()) {
                $this->displayCommonErrorPageAndExit('Duplication of language - record with this language already exists');
            }
        }

        $desc->saveToDb();

        $this->view->redirect($_POST['returnUrl'] ?? $geocache->getCacheUrl());
    }
}
