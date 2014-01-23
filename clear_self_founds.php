<?php
require_once('./lib/common.inc.php');


$sql = "SELECT caches.name, cache_logs.text, caches.user_id, caches.cache_id
FROM caches, cache_logs
WHERE cache_logs.type =1
AND cache_logs.user_id = caches.user_id
AND cache_logs.cache_id = caches.cache_id";
$res = mysql_query($sql);

while( $result = mysql_fetch_array($res))
{
    echo '<table border="0">
    <tr>
        <td bgcolor="#cccccc">';
    //echo 'uid='.$result['user_id'].' cid='.$result['cache_id'].'<br />';
    echo '<b>CACHE ID: '.$result['cache_id'].'<br /><a href="viewcache.php?cacheid='.$result['cache_id'].'">'.$result['name'].'</a></b><br />';
    echo ''.$result['text'].'<br />
        </td>
    </tr>
    </table>
    <br />';
    //$sql = "SELECT "
}

//SELECT caches.user_id, caches.cache_id, cache_logs.user_id, cache_logs.cache_id
//FROM caches, cache_logs
//WHERE cache_logs.type =1
//AND cache_logs.user_id = caches.user_id
//AND cache_logs.cache_id = caches.cache_id

?>
