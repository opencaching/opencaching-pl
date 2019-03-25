<?php
namespace src\Models\ChunkModels\StaticMap;

use src\Models\Coordinates\Coordinates;
use src\Utils\Gis\Gis;
use src\Utils\Uri\SimpleRouter;
use src\Controllers\StartPageController;

class StaticMapModel
{
    private $mapZoom;

    /** @var Coordinates */
    private $mapCenter;

    private $imgWidth;
    private $imgHeight;
    private $mapType;

    private $markers = [];

    private $mapProviderUrl = null;

    public static function defaultFullCountryMap()
    {
        global $main_page_map_center_lat, $main_page_map_center_lon, $main_page_map_zoom;
        global $main_page_map_width, $main_page_map_height;
        global $config;

        $mapCenter = Coordinates::FromCoordsFactory(
            $main_page_map_center_lat, $main_page_map_center_lon);

        $model = self::fixedZoomMapFactory($mapCenter, $main_page_map_zoom,
            $main_page_map_width, $main_page_map_height, $config['maps']['main_page_map']['source']);

        $model->mapProviderUrl = SimpleRouter::getLink(StartPageController::class, 'countryMap');

        return $model;
    }

    public static function fixedZoomMapFactory(Coordinates $mapCenter, $mapZoom,
        $imgWidth, $imgHeight, $mapType=null)
    {

        $map = new self();
        $map->mapCenter = $mapCenter;
        $map->mapZoom = $mapZoom;
        $map->imgHeight = $imgHeight;
        $map->imgWidth = $imgWidth;
        $map->mapType = $mapType;

        return $map;
    }

    public function getMapImgSrc()
    {
        return $this->mapProviderUrl;
    }

    public function getMapTitle()
    {
        return tr('map');
    }

    public function addMarker(StaticMapMarker $m)
    {
        // filter out markers with coords outside of the map img
        if($m->top < 0 || $m->top > $this->imgHeight ||
            $m->left < 0 || $m->left > $this->imgWidth){
            return;
        }

        $this->markers[] = $m;
    }

    public function addMarkers(array $markers)
    {
        $this->markers = array_merge($this->markers, $markers);
    }

    public function createMarker($id, Coordinates $coords, $color,
        $tooltip=null, $link=null){

        list($left, $top, ) = Gis::positionAtMapImg(
            $coords, $this->mapCenter, $this->mapZoom, $this->imgWidth, $this->imgHeight);

        $marker = StaticMapMarker::createWithImgPosition(
            $id, $top, $left, $color, $tooltip, $link);

        $this->addMarker($marker);
    }

    public function getMapMarkers()
    {
        return $this->markers;
    }

    public function getImgWidth()
    {
        return $this->imgWidth;
    }

    public function getImgHeight()
    {
        return $this->imgHeight;
    }

    public function getMapCenterCoords()
    {
        return $this->mapCenter;
    }

    public function getZoom()
    {
        return $this->mapZoom;
    }

}
