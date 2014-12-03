<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder ăĄă˘


 ****************************************************************************/

  //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            $tplname = 'qrcode';

    //set it to writable location, a place for temp generated PNG files
    //$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
    $PNG_TEMP_DIR = $dynbasepath . 'tmp/';

    //html PNG location prefix
    $PNG_WEB_DIR = $dynbasepath . 'tmp/';

    include "./lib/phpqrcode/qrlib.php";

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);


    $filename = $PNG_TEMP_DIR.'test.png';

    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


    if (isset($_REQUEST['data'])) {
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');


        // user data
        tpl_set_var('qrcode', $_REQUEST['data']);
        $uuq=md5($_REQUEST['data']);
        $filename = $PNG_TEMP_DIR.'test.png';
        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);

    } else {

        //default data
        tpl_set_var('qrcode', $config['qrCodeUrl']);
        QRcode::png('http://opencaching.pl/viewcache.php?wp=OP3C90', $filename, $errorCorrectionLevel, $matrixPointSize, 2);

    }

        // Create image instances
        $dest =  imagecreatefromjpeg($GLOBALS['rootpath'].'/images/'. $config['qrCodeLogo']);
        $src =  imagecreatefrompng($dynbasepath . 'tmp/test.png');
        $src_w = imagesx($src);
        $xd=86-($src_w/2);
        $yd=142-($src_w/2);
        // Copy and merge
        imagecopymerge($dest, $src, $xd, $yd, 0, 0, $src_w, $src_w, 100);
        // Output and free from memory
        imagejpeg($dest,$dynbasepath . 'tmp/qrcode.jpg', 85);
        ImageDestroy($dest);
           // generate number for refresh image
       $rand=rand();

        tpl_set_var('imgqrcode', '<img src="/tmp/qrcode.jpg?rand='.$rand.'" border="0" alt="" width="171" height="284" />');

        }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
