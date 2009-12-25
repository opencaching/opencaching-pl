<?php
  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('lib/cache_icon.inc.php');
	require_once('lib/clicompatbase.inc.php');
	
	if( $_POST['flush_print_list'] != "")
		$_SESSION['print_list'] = array();
	
	//Preprocessing
	if ($error == true || !$usr || ((count($_SESSION['print_list'])==0) && ($_GET['source'] != 'mywatches')))
	{
		header("Location:index.php");
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $tpl_subtitle; ?>Opencaching PL - drukowanie</title>
		<meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="{lang}" />
		<meta http-equiv="gallerimg" content="no" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<!-- Favicon noch nicht vorhanden <link rel="shortcut icon" href="favicon.ico" />-->
		<link rel="stylesheet" type="text/css" href="tpl/stdstyle/css/style_print.css" />
	</head>

<script>
function clientSideInclude(id, url) {
  var req = false;
  // For Safari, Firefox, and other non-MS browsers
  if (window.XMLHttpRequest) {
    try {
      req = new XMLHttpRequest();
    } catch (e) {
      req = false;
    }
  } else if (window.ActiveXObject) {
    // For Internet Explorer on Windows
    try {
      req = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        req = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e) {
        req = false;
      }
    }
  }
 var element = document.getElementById(id);
 if (!element) {
  alert("Bad id " + id +
   "passed to clientSideInclude." +
   "You need a div or span element " +
   "with this id in your page.");
  return;
 }
  if (req) {
    // Synchronous request, wait till we have it all
    req.open('GET', url, false);
    req.send(null);
    element.innerHTML = req.responseText;
  } else {
    element.innerHTML =
   "Sorry, your browser does not support " +
      "XMLHTTPRequest objects. This page requires " +
      "Internet Explorer 5 or better for Windows, " +
      "or Firefox for any system, or Safari. Other " +
      "compatible browsers may also exist.";
  }
}
</script>

<?php
if( $_POST['flush_print_list'] != "" || $_POST['submit']!= "")
{
	$showlogs = $_POST['showlogs'];
	$pictures = $_POST['showpictures'];
	$nocrypt = $_POST['nocrypt'];
	$spoiler_only = $_POST['spoiler_only'];
}
else
{
	$showlogs = "";
	$pictures = "&pictures=no";
	$nocrypt = "";
	$spoiler_only = "";
}
	if ((isset($_GET['source'])) && ($_GET['source'] == 'mywatches')) {

			$rs = sql("SELECT `cache_watches`.`cache_id` AS `cache_id` FROM `cache_watches` WHERE `cache_watches`.`user_id`='&1'", $usr['userid']);
			if (mysql_num_rows($rs) > 0) {
				$caches_list = array(); 
				for ($i = 0; $i < mysql_num_rows($rs); $i++) {
					$record = sql_fetch_array($rs);
					$caches_list[] = $record['cache_id'];
					//var_dump($record);
				}
			}

	} else {
		$caches_list = $_SESSION['print_list'];
	}
	
	
/*$caches_list = array();
$nr = 0;
for( $i=1000;$i<2000;$i+=200)
{
	
	$caches_list[$nr] = $i;
	$nr++;
}
*/
	

	foreach( $caches_list as $id )
	{
		$include_caches .= "clientSideInclude('include".$id."', 'viewcache.php?cacheid=".$id."&print=y".$pictures.$showlogs.$nocrypt.$spoiler_only."');";
		$include_caches_list .= "<div id='include".$id."'></div>";
	}
	
	$checked_1 = ""; $checked_2 = ""; $checked_3 = ""; $checked_4 = ""; $checked_5 = ""; $checked_6 = ""; $checked_7 = ""; $checked_8 = "";
	
	if( $_POST['showlogs'] == "" || !isset($_POST['showlogs']))
		$checked_1 = "checked";
	if( $_POST['showlogs'] == "&showlogs=4" )
		$checked_2 = "checked";
	if( $_POST['showlogs'] == "&showlogsall=y" )
		$checked_3 = "checked";
		
	if( $_POST['showpictures'] == "&pictures=no" || !isset($_POST['showpictures']) )
		$checked_4 = "checked";
	if( $_POST['showpictures'] == "&pictures=small" )
		$checked_5 = "checked";
	if( $_POST['showpictures'] == "&pictures=big" )
		$checked_6 = "checked";
		
	if( $_POST['nocrypt'] == "&nocrypt=1" )
		$checked_7 = "checked";
		
	if( $_POST['spoiler_only'] == "&spoiler_only=1" )
		$checked_8 = "checked";
	
?>

<body onload="<?php echo $include_caches;?>">
<?
if ((!isset($_GET['source'])) || ($_GET['source'] != 'mywatches')) {
?>
<form action="printcache.php" method="POST">
<?
}else{
?>
<form action="printcache.php?source=mywatches" method="POST">
<?
}
?>
<span class="text_gray">
<div>
		<input type="radio" name="showlogs" id="shownologs" value="" <?php echo $checked_1;?>><label for="shownologs">Nie pokazuj logów</label>
		<input type="radio" name="showlogs" id="showlogs" value="&showlogs=4" <?php echo $checked_2;?>><label for="showlogs">Pokaż ostatnie logi</label>
		<input type="radio" name="showlogs" id="showalllogs" value="&showlogsall=y" <?php echo $checked_3;?>><label for="showalllogs">Pokaż wszystkie logi</label>
</div>
		<input type="radio" name="showpictures" id="shownopictures" value="&pictures=no" <?php echo $checked_4;?>><label for="shownopictures">Nie pokazuj zdjęć</label>
		<input type="radio" name="showpictures" id="showpictures" value="&pictures=small" <?php echo $checked_5;?>><label for="showpictures">Pokaż miniatury</label>
		<input type="radio" name="showpictures" id="showallpictures" value="&pictures=big" <?php echo $checked_6;?>><label for="showallpictures">Pokaż duże zdjęcia</label>
<div>
		<input type="checkbox" name="nocrypt" id="nocrypt" value="&nocrypt=1" <?php echo $checked_7;?>><label for="nocrypt">Odszyfruj podpowiedzi</label>&nbsp;&nbsp;&nbsp;
		<input type="checkbox" name="spoiler_only" id="spoiler_only" value="&spoiler_only=1" <?php echo $checked_8;?>><label for="spoiler_only">Tylko spoilery</label>&nbsp;&nbsp;&nbsp;
</div>
		<input type="submit" name="submit" value="Zmień">

<?
if ((!isset($_GET['source'])) || ($_GET['source'] != 'mywatches')) {
?>
		&nbsp;&nbsp;&nbsp;
		<input type="submit" name="flush_print_list" value="<?php echo tr("clear_list") . " (" . count($_SESSION['print_list']);?>)">
<?
}
?>
</span>
</form>
<hr>
<?php
echo $include_caches_list;
?>

<div id="printedcaches">
				<?php echo $content;?>
</div>
	</body>
</html>
