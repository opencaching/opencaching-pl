<?php
namespace src\Controllers;

use src\Models\GeoCache\PrintList;
use src\Models\GeoCache\MultiCacheStats;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Models\ChunkModels\ListOfCaches\Column_CacheTypeIcon;
use src\Models\ChunkModels\ListOfCaches\Column_CacheName;
use src\Models\ChunkModels\ListOfCaches\Column_OnClickActionIcon;

use src\Models\GeoCache\GeoCache;


class PrintListController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {
        $this->view->loadJQuery();
        $this->view->setTemplate('geocachesPrintList/printlist');

        $printList = PrintList::GetContent();
        $cachesCount = count($printList);

        $this->view->setVar('rowCount', $cachesCount);

        if ($cachesCount > 0) {
            $geocaches = MultiCacheStats::getGeocachesById($printList);

            $model = new ListOfCachesModel ();
            $model->addColumn (new Column_CacheTypeIcon ('',
                function (GeoCache $cache) {
                    return [
                        'type' => $cache->getCacheType(),
                        'status' => $cache->getStatus(),
                        'user_sts' => null,
                    ];
                }));
            $model->addColumn(new Column_CacheName(tr('myNotes_cacheName'),
                function (GeoCache $cache) {
                    return [
                        'cacheWp' => $cache->getWaypointId(),
                        'cacheName' => $cache->getCacheName(),
                        'cacheStatus' => $cache->getStatus(),
                    ];
                }));

            // <a href="removelist.php?cacheid={cacheid}&target=mylist.php">
            $model->addColumn(new Column_OnClickActionIcon('',
                function (GeoCache $cache) {
                    return [
                        'icon' => '/images/log/16x16-trash.png',
                        'onClick' => "removeFromList(this, {$cache->getCacheId()})",
                        'title' => tr('mylist_02')
                        ];
                }
                ));

            $model->addDataRows ($geocaches);
            $this->view->setVar('listCacheModel', $model);
        }

        $this->view->buildView();
    }


    public function removeFromListAjax($cacheId)
    {
        $geocache = GeoCache::fromCacheIdFactory($cacheId);
        if (!$geocache) {
            $this->ajaxErrorResponse("No such geocache");
        }

        PrintList::RemoveCache($cacheId);
        $this->ajaxSuccessResponse();

    }

    public function clearListAjax()
    {
        PrintList::Flush();
        $this->view->redirect('/printList');
    }
}
