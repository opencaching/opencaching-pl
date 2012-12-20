<?php
class GeoKretyApi
{
 
	private function TakeUserGeokrets()
	{
	 $url = 'http://geokrety.org/export2.php?userid=1&inventory=1';

	 return simplexml_load_file($url);
	}
	
	public function MakeGeokretList()
	{
     $krety = $this->TakeUserGeokrets();
     $lista = 'liczba geokretów u siebie: ' . count($krety->geokrety->geokret).'<br>';
     
     $lista .= '<table>';
	 foreach ($krety->geokrety->geokret as $kret)
	{
		$lista .= '<tr><td></td><td><a href="http://geokrety.org/konkret.php?id='.$kret->attributes()->id.'">'. $kret .'</a></td></tr>'; 
	}
	$lista .= '</table>';
	echo $lista;
	}

	public function MakeGeokretSelector()
	{
		$krety = $this->TakeUserGeokrets();
 
		$selector = '<select name="geokrety"  multiple="multiple" size="10" >';
		foreach ($krety->geokrety->geokret as $kret)
		{
			$selector .= '<option value='.$kret->attributes()->id.'">'. $kret .'</option>';
		}
		$selector .= '</select>';
	
	
		echo ' Selektor do logowania: <br />'.$selector . '<pre>';
		print_r($krety);
		echo '</pre>';
	}

}

// test
print '<html><meta http-equiv="Content-type" content="text/html; charset=utf-8" /><head></head>';
$krety = new GeoKretyApi;
$krety->MakeGeokretList();
$krety->MakeGeokretSelector();
?>