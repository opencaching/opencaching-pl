<?php

/* * *************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 * ************************************************************************* */
$rootpath = '../../';
require_once($rootpath . 'lib/clicompatbase.inc.php');


/* begin with some constants */

$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

class geokrety
{

    function run()
    {
        /* begin db connect */
        db_connect();
        if ($dblink === false) {
            echo 'Unable to connect to database';
            exit;
        }
        /* end db connect */

        $xmlfile = $this->loadXML();
        if ($xmlfile == false) {
            return;
        }
        $this->importXML($xmlfile);
        $this->removeXML($xmlfile);
        db_disconnect();
    }

    /* get file from XML interface
     * and return path of saved xml
     * or false on error
     */

    function loadXML()
    {
        global $opt;
        global $dynbasepath;

        @mkdir($dynbasepath . 'tmp/geokrety');
        $path = $dynbasepath . 'tmp/geokrety/import.xml';

        $this->removeXML($path);

        $sql = "SELECT value FROM sysconfig WHERE name='geokrety_lastupdate'";
        $last_updated = mysql_result(mysql_query($sql), 0);
        $modifiedsince = strtotime($last_updated);

        set_time_limit(300);
        if (!@copy('http://geokrety.org/export_oc.php?modifiedsince=' . date('YmdHis', $modifiedsince - 3600 * 30 * 24), $path))
            return false;

        $path = 'http://geokrety.org/export_oc.php?modifiedsince=' . date('YmdHis', $modifiedsince - 1);
        return $path;
    }

    /* remove given file
     */

    function removeXML($file)
    {
        @unlink($file);
    }

    /* import the given XML file
     */

    function importXML($file)
    {
        global $opt;

        $xr = new XMLReader();
        if (!$xr->open($file)) {
            $xr->close();
            return;
        }

        $xr->read();
        if ($xr->nodeType != XMLReader::ELEMENT) {
            echo 'error: First element expected, aborted' . "\n";
            return;
        }
        if ($xr->name != 'gkxml') {
            echo 'error: First element not valid, aborted' . "\n";
            return;
        }

        $startupdate = $xr->getAttribute('date');
        if ($startupdate == '') {
            echo 'error: Date attribute not valid, aborted' . "\n";
            return;
        }

        while ($xr->read() && !($xr->name == 'geokret' || $xr->name == 'moves'));

        $nRecordsCount = 0;
        do {
            if ($xr->nodeType == XMLReader::ELEMENT) {
                $element = $xr->expand();
                switch ($xr->name) {
                    case 'geokret':
                        $this->importGeoKret($element);
                        break;
                    case 'moves':
                        $this->importMove($element);
                        break;
                }

                $nRecordsCount++;
            }
        } while ($xr->next());

        $xr->close();

        sql("UPDATE sysconfig SET value = '" . sql_escape($startupdate) . "' WHERE name='geokrety_lastupdate'");
    }

    function importGeoKret($element)
    {
        global $opt;
        $id = $element->getAttribute('id');

        $name = addslashes($this->GetNodeValue($element, 'name'));
        if ($name == '')
            return;

        $userid = $this->GetNodeAttribute($element, 'owner', 'id') + 0;
        $username = addslashes($this->GetNodeValue($element, 'owner'));
        $this->checkUser($userid, $username);

        $typeid = $this->GetNodeAttribute($element, 'type', 'id') + 0;
        $typename = addslashes($this->GetNodeValue($element, 'type'));
        $this->checkGeoKretType($typeid, $typename);

        $description = addslashes($this->GetNodeValue($element, 'description'));
        $datecreated = $this->GetNodeValue($element, 'datecreated');

        $distancetravelled = $this->GetNodeValue($element, 'distancetravelled') + 0;
        $state = $this->GetNodeValue($element, 'state') + 0;

        $longitude = $this->GetNodeAttribute($element, 'position', 'longitude') + 0;
        $latitude = $this->GetNodeAttribute($element, 'position', 'latitude') + 0;

// if no geokret-human, then touch the db
        if ($typeid != 2) {
            $sql = "INSERT INTO gk_item (`id`, `name`, `description`, `userid`, `datecreated`, `distancetravelled`,
                              `latitude`, `longitude`, `typeid`, `stateid`)
                          VALUES ('" . sql_escape($id) . "', '" . sql_escape($name) . "', '" . sql_escape($description) . "', '" . sql_escape($userid) . "', '" . sql_escape($datecreated) . "', '" . sql_escape($distancetravelled) . "', '" . sql_escape($latitude) . "', '" . sql_escape($longitude) . "', '" . sql_escape($typeid) . "', '" . sql_escape($state) . "')
                  ON DUPLICATE KEY UPDATE `name`='" . sql_escape($name) . "', `description`='" . sql_escape($description) . "', `userid`='" . sql_escape($userid) . "', `datecreated`='" . sql_escape($datecreated) . "', `distancetravelled`='" . sql_escape($distancetravelled) . "', `latitude`='" . sql_escape($latitude) . "', `longitude`='" . sql_escape($longitude) . "', `typeid`='" . sql_escape($typeid) . "', `stateid`='" . sql_escape($state) . "'";
            $query = mysql_query($sql);
            sql("DELETE FROM gk_item_waypoint WHERE id NOT IN (SELECT id FROM gk_item)");
            sql("DELETE FROM gk_item_waypoint WHERE id='&1'", sql_escape($id));
            //if( $state == 0 )
            {
                // update associated waypoints
                $waypoints = $element->getElementsByTagName('waypoints');
                if ($waypoints->length == 1) {
                    $wpItems = $waypoints->item(0)->getElementsByTagName('waypoint');
                    for ($i = 0; $i < $wpItems->length; $i++) {
                        $wp = $wpItems->item($i)->nodeValue;
                        if ($wp != '') {
                            $sql = "INSERT INTO gk_item_waypoint (id, wp) VALUES ('" . sql_escape($id) . "', '" . sql_escape($wp) . "') ON DUPLICATE KEY UPDATE wp='" . sql_escape($wp) . "'";
                            mysql_query($sql);
                        }
                    }
                }
//          }
            }
        }
    }

    function importMove($element)
    {
        $id = $element->getAttribute('id');
        $geokretid = $this->GetNodeAttribute($element, 'geokret', 'id') + 0;
        $logtype = $this->GetNodeAttribute($element, 'logtype', 'id') + 0;
        $datemodified = $this->GetNodeAttribute($element, 'date', 'moved');

        $sql = "SELECT datemodified FROM gk_item WHERE id='" . sql_escape($geokretid) . "'";
        $db_datemodified = @mysql_result(@mysql_query($sql), 0);

        if ($datemodified > $db_datemodified) {
            $sql = "UPDATE gk_item SET stateid='" . sql_escape($logtype) . "', datemodified='" . sql_escape($datemodified) . "' WHERE id='" . sql_escape($geokretid) . "'";
            mysql_query($sql);

            sql("DELETE FROM gk_item_waypoint WHERE id NOT IN (SELECT id FROM gk_item)");
            sql("DELETE FROM gk_item_waypoint WHERE id='&1'", sql_escape($geokretid));

            $waypoints = $element->getElementsByTagName('waypoints');
            if ($waypoints->length == 1) {
                $wpItems = $waypoints->item(0)->getElementsByTagName('waypoint');
                for ($i = 0; $i < $wpItems->length; $i++) {
                    $wp = $wpItems->item($i)->nodeValue;
                    if ($wp != '') {
                        $sql = "INSERT INTO gk_item_waypoint (id, wp) VALUES ('" . sql_escape($geokretid) . "', '" . sql_escape($wp) . "') ON DUPLICATE KEY UPDATE wp='" . sql_escape($wp) . "'";
                        mysql_query($sql);
                    }
                }
            }
        }
    }

    function checkGeoKretType($id, $name)
    {
        sql("INSERT INTO `gk_item_type` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'", sql_escape($id), sql_escape($name));
    }

    function checkUser($id, $name)
    {
        if ($id == 0)
            return;

        sql("INSERT INTO `gk_user` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'", sql_escape($id), sql_escape($name));
    }

    function GetNodeValue(&$domnode, $element)
    {
        $subnode = $domnode->getElementsByTagName($element);
        if ($subnode->length < 1)
            return '';
        else
            return $subnode->item(0)->nodeValue;
    }

    function GetNodeAttribute(&$domnode, $element, $attr)
    {
        $subnode = $domnode->getElementsByTagName($element);
        if ($subnode->length < 1)
            return '';
        else
            return $subnode->item(0)->getAttribute($attr);
    }

}

$geokret = new geokrety();
$geokret->run();
?>
