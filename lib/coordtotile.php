<?php

# Based on a class by Ian Dees
# Rewritten by Rasmus Schultz <http://mindplay.dk> and Carsten L�tzen <http://lutzen.eu>

class GMapPoint
{

    public $x, $y;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

}

class GMapTile
{

    const TILE_SIZE = 256;

    protected static $pixelsPerLonDegree = array();
    protected static $pixelsPerLonRadian = array();
    protected static $numTiles = array();
    protected static $bitmapOrigo = array();
    protected static $c = self::TILE_SIZE;
    protected static $bc;
    protected static $Wa;
    protected $p; // * The point (x,y) for this tile at the current zoom level
    protected $z; // * Zoom level for this tile
    protected $lat, $long; // * The latitude/longitude for this tile

    public function __construct($lat, $long, $zoom)
    {
        $this->z = $zoom;
        $this->p = self::getTileCoordinate($lat, $long, $zoom);
        $this->lat = $lat;
        $this->long = $long;
    }

    public static function fromTileCoord($x, $y, $zoom)
    {

        // Alternative constructor - constructs a [GMapTile] with the given tile coordinates and zoom level.
        // The latitude and longitude of the new instance indicate the north-west corner of the tile.

        $x = $x * self::TILE_SIZE;
        $y = $y * self::TILE_SIZE;



        $long = ($x - self::$bitmapOrigo[$zoom]->x) / self::$pixelsPerLonDegree[$zoom];
        $e = ($y - self::$bitmapOrigo[$zoom]->y) / (-1 * self::$pixelsPerLonRadian[$zoom]);
        $lat = (2 * atan(exp($e)) - M_PI / 2) / self::$Wa;

        return new GMapTile($lat, $long, $zoom);
    }

    public function getTileCoord()
    {

        // Returns a [GMapPoint] indicating the tile coordinates at this [GMapTile]'s zoom level.

        return $this->p;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLong()
    {
        return $this->long;
    }

    public function getKeyholeString()
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

    public function getKeyholeDirection($x, $y)
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

    public static function getBitmapCoordinate($lat, $long, $zoom)
    {

        // Converts latitude/longitude to pixels at the given zoom level.

        $d = new GMapPoint(0, 0);

        $d->x = floor(self::$bitmapOrigo[$zoom]->x + $long * self::$pixelsPerLonDegree[$zoom]);
        $e = sin($lat * self::$Wa);

        if ($e > 0.9999) {
            $e = 0.9999;
        }

        if ($e < -0.9999) {
            $e = -0.9999;
        }

        $d->y = floor(self::$bitmapOrigo[$zoom]->y + 0.5 * log((1 + $e) / (1 - $e)) * -1 * (self::$pixelsPerLonRadian[$zoom]));

        return $d;
    }

    public static function getTileCoordinate($lat, $long, $zoom)
    {

        // Converts latitude/longitude to tile coordinates at the given zoom level.

        $d = self::getBitmapCoordinate($lat, $long, $zoom);

        $d->x = floor($d->x / self::TILE_SIZE);
        $d->y = floor($d->y / self::TILE_SIZE);

        return $d;
    }

    // --- Internal helper methods:

    public static function _init()
    {

        // Initializes static members of the class.
        // This is called when the class is loaded.

        self::$bc = 2 * M_PI;
        self::$Wa = M_PI / 180;

        for ($d = 17; $d >= 0; --$d) {
            self::$pixelsPerLonDegree[$d] = self::$c / 360;
            self::$pixelsPerLonRadian[$d] = self::$c / self::$bc;
            $e = self::$c / 2;
            self::$bitmapOrigo[$d] = new GMapPoint($e, $e);
            self::$numTiles[$d] = self::$c / 256;
            self::$c *= 2;
        }
    }

}

GMapTile::_init();
?>
