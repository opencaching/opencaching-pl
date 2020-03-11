<?php
/** @noinspection PhpUnused */

namespace src\Controllers;

use src\Models\ChunkModels\ListOfCaches\Column_CacheGeoKretIconObject;
use src\Models\ChunkModels\ListOfCaches\Column_CacheLastLogObject;
use src\Models\ChunkModels\ListOfCaches\Column_CacheNameObject;
use src\Models\ChunkModels\ListOfCaches\Column_CacheRegionObject;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIconObject;
use src\Models\ChunkModels\ListOfCaches\Column_DateTime;
use src\Models\ChunkModels\ListOfCaches\Column_GeoPathIconObject;
use src\Models\ChunkModels\ListOfCaches\Column_SimpleText;
use src\Models\ChunkModels\ListOfCaches\Column_UserNameObject;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\PaginationModel;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\MultiCacheStats;

class CacheController extends BaseController
{

    const CACHES_PER_NEW_CACHES_PAGE = 50;

    public function index()
    {
        // Simple redirect for now.
        $this->view->redirect('/');
    }

    /**
     * List of all new caches in OC country (excluding events)
     */
    public function newCaches()
    {
        if (sizeof($this->ocConfig->getSitePrimaryCountriesList()) > 1) {
            $this->newCachesMultipleCountries();
        } else {
            $this->newCachesOneCountry();
        }

        $this->view->addLocalCss('/css/lightTooltip.css')
            ->buildView();

    }

    private function newCachesOneCountry() {
        $newCachesCount = MultiCacheStats::getLatestNationalCachesCount();

        $pagination = new PaginationModel(self::CACHES_PER_NEW_CACHES_PAGE);
        $pagination->setRecordsCount($newCachesCount);
        list($limit, $offset) = $pagination->getQueryLimitAndOffset();

        $model = new ListOfCachesModel();
        $model->addColumn(new Column_DateTime(tr('cs_publicationDate'),
            function (GeoCache $row) {
                return [
                    'date' => $row->getDatePublished(),
                    'showTime' => false
                ];
            }))
            ->addColumn(new Column_CacheGeoKretIconObject(''))
            ->addColumn(new Column_GeoPathIconObject(''))
            ->addColumn(new Column_CacheTypeIconObject(''))
            ->addColumn(new Column_CacheNameObject(tr('cache')))
            ->addColumn(new Column_UserNameObject(tr('creator'),
                function (GeoCache $row) {
                    return $row->getOwner();
                }))
            ->addColumn(new Column_CacheRegionObject(tr('region')))
            ->addColumn(new Column_CacheLastLogObject(tr('new_logs')))
            ->setPaginationModel($pagination)
            ->addDataRows(MultiCacheStats::getLatestNationalCachesForUserOneCountry($this->loggedUser, $limit, $offset));

        $this->view->setVar('listCacheModel', $model)
            ->setVar('cachesCount', $newCachesCount)
            ->setTemplate('cache/newCachesOneCountry');
    }

    private function newCachesMultipleCountries() {
        $cacheList = MultiCacheStats::getLatestNationalCachesForUserMultiCountries($this->loggedUser);
        $modelArray = [];
        foreach ($cacheList as $country => $caches) {
            $model = new ListOfCachesModel();
            $model->addColumn(new Column_DateTime(tr('cs_publicationDate'),
                function (GeoCache $row) {
                    return [
                        'date' => $row->getDatePublished(),
                        'showTime' => false
                    ];
                }))
                ->addColumn(new Column_CacheGeoKretIconObject(''))
                ->addColumn(new Column_GeoPathIconObject(''))
                ->addColumn(new Column_CacheTypeIconObject(''))
                ->addColumn(new Column_CacheNameObject(tr('cache')))
                ->addColumn(new Column_UserNameObject(tr('creator'),
                    function (GeoCache $row) {
                        return $row->getOwner();
                    }))
                ->addColumn(new Column_CacheRegionObject(tr('region')))
                ->addColumn(new Column_CacheLastLogObject(tr('new_logs')))
                ->addDataRows($caches);
            $modelArray[$country] = $model;
            unset($model);
        }

        $this->view->setVar('listCacheModelArray', $modelArray)
            ->setTemplate('cache/newCachesMultipleCountries');
    }

    /**
     * List of all new foreign caches
     */
    public function newForeignCaches()
    {
        $cacheList = MultiCacheStats::getLatestForeignCachesForUser($this->loggedUser);
        $modelArray = [];
        foreach ($cacheList as $country => $caches) {
            $model = new ListOfCachesModel();
            $model->addColumn(new Column_DateTime(tr('cs_publicationDate'),
                function (GeoCache $row) {
                    return [
                        'date' => $row->getDatePublished(),
                        'showTime' => false
                    ];
                }))
                ->addColumn(new Column_CacheGeoKretIconObject(''))
                ->addColumn(new Column_GeoPathIconObject(''))
                ->addColumn(new Column_CacheTypeIconObject(''))
                ->addColumn(new Column_CacheNameObject(tr('cache')))
                ->addColumn(new Column_UserNameObject(tr('creator'),
                    function (GeoCache $row) {
                        return $row->getOwner();
                    }))
                ->addColumn(new Column_CacheRegionObject(tr('region')))
                ->addColumn(new Column_CacheLastLogObject(tr('new_logs')))
                ->addDataRows($caches);
            $modelArray[$country] = $model;
            unset($model);
        }

        $this->view->setVar('listCacheModelArray', $modelArray)
            ->addLocalCss('/css/lightTooltip.css')
            ->setTemplate('cache/newForeignCaches')
            ->buildView();
    }

    /**
     * List of incoming events
     */
    public function incomingEvents()
    {
        $eventList = MultiCacheStats::getLatestEventsForUser($this->loggedUser);
        $modelArray = [];
        foreach ($eventList as $location => $caches) {
            $model = new ListOfCachesModel();
            $model->addColumn(new Column_DateTime(tr('date'),
                function (GeoCache $row) {
                    return [
                        'date' => $row->getDatePlaced(),
                        'showTime' => false
                    ];
                }))
                ->addColumn(new Column_CacheNameObject(tr('event')))
                ->addColumn(new Column_UserNameObject(tr('creator'),
                    function (GeoCache $row) {
                        return $row->getOwner();
                    }))
                ->addColumn(new Column_CacheLastLogObject(tr('new_logs')))
                ->addDataRows($caches);
            $modelArray[$location] = $model;
            unset($model);
        }

        $this->view->setVar('listCacheModelArray', $modelArray)
            ->addLocalCss('/css/lightTooltip.css')
            ->setTemplate('cache/incomingEvents')
            ->buildView();
    }

    /**
     * List of titled caches
     */
    public function titled()
    {
        $titledCachesCount = MultiCacheStats::getTitledCount();

        $pagination = new PaginationModel(self::CACHES_PER_NEW_CACHES_PAGE);
        $pagination->setRecordsCount($titledCachesCount);
        list($limit, $offset) = $pagination->getQueryLimitAndOffset();

        $model = new ListOfCachesModel();
        $model->addColumn(new Column_DateTime(tr('titled_cache_date'),
            function (array $row) {
                return [
                    'date' => $row['date'],
                    'showTime' => false
                ];
            }))
            ->addColumn(new Column_CacheGeoKretIconObject('',
                function (array $row) {
                    return $row['cache'];
                }))
            ->addColumn(new Column_GeoPathIconObject('',
                function (array $row) {
                    return $row['cache'];
                }))
            ->addColumn(new Column_CacheTypeIconObject('',
                function (array $row) {
                    return $row['cache'];
                }))
            ->addColumn(new Column_CacheNameObject(tr('cache'),
                function (array $row) {
                    return $row['cache'];
                }))
            ->addColumn(new Column_UserNameObject(tr('creator'),
                function (array $row) {
                    return $row['cache']->getOwner();
                }))
            ->addColumn(new Column_CacheRegionObject(tr('region'),
                function (array $row) {
                    return $row['cache'];
                }))
            ->addColumn(new Column_CacheLastLogObject(tr('new_logs'),
                function (array $row) {
                    return $row['cache'];
                }))
            ->setPaginationModel($pagination)
            ->addDataRows(MultiCacheStats::getTitledCachesForUser($this->loggedUser, $limit, $offset));

        $pageTitle = ($this->ocConfig->getTitledCachePeriod() == 'week') ? 'week_titled_caches' : 'month_titled_caches';
        $this->view->setVar('listCacheModel', $model)
            ->setVar('cachesCount', $titledCachesCount)
            ->setVar('pageTitle', $pageTitle)
            ->addLocalCss('/css/lightTooltip.css')
            ->setTemplate('cache/titledCaches')
            ->buildView();
    }

    /**
     * List of recommended caches
     */
    public function recommended()
    {
        $this->redirectNotLoggedUsers();

        $cachesCount = MultiCacheStats::getRecommendedCount();

        $pagination = new PaginationModel(self::CACHES_PER_NEW_CACHES_PAGE);
        $pagination->setRecordsCount($cachesCount);
        list($limit, $offset) = $pagination->getQueryLimitAndOffset();

        $model = new ListOfCachesModel();
        $model->addColumn(new Column_SimpleText(ucfirst(tr('recommendations')),
            function (GeoCache $row) {
                return $row->getRecommendations();
            }))
            ->addColumn(new Column_CacheGeoKretIconObject(''))
            ->addColumn(new Column_GeoPathIconObject(''))
            ->addColumn(new Column_CacheTypeIconObject(''))
            ->addColumn(new Column_CacheNameObject(tr('cache')))
            ->addColumn(new Column_UserNameObject(tr('creator'),
                function (GeoCache $row) {
                    return $row->getOwner();
                }))
            ->addColumn(new Column_CacheRegionObject(tr('region')))
            ->addColumn(new Column_CacheLastLogObject(tr('new_logs')))
            ->setPaginationModel($pagination)
            ->addDataRows(MultiCacheStats::getRecommendedCaches($limit, $offset));

        $this->view->setVar('listCacheModel', $model)
            ->setVar('cachesCount', $cachesCount)
            ->addLocalCss('/css/lightTooltip.css')
            ->setTemplate('cache/recommended')
            ->buildView();
    }

    /**
     * @inheritDoc
     */
    public function isCallableFromRouter($actionName)
    {
        return true;
    }
}