<?php
/* * *************************************************************************
  ./util/newsapprove/index.php
  -------------------
  begin                : Oktoboer 13 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************

  Hide, show and delete news-entries

  Restricted access via .htaccess!

 * ************************************************************************* */

$rootpath = '../../';
require('../../lib/common.inc.php');
if ($usr['admin']) {
    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;

        // check id
        $rs = mysql_query('SELECT COUNT(*) count FROM `news` WHERE `id`=\'' . addslashes($id) . '\'', $dblink);
        $r = mysql_fetch_array($rs);
        mysql_free_result($rs);
        if ($r['count'] != 1)
            die('news-id does not exist');

        if ($action == 'show') {
            mysql_query('UPDATE `news` SET `display`=1 WHERE `id`=\'' . addslashes($id) . '\'', $dblink);
        } else if ($action == 'hide') {
            mysql_query('UPDATE `news` SET `display`=0 WHERE `id`=\'' . addslashes($id) . '\'', $dblink);
        } else if ($action == 'delete') {
            mysql_query('DELETE FROM `news` WHERE `id`=\'' . addslashes($id) . '\'', $dblink);
        }
    }
}
?>
<html>
    <body>
        <?php
        if ($usr['admin']) {
            $rs = mysql_query('SELECT `news`.`id` id, `news`.`date_posted` `date_posted`, `news`.`content` `content`, `news`.`display` `display`, `news_topics`.`name` `topic` FROM `news`, `news_topics` WHERE (`news`.`topic`=`news_topics`.`id`) ORDER BY `news`.`date_posted` DESC', $dblink);
            while ($r = mysql_fetch_array($rs)) {
                echo "<p>\n";

                if ($r['display'] == 0)
                    echo '<font color="#999999">';

                echo htmlspecialchars($r['date_posted']) . ' (' . htmlspecialchars($r['topic']) . ") - \n";
                echo $r['content'] . "\n";

                if ($r['display'] == 0)
                    echo '</font>';

                if ($r['display'] == 0)
                    echo '[<a href="index.php?action=show&id=' . urlencode($r['id']) . '">publikuj</a>]';
                else
                    echo '[<a href="index.php?action=hide&id=' . urlencode($r['id']) . '">ukryj</a>]';

                echo ' [<a href="index.php?action=delete&id=' . urlencode($r['id']) . '">usun</a>]';

                echo "</p>\n";
            }
            mysql_free_result($rs);
        }
        ?>
    </body>
</html>
