<?php

class contactDataProcessor {
	public static function processContactData($contactData)
	{
		$result = self::processContacts($contactData,1);
		return $result;
	}
	private static function processContacts($contactData, $headerLevel){
		$result = '';
		foreach($contactData as $contact){
			$result .= self::processContactItem($contact, $headerLevel);
		}
		return $result;
	}
	
	private static function processContactItem($contact, $headerLevel)
	{
		$result = '';
		
		if (isset($contact['groupName'])){
			$result .= "<h$headerLevel>".self::tr($contact['groupName'], $contact)."</h$headerLevel>\n";
		}
		if (isset($contact['emailAddress'])){
			$result .= "<p><b>E-mail: ".$contact['emailAddress']."</b></p>\n";
		}
		if (isset($contact['groupDescription'])){
			$groupDescription = $contact['groupDescription'];
			if (is_array($groupDescription)){
				foreach($groupDescription as $groupItem){
					$result .= "<p>".self::tr($groupItem, $contact)."</p>\n";
				}
			} else {
				$result .= "<p>".self::tr($groupDescription, $contact)."</p>\n";
			}
		}
		if (isset($contact['subgroup'])){
			$result .= self::processContacts($contact['subgroup'],$headerLevel+1);
		}
		
		return $result;
	}

	private static function tr($str, $context = null)
	{
		global $language, $lang;
		if(isset($language[$lang][$str])&&$language[$lang][$str]) {
			$str = $language[$lang][$str];
		}
		$str = self::resolve($str, $context);
		return $str;
	}
	
	private static function resolve($str, $context)
	{
		if (is_array($context)){
			foreach ($context as $varname=>$varvalue){
				if (is_string($varvalue)){
					$str = mb_ereg_replace('{' . $varname . '}', $varvalue, $str);
				}
			}
		}
		return $str;
	}
	
};


$contact_text = contactDataProcessor::processContactData($contactData);
tpl_set_var('contact_text', $contact_text);

?>