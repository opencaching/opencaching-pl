<?php

if (!isset($rootpath))
    $rootpath = './';

require_once($rootpath . 'lib/language.inc.php');

require_once($rootpath . 'lib/settings.inc.php');

// load HTML specific includes
require_once($rootpath . 'lib/cookie.class.php');

if ($cookie->is_set('lang')) {
    $lang = $cookie->get('lang');
}

//language changed?
if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
}
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
}

// load language settings
load_language_file($lang);

// set locale
// print "$lang<br><br>";

switch ($lang) {
    case 'pl':
        setlocale(LC_TIME, 'pl_PL.UTF-8');
        break;
    case 'nl':
        setlocale(LC_TIME, 'nl_NL.UTF-8');
        break;
    case 'fr':
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        break;
    case 'de':
        setlocale(LC_TIME, 'de_DE.UTF-8');
        break;
    case 'sv':
        setlocale(LC_TIME, 'sv_SV.UTF-8');
        break;
    case 'es':
        setlocale(LC_TIME, 'es_ES.UTF-8');
        break;
    case 'cs':
        setlocale(LC_TIME, 'cs_CS.UTF-8');
        break;
    default:
        setlocale(LC_TIME, 'en_EN');
        break;
}
?>