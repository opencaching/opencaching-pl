<?php
/**
 * There is a great code usefull for refactoring of this code here:
 * https://www.phpclasses.org/browse/file/10671.html
 *
 */

/**
 * This function converts coordinates in lat-lon system to UMT
 *
 * @param unknown $Lat
 * @param unknown $Lon
 * @param unknown $NS
 * @param unknown $EW
 * @return number[]|string[]|unknown[]
 */
function ll2utm($Lat, $Lon, $NS = null, $EW = null)
{
    if ($Lat > 90)
        die("Invalid Latitude");
    if ($Lon > 180)
        die("Invalid Lonitude");
//$NS=(strtoupper(($NS)=='S') && ($Lat>0));
//$EW=(strtoupper($EW)=='W');
    if ($Lat < 0) {
        $Lat = -$Lat;
        $NS = 'S';
    } else {
        $NS = 'N';
    };
    if ($Lon < 0) {
        $Lon = -$Lon;
        $EW = 'W';
    } else {
        $EW = 'E';
    };

    $Deg2Rad = pi() / 180.0;
//$Alpha100km='VQLFAVQLFAWRMGBWRMGBXSNHCXSNHCYTOJDYTOJDZUPKEZUPKEVQLFAVQLFAWRMGBWRMGBXSNHCXSNHCYTOJDYTOJDZUPKEZUPKE';
// UTM WGS84 Ellipsoid
    $F0 = 0.9996;
    $A1 = 6378137.0 * $F0;
    $B1 = 6356752.3142 * $F0;
    $K0 = 0;
    $N0 = 0;
    $E0 = 500000;
    $N1 = ($A1 - $B1) / ($A1 + $B1); // n
    $N2 = $N1 * $N1;
    $N3 = $N2 * $N1;
    $E2 = (($A1 * $A1) - ($B1 * $B1)) / ($A1 * $A1); // e^2
// przeliczenia
    $K = $Lat * $Deg2Rad;
    $L = $Lon * $Deg2Rad;
    $SINK = sin($K);
    $COSK = cos($K);
    $TANK = $SINK / $COSK;
    $TANK2 = $TANK * $TANK;
    $COSK2 = $COSK * $COSK;
    $COSK3 = $COSK2 * $COSK;
    $K3 = $K - $K0;
    $K4 = $K + $K0;



    $Merid = floor(($Lon) / 6) * 6 + 3;
    if (($Lat >= 72) && ($Lon >= 0)) {
        if ($Lon < 9)
            $Merid = 3;
        else if ($Lon < 21)
            $Merid = 15;
        else if ($Lon < 33)
            $Merid = 27;
        else if ($Lon < 42)
            $Merid = 39;
    }
    if (($Lat >= 56) && ($Lat < 64)) {
        if (($Lon >= 3) && ($Lon < 12))
            $Merid = 9;
    }
    $MeridEW = $Merid < 0;
    if ($MeridEW) {
        $MeridianEW = 'W';
    } else {
        $MeridianEW = 'E';
    }
    $Meridian = abs($Merid);
    $L0 = $Merid * $Deg2Rad; // Lon of True Origin (3,9,15 etc)
    // ArcofMeridian
    $J3 = $K3 * (1 + $N1 + 1.25 * ($N2 + $N3));
    $J4 = sin($K3) * cos($K4) * (3 * ($N1 + $N2 + 0.875 * $N3));
    $J5 = sin(2 * $K3) * cos(2 * $K4) * (1.875 * ($N2 + $N3));
    $J6 = sin(3 * $K3) * cos(3 * $K4) * 35 / 24 * $N3;
    $M = ($J3 - $J4 + $J5 - $J6) * $B1;

    // VRH2
    $Temp = 1 - $E2 * $SINK * $SINK;
    $V = $A1 / sqrt($Temp);
    $R = $V * (1 - $E2) / $Temp;
    $H2 = $V / $R - 1.0;

    $P = $L - $L0;
    $P2 = $P * $P;
    $P4 = $P2 * $P2;
    $J3 = $M + $N0;
    $J4 = $V / 2 * $SINK * $COSK;
    $J5 = $V / 24 * $SINK * ($COSK3) * (5 - ($TANK2) + 9 * $H2);
    $J6 = $V / 720 * $SINK * $COSK3 * $COSK2 * (61 - 58 * ($TANK2) + $TANK2 * $TANK2);
    $North = $J3 + $P2 * $J4 + $P4 * $J5 + $P4 * $P2 * $J6;
//      if ($NS) $North=$North+10000000.0; // UTM S hemisphere , nie wiem dlaczego ale w oryginale dodawa³o siê 10000000.0
    $J7 = $V * $COSK;
    $J8 = $V / 6 * $COSK3 * ($V / $R - $TANK2);
    $J9 = $V / 120 * $COSK3 * $COSK2;
    $J9 = $J9 * (5 - 18 * $TANK2 + $TANK2 * $TANK2 + 14 * $H2 - 58 * $TANK2 * $H2);
    $East = $E0 + $P * $J7 + $P2 * $P * $J8 + $P4 * $P * $J9;
    $IEast = round($East);
    $INorth = round($North); // should strictly be trunc
    $Easting = $IEast;
    $Northing = $INorth;
    $EastStr = '' + abs($IEast);
    $NorthStr = '' + abs($INorth);
    //while (EastStr.length<7) EastStr='0'+EastStr;
    $EastStr = sprintf("%07.0f", $EastStr);
    //while (NorthStr.length<7) NorthStr='0'+NorthStr;
    $NorthStr = sprintf("%07.0f", $NorthStr);
    $GR100km = substr($EastStr, 1, 2 - 1) . substr($NorthStr, 1, 2 - 1);
    $GRremainder = substr($EastStr, 2, 7 - 2) . ' ' . substr($NorthStr, 2, 7 - 2);


    // UTM
    $LonZone = ($Merid - 3) / 6 + 31;
    if ($LonZone % 1 != 0)
        $GR = 'non-UTM central meridian';
    else {
        if ($IEast < 100000 || $Lat < -80 || $IEast > 899999 || $Lat >= 84)
            $GR = 'outside UTM grid area';
        else {
            $Letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            $Pos = round($Lat / 8 - 0.5) + 10 + 2;
            $LatZone = substr($Letters, $Pos, 1);
            if ($LatZone > 'X')
                $LatZone = 'X';
            $Pos = round(abs($INorth) / 100000 - 0.5);
            while ($Pos > 19)
                $Pos = $Pos - 20;
            if ($LonZone % 2 == 0) {
                $Pos = $Pos + 5;
                if ($Pos > 19)
                    $Pos = $Pos - 20;
            }
            $N100km = substr($Letters, $Pos, 1);
            $Pos = $GR100km / 10 - 1;
            $P = $LonZone;
            while ($P > 3)
                $P = $P - 3;
            $Pos = $Pos + (($P - 1) * 8);
            $E100km = substr($Letters, $Pos, 1);
            $GR = $LonZone . $LatZone . $E100km . $N100km . ' ' . $GRremainder;
        }
    }

    return array($LonZone, $LatZone, $NS, $Northing, $EW, $Easting);
    /*
      echo "EastStr  ".$EastStr."<br>\n";
      echo "NorthStr  ".$NorthStr."<br>\n<br>";

      echo "LatZone  ".$LatZone."<br>\n";
      echo "E100km  ".$E100km."<br>\n";
      echo "N100km  ".$N100km."<br>\n";
      echo "GRremainder  ".$GRremainder."<br>\n<br> ";

      echo "Easting  ".$Easting."<br>\n";
      echo "Northing  ".$Northing."<br>\n";
      echo "GridRef  ".$GR."<br>\n";
     */
}

?>
