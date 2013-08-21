<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';	

function emailOwners($ptId, $commentType, $commentDateTime, $commentText){
	global $octeam_email, $usr, $absolute_server_URI;
	require_once __DIR__.'/powerTrailBase.php';
	$owners = powerTrailBase::getPtOwners($ptId);
	$commentTypes = powerTrailBase::getPowerTrailComments();
	$ptDbRow = powerTrailBase::getPtDbRow($ptId);
	
	
	/*
	$email_headers  = 'MIME-Version: 1.0' . "\r\n";
    $email_headers .= 'Content-type: text/html; charset=utf-8' . "\r\n"; 
	$email_headers .= 'From: "' . $mailfrom . '" <' . $mailfrom . '>';
	*/
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8 ' . "\r\n";
	$headers .= "From: OpenCaching <".$octeam_email.">\r\n";
	$headers .= "Reply-To: ".$octeam_email. "\r\n";
	
	$subject = tr('pt128').' '.$ptDbRow['name'];
	
	$mailbody = read_file(dirname(__FILE__).'/commentEmail.html');
	$mailbody = mb_ereg_replace('{commentDateTime}', $commentDateTime, $mailbody);
	$mailbody = mb_ereg_replace('{userId}', $usr['userid'], $mailbody);
	$mailbody = mb_ereg_replace('{userName}', $usr['username'], $mailbody);
	$mailbody = mb_ereg_replace('{absolute_server_URI}', $absolute_server_URI, $mailbody);
	$mailbody = mb_ereg_replace('{commentType}', tr($commentTypes[$commentType]['translate']), $mailbody);
	$mailbody = mb_ereg_replace('{commentText}', $commentText, $mailbody);
	$mailbody = mb_ereg_replace('{ptName}', $ptDbRow['name'], $mailbody);
	$mailbody = mb_ereg_replace('{ptId}', $ptId, $mailbody);
	$mailbody = mb_ereg_replace('{pt127}', tr('pt127'), $mailbody);
	$mailbody = mb_ereg_replace('{pt128}', tr('pt128'), $mailbody);
	
	foreach ($owners as $owner) {
		$to = $owner['email'];
		mb_send_mail($to, $subject, $mailbody, $headers);
	}
	//for debug only
	mb_send_mail('lza@tlen.pl', $subject, $mailbody, $headers);
}