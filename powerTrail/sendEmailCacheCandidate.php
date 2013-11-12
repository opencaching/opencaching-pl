<?php
// sendEmailCacheCandidate.php
$siteDateFormat = 'Y-m-d';
$siteDateTimeFormat = 'Y-m-d H:i';

function emailCacheOwner($ptId, $cacheId, $linkCode){
	global $octeam_email, $usr, $absolute_server_URI, $site_name, $siteDateFormat, $siteDateTimeFormat;
	require_once __DIR__.'/powerTrailBase.php';
	$owners = powerTrailBase::getPtOwners($ptId);
	$ptDbRow = powerTrailBase::getPtDbRow($ptId);
	
	$query = 'SELECT `caches` . * , `user`.`email`, `user`.`username` FROM `caches` , `user` WHERE `cache_id` =:1 AND `caches`.`user_id` = `user`.`user_id` LIMIT 1';
	$db = new dataBase(false);
	$db->multiVariableQuery($query, $cacheId);
	$cacheData = $db->dbResultFetch();
	
	//remove images
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8 ' . "\r\n";
	$headers .= "From: $site_name <".$octeam_email.">\r\n";
	$headers .= "Reply-To: ".$octeam_email. "\r\n";
	$mailbody = read_file(dirname(__FILE__).'/candidateEmail.html');
	$mailbody = mb_ereg_replace('{cacheOwnerName}', $cacheData['username'], $mailbody);
	$mailbody = mb_ereg_replace('{ptName}', $ptDbRow['name'], $mailbody);
	$mailbody = mb_ereg_replace('{ptId}', $ptId, $mailbody);
	$mailbody = mb_ereg_replace('{cacheName}', $cacheData['name'], $mailbody);
	$mailbody = mb_ereg_replace('{dateTime}', date($siteDateFormat), $mailbody);
	$mailbody = mb_ereg_replace('{userId}', $usr['userid'], $mailbody);
	$mailbody = mb_ereg_replace('{userName}', $usr['username'], $mailbody);
	$mailbody = mb_ereg_replace('{absolute_server_URI}', $absolute_server_URI, $mailbody);
	$mailbody = mb_ereg_replace('{linkCode}', $linkCode, $mailbody);
	$mailbody = mb_ereg_replace('{runwatch14}', tr('runwatch14'), $mailbody);
	$mailbody = mb_ereg_replace('{cacheWaypoint}', $cacheData['wp_oc'], $mailbody);
	$mailbody = mb_ereg_replace('{pt183}', tr('pt183'), $mailbody);
	$mailbody = mb_ereg_replace('{pt184}', tr('pt184'), $mailbody);
	$mailbody = mb_ereg_replace('{pt185}', tr('pt185'), $mailbody);
	$mailbody = mb_ereg_replace('{pt189}', tr('pt189'), $mailbody);
	$mailbody = mb_ereg_replace('{pt186}', tr('pt186'), $mailbody);
	$mailbody = mb_ereg_replace('{pt187}', tr('pt187'), $mailbody);
	$mailbody = mb_ereg_replace('{pt188}', tr('pt188'), $mailbody);
	$mailbody = mb_ereg_replace('{pt190}', tr('pt190'), $mailbody);
	
	mb_send_mail($cacheData['email'], tr('pt183'), $mailbody, $headers);

	// for debug only
	// mb_send_mail('lza@tlen.pl', tr('pt183'), $mailbody, $headers);
}