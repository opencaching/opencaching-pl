<?php

namespace lib\Objects\ChunkModels\StaticMap;


class StaticMapMarker
{
    const TYPE_IMG_MARKER=0;        // marker with the image
    const TYPE_CSS_MARKER=1;        // marker generated dynamicaly in css
    const TYPE_CSS_LEGEND_MARKER=2; // same as CSS_MARKER but to display in legend

    // default colors for markers
    const COLOR_TITLED_CACHE = '#FF4500';
    const COLOR_CACHE = '#61D8A2 ';
    const COLOR_CACHESET = '#65A5D1';
    const COLOR_EVENT = '#FFC273';

    public $markerType;     //TYPE_* enum

    public $id;             // markerId in HTML
    public $left;           // offset from left border of the map img
    public $top;            // offset from top of the map img
    public $tooltip = null; // tooltip HTML of the marker
    public $color;          // marker color

    public $markerImg;      // optional image to display
    public $link = null;    // marker can be clickable

    public function getClasses(){
        $cssClasses = [];

        if($this->tooltip){
            $cssClasses[]='lightTipped';
        }

        return implode(' ', $cssClasses);
    }

    public static function createWithImgPosition($id, $top, $left, $color,
        $tooltip=null, $link=null)
    {
        $marker = new self();
        $marker->id = $id;
        $marker->left = $left;
        $marker->top = $top;
        $marker->color = $color;

        $marker->markerImg = null;
        $marker->markerType = self::TYPE_CSS_MARKER;
        $marker->coords = null;
        $marker->tooltip = $tooltip;
        $marker->link = $link;
        return $marker;
    }

    public static function getCssMarkerForLegend($markerColor)
    {
        $marker = new self();

        $marker->color = $markerColor;
        $marker->markerType = self::TYPE_CSS_LEGEND_MARKER;

        return $marker;
    }

}
