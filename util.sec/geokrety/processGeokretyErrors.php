<?php
$rootpath = __DIR__.'/../../';
//require_once($rootpath.'lib/clicompatbase.inc.php');
require_once($rootpath.'GeoKretyAPI.php');

$errors = GeoKretyApi::getErrorsFromDb();
$errorNumber = count($errors);
print "znaleziono $errorNumber bledow";
if($errorNumber == 0) exit;

$i = 1;
foreach ($errors as $nr => $error) {
	if ($error['operationType'] == 1 || $error['operationType'] == 2) {
		$toMail[$i] = $error;
		$toMail[$i]['dataSent'] = unserialize(stripslashes($error['dataSent']));
		$toMail[$i]['response'] = unserialize(stripslashes($error['response']));
		$i++;
	}
}

if(isset($toMail)) {
	// send one mail with all errors to RT, clean db.
	$gk = new GeoKretyApi;
	if ($gk->mailToRT($toMail)) {
		$queryParam = '';
		foreach ($toMail as $recordId) {
			$queryParam .= $recordId['id'].',';
		}
	$queryParam = substr($queryParam, 0, -1);
	GeoKretyApi::removeDbRows($queryParam);
	}
}



	