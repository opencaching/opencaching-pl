<?php


$GeokretyArray =
array(
		'secid'   => 'e6MdMSRIcHkEbf0uSNq7mOdmoTyjfBSOVg23uXrs8mxM183xV2lHaMaWKVDsWAlpdhdPSbU3abjSlDO1KNQs2TCnQ1NuGR0f06PwyU6A83asTcpsLB3SMYUOpmDYVv3q', # 'e6MdMSRIcHkEbf0uSNq7mOdmoTyjfBSOVg23uXrs8mxM183xV2lHaMaWKVDsWAlpdhdPSbU3abjSlDO1KNQs2TCnQ1NuGR0f06PwyU6A83asTcpsLB3SMYUOpmDYVv3q',
		'nr'      => '2B9L8U',
		'formname'=> 'ruchy',
		'logtype' => 0, #0 = Dropped to; 1 = Grabbed from; 2 = comment; 3 = Seen in; 4 = Archived; 5 = Visiting;
		'data'    => '2012-12-27',
		'godzina' => 14,
		'minuta'  => 15,
		'comment' => '(synchro z serwisu www.opencaching.pl Virtual Machine)',
		'wpt'     => 'op44da',
		'app'     => 'Opencaching',
		'app_ver' => 'PL'
);


print 'tablica przed wyslaniem: <pre>';
print_r ($GeokretyArray);
print '</pre><br /><br />';


$postdata = http_build_query($GeokretyArray);

$opts = array('http' =>
		array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
		)
);

$context = stream_context_create($opts);
$result = file_get_contents('http://geokrety.org/ruchy.php', false, $context);
$resultarray = simplexml_load_string($result);



// print $resultarray->geokrety->geokret->attributes()->state .'<br><br>';
print 'tablica z wynikiem z geokretow <pre>';
print_r ($resultarray);
print '</pre>';

// print $result;
mail('wloczynutka@gmail.com', 'GeoKretyApi Error', $result);

?>