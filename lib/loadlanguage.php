<?php
	if (!isset($rootpath)) $rootpath = './';

	require_once($rootpath . 'lib/language.inc.php');

	require_once($rootpath . 'lib/settings.inc.php');

	// load HTML specific includes
	require_once($rootpath . 'lib/cookie.class.php');

        if ($cookie->is_set('lang'))
        {
            $lang = $cookie->get('lang');
        }

	//language changed?
	if (isset($_POST['lang']))
	{
		$lang = $_POST['lang'];
	}
	if (isset($_GET['lang']))
	{
		$lang = $_GET['lang'];
	}

	// load language settings
	load_language_file($lang);

   // set locale
   // print "$lang<br><br>";
   
   switch ($lang){
   case 'pl':
		setlocale(LC_TIME, 'pl_PL.UTF-8');  
		break;
   case 'nl':
		setlocale(LC_ALL, 'nl_NL');
		break;
   case 'fr':
		setlocale(LC_ALL, 'fr_FR');
		break;
   case 'de':
		setlocale(LC_ALL, 'de_DE');
		break;	
   case 'sv':
		setlocale(LC_ALL, 'sv_SV');
		break;		
   case 'es':
		setlocale(LC_ALL, 'es_ES');
		break;	
   case 'cs':
		setlocale(LC_ALL, 'cs_CS');
		break;			
   default:
   		setlocale(LC_ALL, 'en_EN');
		break;	
   }
   

?>