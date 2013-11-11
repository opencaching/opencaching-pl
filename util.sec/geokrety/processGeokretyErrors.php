<?php
// /ocpl/util.sec/geokrety/processGeokretyErrors.php 
require_once(__DIR__.'/../../GeoKretyAPI.php');
require_once(__DIR__.'/../../powerTrail/powerTrailBase.php');

//$rootpath = __DIR__.'/../../';
//include __DIR__.'/../../lib/common.inc.php';

$run = new processGeokretyErrors;
$run->run();

class processGeokretyErrors {
	
	private $errors;
	private $errorNumber;
	private $debug = false;
	private $toMail = false;
	private $logGeokrety; 
	
	public function run(){
        	
		// geoPaths
		$this->cleanGeoPaths();
        $this->makePt();
		
		// geoKrety
		$this->getErrors();
		if($this->errorNumber == 0) return;
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
		$this->logGeokrety->setGeokretyTimeout(30);
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
			if($error != '') {
				$success = 'no';
				$result['errors'] = $error;
			}
		}
		$result['retryLoggingSucces'] = $success;
		$result['geokretId'] = $GeoKretyLogResult['geokretId'];
     	$result['geokretName'] = $GeoKretyLogResult['geokretName'];
		return $result;
	}
	
	private function makePt() {
		include __DIR__.'/../../lib/settings.inc.php';
		include __DIR__.'/../../lib/language.inc.php';
		$langArray = available_languages();
		
		$oldFileArr = explode('xxkgfj8ipzxx', file_get_contents($dynstylepath.'ptPromo.inc-'.$lang.'.php'));
		require_once __DIR__.'/../../region_class.php';
		$region = new GetRegions();
		$newPt =  powerTrailBase::writePromoPt4mainPage($oldFileArr[1]);
		$regions = $region->GetRegion($newPt['centerLatitude'], $newPt['centerLongitude']);
		foreach ($langArray as $language) {
			$this->makePtContent($newPt, $language, $dynstylepath, $regions);
		}
	}
	
	private function makePtContent($newPt, $langTr, $dynstylepath, $regions) {
		$fileContent = '<span style="display:none" id="ptPromoId">xxkgfj8ipzxx'.$newPt['id'].'xxkgfj8ipzxx</span>';
		$fileContent .= '<table width="100%"><tr><td style="padding-left: 10px;padding-right: 10px;">';
		if ($newPt['image'] != '') {
			$fileContent .= '<img height="50" src="'.$newPt['image'].'" />';
		} else {
			$fileContent .= '<img height="50" src="tpl/stdstyle/images/blue/powerTrailGenericLogo.png" />';
		}
		$fileContent .= '</td><td width=50% style="font-size: 13px; padding-left: 10px; padding-right: 10px;" valign="center"><a href="powerTrail.php?ptAction=showSerie&ptrail='.$newPt['id'].'">'.$newPt['name'].'</a>';
		$fileContent .= '<td style="font-size: 13px;" valign="center"><b>'.$newPt['cacheCount'].'</b>&nbsp;'.tr2('pt138',$langTr).', <b>'.round($newPt['points'], 2).'</b>&nbsp;'.tr2('pt038', $langTr).'</td>';
		if ($regions) $fileContent .= '</td><td style="font-size: 12px;" valign="center">'.tr2($regions['code1'], $langTr).'>'.$regions['adm3'];
		$fileContent .= '</td></tr></table>';
		file_put_contents($dynstylepath.'ptPromo.inc-'.$langTr.'.php' , $fileContent);
		
		// print "$langTr <br/> $fileContent";
	}
	
	private function cleanGeoPaths(){
		powerTrailBase::cleanGeoPaths();
	}
}
