<?php

class Tile
{

    // The point (x,y) for this tile
    var $p;
    // The coord (lat,lon) for this tile
    var $co;
    // Zoom level for this tile
    var $z;
    // ...Constants...
    var $PI = 3.1415926535;
    var $tileSize = 256;
    var $pixelsPerLonDegree = Array();
    var $pixelsPerLonRadian = Array();
    var $numTiles = Array();
    var $bitmapOrigo = Array();
    // Note: These variable names are based on the variables names found in the
    //       Google maps.*.js code.
    var $c = 256;
    var $bc;
    var $Wa;

    // Fill in the constants array
    function fillinconstants()
    {
        $this->bc = 2 * $this->PI;
        $this->Wa = $this->PI / 180;

        for ($d = 17; $d >= 0; --$d) {
            $this->pixelsPerLonDegree[$d] = $this->c / 360;
            $this->pixelsPerLonRadian[$d] = $this->c / $this->bc;
            $e = $this->c / 2;
            $this->bitmapOrigo[$d] = new p($e, $e);
            $this->numTiles[$d] = $this->c / 256;
            $this->c *= 2;
        }
    }

    function Tile($latitude, $longitude, $zoomLevel)
    {
        $this->fillInConstants();
        $this->z = $zoomLevel;
        $this->p = $this->getTileCoordinate($latitude, $longitude, $zoomLevel);
        $this->co = $this->getLatLong($latitude, $longitude, $zoomLevel);
        $this->getLatLong($latitude, $longitude, $zoomLevel);
    }

    function getTileCoord()
    {
        return $this->p;
    }

    function getTileLatLong()
    {
        return $this->co;
    }

    function getKeyholeString()
    {
        $s = "";
        $myX = $this->p->x;
        $myY = $this->p->y;

        for ($i = 17; $i > $this->z; $i--) {
            $rx = (fmod($myX, 2));
            $myX = floor($myX / 2);
            $ry = (fmod($myY, 2));
            $myY = floor($myY / 2);
            $s = $this->getKeyholeDirection($rx, $ry) . $s;
        }
        return 't' . $s;
    }

    function getKeyholeDirection($x, $y)
    {
        if ($x == 1) {
            if ($y == 1) {
                return 's';
            } else if ($y == 0) {
                return 'r';
            }
        } else if ($x == 0) {
            if ($y == 1) {
                return 't';
            } else if ($y == 0) {
                return 'q';
            }
        }

        return '';
    }

    function getBitmapCoordinate($a, $b, $c)
    {
        $d = new p(0, 0);

        $d->x = floor($this->bitmapOrigo[$c]->x + $b * $this->pixelsPerLonDegree[$c]);
        $e = sin($a * $this->Wa);

        if ($e > 0.9999) {
            $e = 0.9999;
        }

        if ($e < -0.9999) {
            $e = -0.9999;
        }

        $d->y = floor($this->bitmapOrigo[$c]->y + 0.5 * log((1 + $e) / (1 - $e)) * -1 * ($this->pixelsPerLonRadian[$c]));
        return $d;
    }

    function getTileCoordinate($a, $b, $c)
    {
        $d = $this->getBitmapCoordinate($a, $b, $c);
        $d->x = floor($d->x / $this->tileSize);
        $d->y = floor($d->y / $this->tileSize);

        return $d;
    }

    function getLatLong($a, $b, $c)
    {
        $d = new p(0, 0);
        $e = $this->getBitmapCoordinate($a, $b, $c);
        $a = $e->x;
        $b = $e->y;

        $d->x = ($a - $this->bitmapOrigo[$c]->x) / $this->pixelsPerLonDegree[$c];
        $e = ($b - $this->bitmapOrigo[$c]->y) / (-1 * $this->pixelsPerLonRadian[$c]);
        $d->y = (2 * atan(exp($e)) - $this->PI / 2) / $this->Wa;
        return $d;
    }

}

// A simple PHP class that represents a point
class p
{

    var $x;
    var $y;

    function p($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

}

?>
