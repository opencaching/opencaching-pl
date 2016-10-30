<?php

use Utils\Database\XDb;
use Utils\Database\OcDb;

global $dateFormat;
require_once('./lib/common.inc.php');

$tplname = 'cache_titled';

$usrid = -1;
if ( $usr != false )
    $usrid = $usr['userid'];

$dbcLocCache = OcDb::instance();

$query="SELECT
    caches.type cache_type, caches.name cacheName, caches.cache_id cache_id,
    user.username userName, user.user_id user_id,
    cache_location.adm3 cacheRegion, cache_titled.date_alg dateAlg

    FROM cache_titled
    JOIN caches on caches.cache_id = cache_titled.cache_id
    JOIN user on user.user_id = caches.user_id
    JOIN cache_location ON cache_location.cache_id = cache_titled.cache_id
    WHERE caches.status=1";

if ( isset( $_REQUEST[ 'type' ] ) )
{
    $latitude = XDb::xSimpleQueryValue("SELECT `latitude` FROM user WHERE user_id='" . XDb::xEscape($usr['userid']) . "'", 0);
    $longitude = XDb::xSimpleQueryValue("SELECT `longitude` FROM user WHERE user_id='" . XDb::xEscape($usr['userid']) . "'", 0);
    $distance = XDb::xSimpleQueryValue("SELECT `notify_radius` FROM user WHERE user_id='" . XDb::xEscape($usr['userid']) . "'", 0);

    localCachesInc::createLocalCaches($dbcLocCache, $longitude, $latitude, $distance, $usrid);

    // quick fix - this line generate syntax error in sql query.
    // $query .= " JOIN local_caches on cache_titled.cache_id = local_caches.cache_id ";
}

$s = $dbcLocCache->simpleQuery($query);

$content="";

for( $i = 0; $i < $dbcLocCache->rowCount($s); $i++ )
{
   $record = $dbcLocCache->dbResultFetch($s);

   $cacheId = $record[ 'cache_id' ];
   $cacheName = str_replace("'", "-", $record[ 'cacheName' ] );
   $cacheName = str_replace("\"", " ", $cacheName);

   $cacheNameRef = '<a href="viewcache.php?cacheid={cacheId}">{cacheName}<a>';
   $cacheNameRef = str_replace('{cacheId}', $cacheId, $cacheNameRef );
   $cacheNameRef = str_replace('{cacheName}', $cacheName, $cacheNameRef );

   $cacheRegion = $record[ 'cacheRegion' ];

   $ownId = $record[ 'user_id' ];

   $userName = str_replace("'", "-", $record[ 'userName' ] );
   $userName = str_replace("\"", " ", $userName);

   $userNameRef = '<a href="viewprofile.php?userid={userId}">{userName}<a>';
   $userNameRef = str_replace('{userId}', $ownId, $userNameRef );
   $userNameRef = str_replace('{userName}', $userName, $userNameRef );

   $dateAlg = date($dateFormat, strtotime($record[ 'dateAlg' ]));
   $dateAlgSort = date("y.m.d", strtotime($record[ 'dateAlg' ]));

   $cacheType = $record[ 'cache_type' ];

   $typeIcon ='<img src="{src}" />';
   $typeIcon = str_replace( "{src}", myninc::checkCacheStatusByUser($record, $usrid), $typeIcon );

    $content .=  "
        gct.addEmptyRow();
        gct.addToLastRow( 0, '$typeIcon' );
        gct.addToLastRow( 1, '$cacheNameRef' );
        gct.addToLastRow( 2, '$cacheRegion' );
        gct.addToLastRow( 3, '$userNameRef' );
        gct.addToLastRow( 4, '<span $dateAlgSort/> $dateAlg' );
    ";

}

tpl_set_var( 'contentTable', $content );

tpl_BuildTemplate();

