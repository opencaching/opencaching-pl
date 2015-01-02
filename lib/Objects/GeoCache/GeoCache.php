<?php

namespace lib\Objects\GeoCache;

/**
 * Description of geoCache
 *
 * @author Łza
 */
class GeoCache
{

    private $caheId;
    private $cacheType;
    private $cacheName;
    private $datePlaced;
    private $cacheLocation = array();

	/**
	 * geocache coordinates object (instance of \lib\Objects\Coordinates\Coordinates class)
	 * @var $coordinates \lib\Objects\Coordinates\Coordinates
	 */
	private $coordinates;

    /**
     *
     * @param array $params
     *  'cacheId' => (integer) database cache identifier
     *  'wpId' => (string) geoCache wayPoint (ex. OP21F4)
     */
    public function __construct($params)
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        if (isset($params['cacheId'])) {
            $this->caheId = (int) $params['cacheId'];
            $queryById = "SELECT name, type, date_hidden, longitude, latitude FROM `caches` WHERE `cache_id`=:1 LIMIT 1";
            $db->multiVariableQuery($queryById, $this->caheId);
        }
        $cacheDbRow = $db->dbResultFetch();
        $this->cacheType = $cacheDbRow['type'];
        $this->cacheName = $cacheDbRow['name'];
        $this->datePlaced = strtotime($cacheDbRow['date_hidden']);
        $this->loadCacheLocation($db);
		$this->coordinates = new \lib\Objects\Coordinates\Coordinates($cacheDbRow);
    }

    private function loadCacheLocation()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = 'SELECT `code1`, `code2`, `code3`, `code4`  FROM `cache_location` WHERE `cache_id` =:1 LIMIT 1';
        $db->multiVariableQuery($query, $this->caheId);
        $dbResult = $db->dbResultFetch();
        $this->cacheLocation = $dbResult;
    }

    public function getCacheType()
    {
        return $this->cacheType;
    }

    public function getCacheLocation()
    {
        return $this->cacheLocation;
    }

    public function getCacheName()
    {
        return $this->cacheName;
    }

    public function getDatePlaced()
    {
        return $this->datePlaced;
    }

}
