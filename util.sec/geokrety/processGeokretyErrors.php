<?php
//require_once($rootpath.'lib/clicompatbase.inc.php');
require_once(__DIR__.'/../../GeoKretyAPI.php');
$run = new processGeokretyErrors;
$run->run();


class processGeokretyErrors {
	
	private $errors;
	private $errorNumber;
	private $debug = true;
	private $toMail = false;
	private $logGeokrety; 
	
	public function run(){
		$this->getErrors();
		if($this->errorNumber == 0) exit;
		$this->processGetGeokretyErrors();
		if($this->toMail) $this->sendReportEmail();
	}
	
	private function getErrors() {
		$this->errors = GeoKretyApi::getErrorsFromDb();
		$this->errorNumber = count($this->errors);
		if($this->debug) print "znaleziono $this->errorNumber bledow<br/>";
	}	

	private function processGetGeokretyErrors(){
		$i = 1;
		$this->logGeokrety = new GeoKretyApi;
		foreach ($this->errors as $nr => $error) {
			if ($error['operationType'] == 1 || $error['operationType'] == 2) {
				// errors get geokrety in cache, get geokrety in user inventory 	
				$this->toMail[$i] = $error;
				$this->toMail[$i]['dataSent'] = unserialize(stripslashes($error['dataSent']));
				$this->toMail[$i]['response'] = unserialize(stripslashes($error['response']));
				$i++;
			}
			if ($error['operationType'] == 3) {
				// errors on logging geokrety
				$retryLoggingGeokrety = $this->retryLoggingGeokrety(unserialize(stripslashes($error['dataSent'])));
				
				$this->toMail[$i] = $error;
				$this->toMail[$i]['dataSent'] = unserialize(stripslashes($error['dataSent']));
				$this->toMail[$i]['response'] = unserialize(stripslashes($error['response']));
				$this->toMail[$i]['logRetry'] = $retryLoggingGeokrety;
				$i++;
			} 
		}
	}

	private function sendReportEmail() {
		// send one mail with all errors to RT, clean db.
		$gk = new GeoKretyApi;
		if ($gk->mailToRT($this->toMail)) {
			$queryParam = '';
			foreach ($this->toMail as $recordId) {
				$queryParam .= $recordId['id'].',';
			}
		$queryParam = substr($queryParam, 0, -1);
		GeoKretyApi::removeDbRows($queryParam);
		if($this->debug) print "wyslano maila, usunieto wpisy z bazy<br/>";
		}
	}

	private function retryLoggingGeokrety($GeokretyLogArray) {
		$GeoKretyLogResult = $this->logGeokrety->LogGeokrety($GeokretyLogArray, true);
		$success = 'yes';
		foreach ($GeoKretyLogResult['errors'] as $nr => $error) {
			if($error == '') {
				 $success = 'no';
			}
		}
		$result['retryLoggingSucces'] = $success;
		$result['geokretId'] = $GeoKretyLogResult['geokretId'];
     	$result['geokretName'] = $GeoKretyLogResult['geokretName'];
		return $result;
	}
}
