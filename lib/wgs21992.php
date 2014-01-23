<?php


//L (dlugosc)
//B (szerokosc)
// lat,lon

function wgs2u1992 ($lat, $lon) {

//double Brad , Lrad, Lorad ,k, C, firad, Xmer, Ymer, Xgk, Ygk;

// stale
$E=  0.0818191910428;
$Pi=  3.141592653589793238462643;
$Pi_2=  1.570796327;  //3.141592653589793238462643 / 2  // Pi / 2
$Pi_4=  0.7853981634; // 3.141592653589793238462643 / 4  // Pi / 4
$Pi__180=  0.01745329252; // 3.141592653589793238462643 / 180
$Ro=  6367449.14577;
$a2=  0.0008377318247344;
$a4=  0.0000007608527788826;
$a6=  0.000000001197638019173;
$a8=  0.00000000000244337624251;


// uklad UTM
//#define mo  0.9996   //wspo#udnik skali na po#udniku #rodkowym
//#define Lo  (double)((((int)(lon/6)) * 6) + 3) // po#udnik #rodkowy
// zone = (int)(lon+180/6)+1
//#define FE  500000   //False Easting
//#define FN  0 //False Northing

// uklad 1992
$mo=  0.9993;   //wspo#udnik #rodkowy
$Lo=  19.0;
$FE=  500000;   //False Easting
$FN=  -5300000; //False Northing


    $Brad = $lat * $Pi / 180; //Pi / 180;
    $Lrad = $lon * $Pi / 180; // Pi / 180;
    $Lorad = $Lo * $Pi / 180; // Pi / 180;

    //k = ((1 - E * sin(Brad)) / (1 + E * sin(Brad))) ^ (E / 2); // pasc
    //k = pow(((1 - E * sin(Brad)) / (1 + E * sin(Brad))) , (E / 2)); // c
    $k = exp( ($E / 2) * log((1 - $E * sin($Brad)) / (1 + $E * sin($Brad))) );

    $C = $k * tan(($Brad / 2) + ($Pi_4));

    $firad = (2 * atan($C)) - ($Pi_2);

    $Xmer = atan(sin($firad) / (cos($firad) * cos($Lrad - $Lorad)));
    $Ymer = 0.5 * log((1 + cos($firad) * sin($Lrad - $Lorad)) / (1 - cos($firad) * sin($Lrad - $Lorad)));

    $Xgk = $Ro * ($Xmer + ($a2 * sin(2 * $Xmer) * cosh(2 * $Ymer)) + ($a4 * sin(4 * $Xmer) * cosh(4 * $Ymer)) + ($a6 * sin(6 * $Xmer) * cosh(6 * $Ymer)) + ($a8 * sin(8 * $Xmer) * cosh(8 * $Ymer)));
    $Ygk = $Ro * ($Ymer + ($a2 * cos(2 * $Xmer) * sinh(2 * $Ymer)) + ($a4 * cos(4 * $Xmer) * sinh(4 * $Ymer)) + ($a6 * cos(6 * $Xmer) * sinh(6 * $Ymer)) + ($a8 * cos(8 * $Xmer) * sinh(8 * $Ymer)));

    $X = $mo * $Xgk + $FN;
    $Y = $mo * $Ygk + $FE;

    return (array($X,$Y));
}

//$temp= wgs2u1992(52.11,20.67);
//print $temp[0];
//print " ";
//print $temp[1];

?>
