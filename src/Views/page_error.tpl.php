<?php

// Do not reference any other PHP items here except the variables set in
// ErrorHandler::processErrorMsg(), to minimize the risk of error recursion.

?><!DOCTYPE html>
<html>
<head>
    <title>Opencaching - Error</title>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="/images/icons/oc_icon.png" />
    <link rel="apple-touch-icon-precomposed" href="/images/icons/oc_logo_144.png" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/style_screen.css" />
</head>
<body style="margin:20px 40px 0 40px; background:white">
    <a href="/"><img src="/images/oc_logo.png" /></a>
    <br />
    <p style="font-size:1.4em; max-width:720px">
        <br /><?= $pageError ?>
    </p>
    <?php if ($showMainPageLink) { ?>
        <p><a href="/"><?= $mainPageLinkTitle ?></a></p>
    <?php } ?>
    <?php if ($errorMsg) { ?>
        <br />
        <p><b>--- Additional Information for developers, only displayed if $debug_page is true ---</b></p>
        <p><?= nl2br($errorMsg) ?></p>
    <?php } ?>
</body>
</html>
