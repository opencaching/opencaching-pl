<?php

// logbook generator...

require_once('./lib/common.inc.php');

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {
    $tplname = 'logbook';
}

$secret = "dupa231"; //kojoty: this is not my idea - I copied
tpl_set_var('encrypted_message', encrypt($_GET['logbook_type'] . " This is a secret message", $secret));

tpl_BuildTemplate();




function encrypt($text, $key)
{
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv));
}


