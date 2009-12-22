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



?>