<?php

namespace src\Controllers;

use RuntimeException;
use src\Controllers\Core\ViewBaseController;
use src\Models\CacheSet\CacheSet;
use src\Models\CacheSet\GeopathCandidate;
use src\Models\ChunkModels\ListOfCaches\Column_ActionButtons;
use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_CacheSetNameAndIcon;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_OnClickActionIcon;
use src\Models\ChunkModels\ListOfCaches\Column_SimpleText;
use src\Models\ChunkModels\ListOfCaches\Column_UserName;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\GeoCache\GeoCache;
use src\Models\User\MultiUserQueries;
use src\Utils\Text\Formatter;
use src\Utils\Text\UserInputFilter;

class GeoPathController extends ViewBaseController
{
    public function isCallableFromRouter(string $actionName): bool
    {
        return true;
    }

    public function index()
    {
        $this->searchByName(); // Temporary. To be removed in the future
    }

    /**
     * Search GeoPaths by name. Used by search engine in top line
     */
    public function searchByName()
    {
        if (isset($_REQUEST['name'])) {
            $searchStr = UserInputFilter::purifyHtmlString($_REQUEST['name']);
            $searchStr = strip_tags($searchStr);
        } else {
            $searchStr = null;
        }
        $this->view->setVar('geoPaths', CacheSet::getCacheSetsByName($searchStr));
        $this->view->setVar('searchStr', $searchStr);
        $this->view->setTemplate('geoPath/searchByName');
        $this->view->buildView();
    }

    public function acceptCacheCandidate($geopathId, $cacheId, $code)
    {
        $this->acceptCancelCandidate($geopathId, $cacheId, $code, true);
    }

    public function cancelCacheCandidate($geopathId, $cacheId, $code)
    {
        $this->acceptCancelCandidate($geopathId, $cacheId, $code, false);
    }

    private function acceptCancelCandidate($geopathId, $cacheId, $code, $proposalAccepted)
    {
        $this->redirectNotLoggedUsers();

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->displayCommonErrorPageAndExit('Unknown cache!');
        }

        $geoPath = CacheSet::fromCacheSetIdFactory($geopathId);

        if (! $geoPath) {
            $this->displayCommonErrorPageAndExit('There is no such geoPath');
        }

        if ($cache->isPowerTrailPart()) {
            $this->displayCommonErrorPageAndExit('This geocache is already part of the geopath!');
        }

        if (! $geoPath->isCandiddateCodeExists($cache, $code)) {
            $this->displayCommonErrorPageAndExit('There is no such proposal!');
        }

        // there was such proposal
        if ($proposalAccepted) {
            try {
                $geoPath->addCache($cache);
            } catch (RuntimeException $e) {
                $this->displayCommonErrorPageAndExit($e->getMessage());
            }
            // cache added to geopath - cancel all others proposals
            $geoPath->deleteCandidateCode($cache);
        } else {
            // cancel this proposal
            $geoPath->deleteCandidateCode($cache, $code);
        }

        $this->view->redirect($geoPath->getUrl());
    }

    /**
     * List of caches - candidates to given geopath
     *
     * @param $geopathId
     */
    public function candidatesList($geopathId)
    {
        $this->redirectNotLoggedUsers();

        if (! $geopath = CacheSet::fromCacheSetIdFactory($geopathId)) {
            $this->displayCommonErrorPageAndExit('No such geopath!');
        }

        if (! $geopath->isOwner($this->loggedUser)) {
            $this->displayCommonErrorPageAndExit('You are not an owner of this geopath!');
        }

        $this->view->setTemplate('geoPath/gpCandidatesList');
        $this->view->loadJQuery();
        $this->view->setVar('gp', $geopath);

        // init model for list of watched geopaths
        $listModel = new ListOfCachesModel();

        // rows to display
        $candidates = GeopathCandidate::getCacheCandidates($geopath);

        $userDict = [];

        foreach ($candidates as $candidate) {
            $userDict[$candidate->getGeoCache()->getOwnerId()] = null;
        }

        $userDict = MultiUserQueries::GetUserNamesForListOfIds(array_keys($userDict));

        $listModel->addDataRows(GeopathCandidate::getCacheCandidates($geopath));

        $listModel->addColumn(new Column_SimpleText(tr('gpCandidates_submitedDate'), function (GeopathCandidate $candidate) {
            return Formatter::date($candidate->getSubmitedDate());
        }, 'width15'));

        $listModel->addColumn(new Column_CacheTypeIcon('', function (GeopathCandidate $candidate) {
            $cache = $candidate->getGeoCache();

            return [
                'type' => $cache->getCacheType(),
                'status' => $cache->getStatus(),
                'user_sts' => null,
            ];
        }, 'width5'));

        $listModel->addColumn(new Column_CacheName(tr('gpCandidates_cacheName'), function (GeopathCandidate $candidate) {
            $cache = $candidate->getGeoCache();

            return [
                'cacheWp' => $cache->getWaypointId(),
                'cacheName' => $cache->getCacheName(),
                'isStatusAware' => true,
                'cacheStatus' => $cache->getStatus(),
            ];
        }));

        $listModel->addColumn(new Column_UserName(tr('gpCandidates_cacheOwner'), function (GeopathCandidate $candidate) use ($userDict) {
            $userId = $candidate->getGeoCache()->getOwnerId();

            return [
                'userId' => $userId,
                'userName' => $userDict[$userId],
            ];
        }));

        $listModel->addColumn(new Column_OnClickActionIcon(tr('gpCandidates_action'), function (GeopathCandidate $candidate) {
            return [
                'icon' => '/images/log/16x16-trash.png',
                'onClick' => 'cancelCandidateOffer(this, ' . $candidate->getId() . ')',
                'title' => tr('gpCandidates_cancelOffer'),
            ];
        }, 'width10'));

        $this->view->setVar('listModel', $listModel);

        $this->view->buildView();
    }

    /**
     * Display the lists of offers of cache adding to geopath for current user
     */
    public function myCandidates()
    {
        $this->redirectNotLoggedUsers();

        $this->view->loadJQuery();
        $this->view->setTemplate('geoPath/myCandidatesList');
        $this->view->setVar('user', $this->loggedUser);

        // init model for list of watched geopaths
        $listModel = new ListOfCachesModel();

        $listModel->addColumn(new Column_SimpleText(
            tr('gpMyCandidates_submitedDate'),
            function (GeopathCandidate $candidate) {
                return Formatter::date($candidate->getSubmitedDate());
            },
            'width15'
        ));

        $listModel->addColumn(new Column_CacheSetNameAndIcon(
            tr('gpMyCandidates_geopathName'),
            function (GeopathCandidate $candidate) {
                $gp = $candidate->getGeopath();

                return [
                    'type' => $gp->getType(),
                    'id' => $gp->getId(),
                    'name' => $gp->getName(),
                ];
            }
        ));

        $listModel->addColumn(new Column_CacheTypeIcon(
            '',
            function (GeopathCandidate $candidate) {
                $cache = $candidate->getGeoCache();

                return [
                    'type' => $cache->getCacheType(),
                    'status' => $cache->getStatus(),
                    'user_sts' => null,
                ];
            },
            'width5'
        ));

        $listModel->addColumn(new Column_CacheName(
            tr('gpMyCandidates_cacheName'),
            function (GeopathCandidate $candidate) {
                $cache = $candidate->getGeoCache();

                return [
                    'cacheWp' => $cache->getWaypointId(),
                    'cacheName' => $cache->getCacheName(),
                    'isStatusAware' => true,
                    'cacheStatus' => $cache->getStatus(),
                ];
            }
        ));

        $listModel->addColumn(new Column_ActionButtons(
            tr('gpMyCandidates_actions'),
            function (GeopathCandidate $candidate) {
                return [
                    [
                        'btnClasses' => 'btn-primary',
                        'btnText' => tr('gpMyCandidates_acceptOffer'),
                        'onClick' => 'acceptOffer(this, ' . $candidate->getId() . ')',
                        'title' => tr('gpMyCandidates_acceptOfferTitle'),
                    ],
                    [
                        'btnClasses' => '',
                        'btnText' => tr('gpMyCandidates_refuseOffer'),
                        'onClick' => 'refuseOffer(this, ' . $candidate->getId() . ')',
                        'title' => tr('gpMyCandidates_refuseOfferTitle'),
                    ],
                ];
            },
            'width25'
        ));

        // load rows to display
        $listModel->addDataRows(GeopathCandidate::getUserGeopathCandidates($this->loggedUser));
        $this->view->setVar('listModel', $listModel);

        $this->view->buildView();
    }
}
