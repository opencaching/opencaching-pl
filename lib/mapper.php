<?php
    $rootpath = "../";
    require_once('./common.inc.php');

    function isFloat($n)
    {
    return ( $n == strval(floatval($n)) )? true : false;
    }

    function typeLetter($intType)
    {
        switch($intType)
        {
            case 1: return "U"; break;      //unknown
            case 2: return "T"; break;      //traditional
            case 3: return "M"; break;      //multi
            case 4: return "V"; break;      //virtual
            case 5: return "W"; break;      //webcam
            case 6: return "E"; break;      //event
            case 7: return "Q"; break;      //quiz
            case 8: return "O"; break;      //mOving
            case 9: return "C"; break;      //math
            case 10: return "D"; break;     //Drive-in
            default: return "";
        }
    }

    function scoreColor($im, $score, $votes, $default)
    {
        if( $votes < 3 )
            {
                return $default;
            }
            else
            {
                // show cache's score
                if( $score <= 0.5 )
                    return imagecolorallocate($im, 255,0,0);
                if( $score > 0.5 && $score <= 1.0 )
                    return imagecolorallocate($im, 255,51,0);
                if( $score > 1.0 && $score <= 1.5 )
                    return imagecolorallocate($im, 255,102,0);
                if( $score > 1.5 && $score <= 3.5 )
                    return imagecolorallocate($im, 255,153,0);
                if( $score > 3.5 && $score <= 4.5 )
                    return imagecolorallocate($im, 153,255,0);
                if( $score > 4.5 && $score <= 5.0 )
                    return imagecolorallocate($im, 102,255,0);
                if( $score > 5.0 && $score <= 5.5 )
                    return imagecolorallocate($im, 51,255,0);
                if( $score > 5.5)
                    return imagecolorallocate($im, 0,255,0);
            }
    }

    function latlon_to_pix($lat,$lon, $rect)
    {
        $lat = ($lat);
        $lon = ($lon);
        $x_min   = 0;  $x_max   = 256;
        $y_min   = 0;  $y_max   = 256;
        $lon_max = $rect->x; $lon_min = $rect->x+$rect->width;
        $lat_min = $rect->y; $lat_max = $rect->y+$rect->height;
        $x = $x_min + ($x_max - $x_min) *
            ( 1 - ($lon - $lon_min) / ($lon_max - $lon_min) );
        $y = $y_max - ($y_max - $y_min) *
            ( ($lat - $lat_min) / ($lat_max - $lat_min) );
        return array("x"=>round($x),"y"=>round($y));
    }

    $x = intval($_GET['x']);
    $y = intval($_GET['y']);
    $zoom = intval($_GET['z']);
    if( $zoom < 4 )
        exit;
  $user_id = intval($_GET['userid']);

    $rect = getLatLongXYZ($x,$y,$zoom);

    $im = imagecreatetruecolor(256, 256);
    $color=array();
    $color['found'] = imagecolorallocate ($im, 199, 198, 197);
    $color['new'] = imagecolorallocate ($im, 250, 249, 116);
    $color['own'] = imagecolorallocate ($im, 112, 192, 103);
    $color['r'] = imagecolorallocate ($im, 237, 129, 125);

    $black = imagecolorallocate($im, 0,0,0);
    $twhite = imagecolorallocate ($im, 250,250,250);
    imagefilledrectangle($im,0,0,256,256,$twhite);
    imagecolortransparent($im, $twhite);
    //$alpha = imagecolorallocatealpha($im, 199, 198, 197, 99);

    $bound = 0.15;

    // enable searching for ignored caches
    if( $_GET['h_ignored'] == "true" )
    {
        $h_sel_ignored = "cache_ignore.id as ignored,";
        $h_ignored = "LEFT JOIN cache_ignore ON (cache_ignore.user_id='".sql_escape($user_id)."' AND cache_ignore.cache_id=caches.cache_id)";
    }
    else
    {
        $h_sel_ignored = "";
        $h_ignored = "";
    }

    if( $_GET['be_ftf'] == "true" )
    {
        $own_not_attempt = "caches.founds>0";
        $_GET['h_temp_unavail'] = "true";
        $_GET['h_arch'] = "true";
    }
    else
        $own_not_attempt = "caches.cache_id IN (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='".sql_escape($user_id)."' AND (type=1 OR type=8))";


    $min_score = floatval($_GET['min_score']);
    $max_score = floatval($_GET['max_score']);

    if( $_GET['h_nogeokret'] == "true" )
        $filter_by_type_string .= " AND caches.cache_id IN (SELECT cache_id FROM caches WHERE wp_oc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) OR (wp_gc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<> 4 AND typeid<>2)) AND wp_gc <> '') OR (wp_nc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) AND wp_nc <> '')) ";
    else
        $filter_by_type_string = "";

    $sql = "SELECT $h_sel_ignored caches.cache_id, caches.name, caches.wp_oc as wp, caches.votes, caches.score, caches.latitude, caches.longitude, caches.type, caches.status as status, datediff(now(), caches.date_hidden) as old, caches.user_id, IF($own_not_attempt, 1, 0) as found
    FROM caches
    $h_ignored
    WHERE ( caches.latitude BETWEEN ".($rect->y-$rect->height*$bound)." AND ".($rect->y + $rect->height+$rect->height*$bound)." ) AND ( caches.longitude BETWEEN ".($rect->x-$rect->width*$bound)." AND ".($rect->x+$rect->width+$rect->width*$bound)." ) ".$filter_by_type_string;

    $query = mysql_query($sql);

        while( $cache = mysql_fetch_array($query) )
        {
            if(  !($_GET['h_u'] == "true" && $cache['type'] == 1) // hide unknown type?
                && !($_GET['h_t'] == "true" && $cache['type'] == 2) // hide traditional type?
                && !($_GET['h_m'] == "true" && $cache['type'] == 3) // hide multi type?
                && !($_GET['h_v'] == "true" && $cache['type'] == 4) // hide virtual type?
                && !($_GET['h_w'] == "true" && $cache['type'] == 5) // hide webcam type?
                && !($_GET['h_e'] == "true" && $cache['type'] == 6) // hide event type?
                && !($_GET['h_q'] == "true" && $cache['type'] == 7) // hide quiz type?
                && !($_GET['h_o'] == "true" && $cache['type'] == 8) // hide mobile type?
                && !($_GET['h_ignored'] == "true" && $cache['ignored'])              // hide ignored?
                && !($_GET['h_own'] == "true" && ($cache['user_id'] == $user_id))                // hide own?
                && !($_GET['h_found'] == "true" && $cache['found'] )                 // hide found?
                && !($_GET['be_ftf'] == "true" && ($cache['found']==1 || $cache['status']!=1 || $cache['user_id']==$user_id))                // find ftf
                && !($_GET['h_avail'] == "true" && $cache['status']==1 )                 // hide ready to find?
                && !($_GET['h_temp_unavail'] == "true" && $cache['status']==2 )              // hide ready to find?
                && !($_GET['h_arch'] == "true" && $cache['status']==3 )              // hide ready to find?
                && !($_GET['h_noattempt'] == "true" && !$cache['found'] )                // hide not yet found?
                && (($cache['score']>=$min_score && $cache['score']<=$max_score && $cache['votes']>=3)|| ($cache['votes']<3 && $_GET['h_noscore'] == "true" ))
                && $cache['status'] <= 3                                                                       // always hide waiting for approval, not yet available, blocked by rr
                && $_GET['h_pl'] == "true"
            )
            {

                // caches, user haven't been searching for
                $pt = latlon_to_pix($cache['latitude'], $cache['longitude'], $rect);

                if($cache['user_id'] == $user_id)
                    $typeColor = "own";
                else
                if( $cache['found'] )
                    $typeColor = "found";
                else
                if( $cache['old'] <= 10 )
                    $typeColor = "new";
                else
                    $typeColor = "r";

                if( $zoom > 13 )
                    $pointer = 1.5*$zoom;
                else
                    $pointer = 0;

                    if( $_GET['sc']==1 )
                        imagefilledellipse($im, $pt["x"], $pt["y"]-$pointer, max(2*$zoom-7,7),max(2*$zoom-7,7),scoreColor($im, $cache['score'], $cache['votes'], $color[$typeColor]));

                    // BLACK ELLIPSE
                    imageellipse($im, $pt["x"], $pt["y"]-$pointer, max(2*$zoom-10,5),max(2*$zoom-10,5),$black);

                    // MARKER ELLIPSE
                    imagefilledellipse($im, $pt["x"], $pt["y"]-$pointer, max(2*$zoom-12,3),max(2*$zoom-12,3),$color[$typeColor]);

                if( $zoom > 13 )
                {
                    $pointer = 1.5*$zoom;

                    // show additional pointer
                    $value = array(
                    $pt["x"]-max(2*$zoom-10,5)/2, $pt["y"]-$pointer,
                    $pt["x"], $pt["y"],
                    $pt["x"]+max(2*$zoom-10,5)/2, $pt["y"]-$pointer
                    );
                    imagefilledpolygon($im, $value, 3, $color[$typeColor]);

                    imageline($im, $pt["x"]-max(2*$zoom-10,5)/2, $pt["y"]-$pointer, $pt["x"], $pt["y"], $black );
                    imageline($im, $pt["x"]+max(2*$zoom-10,5)/2, $pt["y"]-$pointer, $pt["x"], $pt["y"], $black );
                    //imageellipse($im, $pt["x"], $pt["y"]+1.5*$zoom, $radius, $radius, $black);

                    if( $_GET['signes'] == "true" )
                    {
                        $fontSize = 1.3*$zoom-11;
                        $box = imagettfbbox( $fontSize, 0, '../util.sec/bt.ttf', $cache['wp']);
                        imagettftext($im, $fontSize, 0, $pt["x"]-(int)abs($box[2]/2), $pt["y"]+(int)(abs($box[7]/2))-$pointer+max(2*$zoom+28,5)/2, $black, $rootpath.'util.sec/bt.ttf', $cache['wp']);
                    }
                }

                if( $zoom > 10)
                {
                //echo "aaa";
                    $fontSize = 1.55*$zoom-11;
                    $box = imagettfbbox( $fontSize, 0, '../util.sec/bt.ttf', typeLetter($cache['type']));
                    imagettftext($im, $fontSize, 0, $pt["x"]-(int)abs($box[2]/2), $pt["y"]+(int)(abs($box[7]/2))-$pointer, $black, $rootpath.'util.sec/bt.ttf', typeLetter($cache['type']));
                }
                //imagestring($im, 5, $pt["x"], $pt["y"], 'T', $white);


            }
            //else
            {
                // found caches
            }
            // end markers
        }

        $sql = "SELECT longitude, latitude, notify_radius FROM user WHERE user_id = ".sql_escape(intval($user_id));
        $query = mysql_query($sql);
        $userdata = mysql_fetch_array($query);

        $watch = latlon_to_pix($userdata['latitude'], $userdata['longitude'], $rect);
        //if( $_GET['watch_circle'] == "true" )
    //  {
        //echo "u=".$userdata['notify_radius']."aa".$user_id.$black;
        //die();

        $north = calcLatLong($userdata['longitude'], $userdata['latitude'], $userdata['notify_radius']*1000, 0);
        $east = calcLatLong($userdata['longitude'], $userdata['latitude'], $userdata['notify_radius']*1000, 90);

        $north_pt = latlon_to_pix($north['latitude'], $north['longitude'], $rect);
        $east_pt = latlon_to_pix($east['latitude'], $east['longitude'], $rect);
        //print_r($north_pt);
        //print_r($east_pt);
        //print_r($watch);
        //die();
        imageellipse($im, $watch["x"], $watch["y"], $watch["x"]-$east_pt["x"], $watch["y"]-$north_pt["y"], $alpha);
        //}

        unset($icons);
        header("Content-Type: image/gif");
    imagegif($im,'',9);
        imagedestroy($im);

// utility class to hold the rectangle position and size.
class Rectangle {
    var $x,$y;
    var $width, $height;
}

/** returns the Google zoom level for the keyhole string. */
function getTileZoom($keyHoleString) {
  return strlen($keyHoleString)-1;
}


/**
 * returns a Rectangle2D with x = lon, y = lat, width=lonSpan, height=latSpan for a keyhole string.
*/

/**
* Calculate the unknown coordinates at bearing and distance from known coordinates
*
* Developed by TJ (http://tjworld.net/software/kml/) for Google Earth applications
*
* @param    float   long        Longitude of known point (decimal degreees)
* @param    float   lat     Latitude of known point (decimal degrees)
* @param    float   distance    Distance to unknown point (meters)
* @param    float   bearing     Bearing (angle in degrees) to unknown point.
*                   Measured from North=0, -ve going to west (-90), +ve going to east (90)
* @returns  array           Associative array containing unknown 'longitude' and 'latitude'
*/

function calcLatLong($long, $lat, $distance, $bearing) {
 $EARTH_RADIUS_EQUATOR = 6378140.0;
 $RADIAN = 180 / pi();
 $b = $bearing / $RADIAN;
 $long = $long / $RADIAN;
 $lat = $lat / $RADIAN;
 $f = 1/298.257;
 $e = 0.08181922;

 $R = $EARTH_RADIUS_EQUATOR * (1 - $e * $e) / pow( (1 - $e*$e * pow(sin($lat),2)), 1.5);
 $psi = $distance/$R;
 $phi = pi()/2 - $lat;
 $arccos = cos($psi) * cos($phi) + sin($psi) * sin($phi) * cos($b);
 $latA = (pi()/2 - acos($arccos)) * $RADIAN;

 $arcsin = sin($b) * sin($psi) / sin($phi);
 $longA = ($long - asin($arcsin)) * $RADIAN;
 return array('longitude' => $longA, 'latitude' => $latA);
}

function getLatLongSat($keyholeStr) {
      $lon      = -180; // x
      $lonWidth = 360; // width 360

      //double lat = -90;  // y
      //double latHeight = 180; // height 180
      $lat       = -1;
      $latHeight = 2;

      for ($i = 1; $i < strlen($keyholeStr); $i++) {
         $lonWidth /= 2;
         $latHeight /= 2;

         $c = substr($keyholeStr,$i,1);

         switch ($c) {
            case 's':
               $lon += $lonWidth;

               break;

            case 'r':
               $lat += $latHeight;
               $lon += $lonWidth;

               break;

            case 'q':
               $lat += $latHeight;
               break;

            case 't':
               break;

            default:
               return;
         }
      }

      // convert lat and latHeight to degrees in a transverse mercator projection
      // note that in fact the coordinates go from about -85 to +85 not -90 to 90!
      $latHeight += $lat;
      $latHeight = (2 * atan(exp(PI() * $latHeight))) - (PI() / 2);
      $latHeight *= (180 / PI());

      $lat = (2 * atan(exp(PI() * $lat))) - (PI() / 2);
      $lat *= (180 / PI());

      $latHeight -= $lat;

      if ($lonWidth < 0) {
         $lon      = $lon + $lonWidth;
         $lonWidth = -$lonWidth;
      }

      if ($latHeight < 0) {
         $lat       = $lat + $latHeight;
         $latHeight = -$latHeight;
      }

      //        lat = Math.asin(lat) * 180 / Math.PI();
      $rect = new Rectangle;
      $rect->x = $lon;
      $rect->y = $lat;
      $rect->width = $lonWidth;
      $rect->height = $latHeight;
      return $rect;
}

   /**
   * returns a Rectangle2D with x = lon, y = lat, width=lonSpan, height=latSpan
   * for an x,y,zoom as used by google.
   */
function getLatLongXYZ($x, $y, $zoom) {
$debug = $_GET['debug'];
      $lon      = -180; // x
      $lonWidth = 360; // width 360

      $lat       = -1;
      $latHeight = 2;

      $tilesAtThisZoom = 1 << ($zoom);
      $lonWidth  = 360.0 / $tilesAtThisZoom;
      $lon       = -180 + ($x * $lonWidth);
      $latHeight = 2.0 / $tilesAtThisZoom;
      $lat       = (($tilesAtThisZoom/2 - $y-1) * $latHeight);

if ($debug) {echo("(uniform) lat:$lat latHt:$latHeight<br />");}
      // convert lat and latHeight to degrees in a transverse mercator projection
      // note that in fact the coordinates go from about -85 to +85 not -90 to 90!
      $latHeight += $lat;
      $latHeight = (2 * atan(exp(PI() * $latHeight))) - (PI() / 2);
      $latHeight *= (180 / PI());

      $lat = (2 * atan(exp(PI() * $lat))) - (PI() / 2);
      $lat *= (180 / PI());


if ($debug) {echo("pre subtract lat: $lat latHeight $latHeight<br />");}
      $latHeight -= $lat;
if ($debug) {echo("lat: $lat latHeight $latHeight<br />");}

      if ($lonWidth < 0) {
         $lon      = $lon + $lonWidth;
         $lonWidth = -$lonWidth;
      }

      if ($latHeight < 0) {
         $lat       = $lat + $latHeight;
         $latHeight = -$latHeight;
      }


      $rect = new Rectangle();
      $rect->x = $lon;
      $rect->y = $lat;
      $rect->height = $latHeight;
      $rect->width= $lonWidth;

      return $rect;
   }


?>
