<?php
//prepare the templates and include all neccessary
if (!isset($rootpath))
    $rootpath = '';
require_once('./lib/common.inc.php');
require_once('lib/cache_icon.inc.php');
require_once('lib/clicompatbase.inc.php');

if (!isset($_POST['flush_print_list']))
    $_POST['flush_print_list'] = '';
if ($_POST['flush_print_list'] != "")
    $_SESSION['print_list'] = array();

//Preprocessing
if ($_GET['cacheid'] == '') {
    if ($error == true || !$usr || ((count($_SESSION['print_list']) == 0 ) && ($_GET['source'] != 'mywatches'))) {
        header("Location:index.php");
        die();
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $tpl_subtitle; ?><?php echo $pagetitle; echo ' - ' . tr('pagetitle_print'); ?></title>
        <meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta http-equiv="Content-Language" content="<?php echo $lang; ?>" />
        <meta http-equiv="gallerimg" content="no" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="cache-control" content="no-cache" />
        <link rel="shortcut icon" href="/images/<?php print $config['headerFavicon']; ?>" />
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
    if (!isset($_POST['showpictures']))
        $_POST['showpictures'] = '';
    if (!isset($_POST['spoiler_only']))
        $_POST['spoiler_only'] = '';
    if (!isset($_POST['showlogs']))
        $_POST['showlogs'] = '';
    if (!isset($_POST['nocrypt']))
        $_POST['nocrypt'] = '';

    if ($_GET['cacheid'] != "") {
        $showlogs = $_POST['showlogs'];
        $pictures = $_POST['showpictures'] != "" ? $_POST['showpictures'] : "&pictures=no";
        $nocrypt = $_POST['nocrypt'];
        $spoiler_only = $_POST['spoiler_only'];
    } else if ($_POST['flush_print_list'] != "" || $_POST['submit'] != "") {
        $showlogs = $_POST['showlogs'];
        $pictures = $_POST['showpictures'];
        $nocrypt = $_POST['nocrypt'];
        $spoiler_only = $_POST['spoiler_only'];
    } else {
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
    } else if ($_GET['cacheid'] != "") {
        $caches_list = array();
        $caches_list[] = $_GET['cacheid'];
    } else {
        $caches_list = $_SESSION['print_list'];
    }


    /* $caches_list = array();
      $nr = 0;
      for( $i=1000;$i<2000;$i+=200)
      {

      $caches_list[$nr] = $i;
      $nr++;
      }
     */

    if (!isset($include_caches))
        $include_caches = '';
    if (!isset($include_caches_list))
        $include_caches_list = '';
    foreach ($caches_list as $id) {
        $include_caches .= "clientSideInclude('include" . $id . "', 'viewcache.php?cacheid=" . $id . "&print=y" . $pictures . $showlogs . $nocrypt . $spoiler_only . "');";
        $include_caches_list .= "<div id=\"include" . $id . "\" class=\"content-cache\"></div>";
    }

    $checked_0 = "";
    $checked_1 = "";
    $checked_2 = "";
    $checked_3 = "";
    $checked_4 = "";
    $checked_5 = "";
    $checked_6 = "";
    $checked_7 = "";
    $checked_8 = "";

    if ($_POST['shownologbook'] == "&logbook=no")
        $checked_0 = "checked";
    if (!isset($_POST['showlogs']))
        $_POST['showlogs'] = '';
    if ($_POST['showlogs'] == "")
        $checked_1 = "checked";
    if ($_POST['showlogs'] == "&showlogs=4")
        $checked_2 = "checked";
    if ($_POST['showlogs'] == "&showlogsall=y")
        $checked_3 = "checked";

    if ($_POST['showpictures'] == "&pictures=no" || !isset($_POST['showpictures']))
        $checked_4 = "checked";
    if ($_POST['showpictures'] == "&pictures=small")
        $checked_5 = "checked";
    if ($_POST['showpictures'] == "&pictures=big")
        $checked_6 = "checked";

    if ($_POST['nocrypt'] == "&nocrypt=1")
        $checked_7 = "checked";

    if ($_POST['spoiler_only'] == "&spoiler_only=1")
        $checked_8 = "checked";
    ?>

    <body onload="<?php echo $include_caches; ?>">

        <div class="nodisplay-onprint">
        <?php
        if ($_GET['cacheid']) {
        ?>
        <form action="printcache.php?cacheid=<?php print intval($_GET['cacheid']);?>" method="POST">
            <?php
            } else if ((!isset($_GET['source'])) || ($_GET['source'] != 'mywatches')) {
            ?>
            <form action="printcache.php" method="POST">
                <?php
                }else{
                ?>
            <form action="printcache.php?source=mywatches" method="POST">
                <?php
                }
                ?>
                <div>
                    <input type="radio" name="showlogs" id="shownologbook" value="&logbook=no" <?php echo $checked_0; ?>><label for="shownologbook"><?php print tr('printcache_00'); ?></label>
                    <input type="radio" name="showlogs" id="shownologs" value="" <?php echo $checked_1; ?>><label for="shownologs"><?php print tr('printcache_01'); ?></label>
                    <input type="radio" name="showlogs" id="showlogs" value="&showlogs=4" <?php echo $checked_2; ?>><label for="showlogs"><?php print tr('printcache_02'); ?></label>
                    <input type="radio" name="showlogs" id="showalllogs" value="&showlogsall=y" <?php echo $checked_3; ?>><label for="showalllogs"><?php print tr('printcache_03'); ?></label>
                </div>
                <input type="radio" name="showpictures" id="shownopictures" value="&pictures=no" <?php echo $checked_4; ?>><label for="shownopictures"><?php print tr('printcache_04'); ?></label>
                <input type="radio" name="showpictures" id="showpictures" value="&pictures=small" <?php echo $checked_5; ?>><label for="showpictures"><?php print tr('printcache_05'); ?></label>
                <input type="radio" name="showpictures" id="showallpictures" value="&pictures=big" <?php echo $checked_6; ?>><label for="showallpictures"><?php print tr('printcache_06'); ?></label>
                <div>
                    <input type="checkbox" name="nocrypt" id="nocrypt" value="&nocrypt=1" <?php echo $checked_7; ?>><label for="nocrypt"><?php print tr('printcache_07'); ?></label>&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="spoiler_only" id="spoiler_only" value="&spoiler_only=1" <?php echo $checked_8; ?>><label for="spoiler_only"><?php print tr('printcache_08'); ?></label>&nbsp;&nbsp;&nbsp;
                </div>
                <input type="submit" name="submit" value="<?php print tr('printcache_09'); ?>">

                <?php
                if($_GET['cacheid'] == '')
                    if ((!isset($_GET['source'])) || ($_GET['source'] != 'mywatches')) {
                ?>
                &nbsp;&nbsp;&nbsp;
                <input type="submit" name="flush_print_list" value="<?php echo tr("clear_list") . " (" . count($_SESSION['print_list']); ?>)">
                <?php
                    }
                ?>
            </form>
            <hr class="nodisplay-onprint">
        </div>
        <?php
        echo $include_caches_list;
        ?>

        <div id="printedcaches">
            <?php
            if (isset($content))
                echo $content;
            ?>
        </div>
    </body>
</html>
