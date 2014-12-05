<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<?php
    $rootpath = __DIR__ . '/../../';
    require_once __DIR__ . '/../common.inc.php';
    require_once __DIR__ . '/userInputFilter.php';
?>	
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<body>
<form action="" method="post">
    <textarea cols="100" rows="15" name="html"><?php if (isset($_POST['html'])) echo htmlentities($_POST['html'],ENT_NOQUOTES,'UTF-8');?></textarea>
    <br><input type="submit"> 
</form>
<?php 
    if (isset($_POST['html'])){ 
        $context = array();
        $clean = userInputFilter::purifyHtmlString($_POST['html'], $context);
        $errors = @$context['errors'];
        if (isset($errors)){
            echo $errors->getHTMLFormatted(userInputFilter::getConfig());
        }
        echo '<pre>';
        echo htmlentities($clean, ENT_NOQUOTES | ENT_HTML401, 'UTF-8');
        echo '</pre>';
        //echo '<hr>';
        //echo '<pre>';
        //echo htmlentities(htmlspecialchars_decode($clean), ENT_NOQUOTES | ENT_HTML401, 'UTF-8');
        //echo '</pre>';
        //echo '<hr>';
        //$clean2 = userInputFilter::purifyHtmlString($clean);
        //if ($clean2 == $clean){
        //    echo '<p>Clean 2 OK</p>';
        //}
        echo '<hr>';
        echo $clean;
    }   
?>
</body>
