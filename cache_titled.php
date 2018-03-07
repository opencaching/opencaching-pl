<?php

use Utils\Database\OcDb;
use Utils\Text\Formatter;

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

   $dateAlg = Formatter::date($record[ 'dateAlg' ]);
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

$view->loadJQueryUI();
tpl_set_var( 'contentTable', $content );

tpl_BuildTemplate();