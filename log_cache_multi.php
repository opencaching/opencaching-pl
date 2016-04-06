<?php

use Utils\Database\XDb;
if (isset($_POST['submitDownloadGpx'])) {
    $fd = "";
    $fd .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    $fd .= "<gpx xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" \r\n";
    $fd .= "  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" version=\"1.0\" \r\n";
    $fd .= "  creator=\"tATO ;)\" \r\n";
    $fd .= "  xsi:schemaLocation=\"http://www.topografix.com/GPX/1/0 \r\n";
    $fd .= "  http://www.topografix.com/GPX/1/0/gpx.xsd \r\n";
    $fd .= "  http://genpol.com/temp/geocache/ http://genpol.com/temp/geocache/geocache.xsd\" \r\n";
    $fd .= "  xmlns=\"http://www.topografix.com/GPX/1/0\">\r\n\r\n";
    $fd .= "<name>Opencaching.pl Oregon multiload</name>\r\n";
    session_start();
    if (isset($_SESSION['log_cache_multi_filteredData'])) {
        $tempList = array();
        foreach ($_SESSION['log_cache_multi_filteredData'] as $k => $v) {
            $v['cache_creator'] = str_replace("&", " ", $v['cache_creator']);

            $fd .= "<wpt lat=\"" . $v['latitude'] . "\" lon=\"" . $v['longitude'] . "\">\r\n";
            $fd .= "    <time>" . $v['rok'] . "-" . $v['msc'] . "-" . $v['dzien'] . "T" . $v['godz'] . ":" . $v['min'] . ":00Z</time>\r\n";
            $fd .= "    <name>" . $v['kod_str'] . " " . mb_substr($v['cache_name'], 0, 20, 'UTF-8') . "</name>\r\n";
            $fd .= "    <desc>" . $v['cache_name'] . " by " . $v['cache_creator'] . "</desc>\r\n";
            $fd .= "    <url>http://opencaching.pl/viewcache.php?cacheid=" . $v['cache_id'] . "</url>\r\n";
            $fd .= "    <urlname>" . $v['kod_str'] . " " . mb_substr($v['cache_name'], 0, 20, 'UTF-8') . "</urlname>\r\n";
            if ($v['status'] == 1 || $v['status'] == 5) { // found, need maintenance
                $fd .= "    <sym>Geocache found</sym>\r\n";
            } else {
                $fd .= "    <sym>Geocache</sym>\r\n";
            }
//          $fd .= "    <sym>Flag, ".($v['status'] == "1" ? "Blue" : "Red")."</sym>\r\n";
            $fd .= "    <geocache xmlns=\"http://genpol.com/temp/geocache/\">\r\n";
            $fd .= "        <name>" . $v['kod_str'] . " " . mb_substr($v['cache_name'], 0, 20, 'UTF-8') . "</name>\r\n";
            $fd .= "        <owner>" . $v['cache_creator'] . "</owner>\r\n";
            switch ($v['cache_type']) {
                case 2: $fd .= "        <type>Traditional</type>\r\n";
                    break;
                case 3: $fd .= "        <type>Multi</type>\t\n";
                    break;
                case 4: $fd .= "        <type>Virtual</type>\r\n";
                    break;
                case 5: $fd .= "        <type>Webcam</type>\r\n";
                    break;
                case 6: $fd .= "        <type>Event</type>\r\n";
                    break;
                default: $fd .= "       <type>Other</type>\r\n";
            }
            $fd .= "        <container>0</container>\r\n";
            $fd .= "        <difficulty>" . $v['cache_difficulty'] . "</difficulty>\r\n";
            $fd .= "        <terrain>" . $v['cache_terrain'] . "</terrain>\r\n";
            $fd .= "    </geocache>\r\n";
            $fd .= "</wpt>\r\n";

            // pre-define TRK
            $tmpDate = $v['timestamp'] - 4 * 60 * 60; // -4 godziny do daty.
            $td = date("Ymd", $tmpDate);
            if (!isset($tempList[$td])) { // zaloz dla daty:
                $tempList[$td]['name'] = "OC-PL " . date("Y-m-d", $tmpDate);
                $tempList[$td]['pts'] = array();
            }
            $tempList[$td]['pts'][] = "     <trkpt lat=\"" . $v['latitude'] . "\" lon=\"" . $v['longitude'] . "\"><time>" . $v['rok'] . "-" . $v['msc'] . "-" . $v['dzien'] . "T" . $v['godz'] . ":" . $v['min'] . ":00Z</time></trkpt>\r\n";
        }
        // EOF waypointy - zdefiniowane.

        foreach ($tempList as $k => $v) {
            $fd .= "\r\n<trk>\r\n";
            $fd .= "    <name>" . $v['name'] . "</name>\r\n";
            $fd .= "    <trkseg>\r\n";
            foreach ($v['pts'] as $k1 => $v1) {
                $fd .= $v1;
            }
            $fd .= "    </trkseg>\r\n";
            $fd .= "</trk>\r\n";
        }

        $fd .= "</gpx>";

        $filname = date("Ymd-Hi") . ".gpx";
        header("Content-type: application/x-download");
        header("Content-type: text/plain; charset=utf-8");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

//      header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filname . "\"");
        echo pack("ccc", 0xef, 0xbb, 0xbf);
        echo $fd;
        die();
    } else {
        die("No data");
    }
}

// //
require_once('./lib/common.inc.php');

$no_tpl_build = false;
if ($usr == false || (!isset($_FILES['userfile']) && !isset($_SESSION['log_cache_multi_data']))) {
    tpl_redirect('log_cache_multi_send.php');
} else {
    require_once($rootpath . 'lib/caches.inc.php');
    require($stylepath . '/log_cache.inc.php');

    $tplname = 'log_cache_multi';
    $myHtml = "";

    $statusy = array();
    $statusy = fcGetStatusyEn();

    // moje dane o skrzynkach z pliku...
    $dane = array();

    if (isset($_FILES['userfile'])) {
        // usuwam zapamietane a nieaktualne juz dane...
        unset($_SESSION['log_cache_multi_data']);
        unset($_SESSION['log_cache_multi_filteredData']);
        unset($_SESSION['filter_to']);
        unset($_SESSION['filter_from']);

        // czy wyslalo sie ok?
        if ($_FILES['userfile']['error'] != 0) {
            // jesli nie to jaki blad?
            if ($_FILES['userfile']['error'] == 2) {
                die("Plik zbyt duzy");
            }
            exit;
        }

        // czy ktos cos nie kombinuje?
        if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            die("Cos nie tak z wysylaniem pliku, sprobuj ponownie...");
        }

        // wczytuje plik
        $some_file = $_FILES['userfile']['tmp_name'];
        $filesize = filesize($some_file);
        $fp = fopen($some_file, "r");
        $filecontent = fread($fp, $filesize);
        fclose($fp);
        // kasuje tymczasowy plik uploadu
        unlink($_FILES['userfile']['tmp_name']);
        unset($_FILES['userfile']);

        // sprawdz czy utf16 i konwert jesli tak
        if (( $filesize >= 2) &&
                (
                ($filecontent[0] == 0x00 || $filecontent[1] == 0x00) ||
                ($filecontent[0] == 0xFF && $filecontent[1] == 0xFE) ||
                ($filecontent[0] == 0xFE || $filecontent[1] == 0xFF)
                )
        ) {
            $filecontent = utf16_to_utf8($filecontent);
        }

        if (strlen($filecontent) >= 3 && ord($filecontent[0]) == 0xEF && ord($filecontent[1]) == 0xBB && ord($filecontent[2]) == 0xBF) {
            // cut UTF-8 BOM
            $filecontent = substr($filecontent, 3);
        }

        $filecontent = explode("\n", $filecontent);

        $dane_i = -1;

        // parsuje plik
        $listaKodowOP = array();
        foreach ($filecontent as $line) {
            $rec = preg_split('[,]', trim($line), 4);
            if (count($rec) >= 4) {
                // wyglada na skrzynke
                if (substr($rec[0], 0, 2) == $oc_waypoint) {
                    $dane_i++;
                    $dane[$dane_i]['kod_str'] = $rec[0];
                    $dane[$dane_i]['typ_kodu'] = $oc_waypoint;

                    $listaKodowOP[] = $dane[$dane_i]['kod_str'];
                    // kod
                    // czas
                    $regex = "/(.+)-(.+)-(.+)T(.+):(.+)Z/";
                    $ileMatches = preg_match($regex, trim($rec[1]), $matches);
                    if (count($matches) >= 6) {
                        $dane[$dane_i]['timestamp'] = mktime($matches[4], $matches[5], 0, $matches[2], $matches[3], $matches[1]);
                        $dane[$dane_i] = UstawDatyZTimeStampa($dane[$dane_i]);
                        unset($matches);
                    }
                    //status
                    $dane[$dane_i]['status'] = isset($statusy[trim($rec[2])]) ? $statusy[trim($rec[2])] : 0;
                    // komentarz
                    $dane[$dane_i]['koment'] = str_replace("\"", "", trim($rec[3]));
                }
            }
        }
        // plik sparsowany...

        $_SESSION['log_cache_multi_data'] = $dane;
    }// EOF jesli jest plik wyslany to parsowanie...

    if (isset($_SESSION['log_cache_multi_data'])) {
        $dane = $_SESSION['log_cache_multi_data'];

        // pomocna lista do WHEREa
        $listaKodowOP = array();
        $minTimeStamp = time();
        $maxTimeStamp = 1;
        foreach ($dane as $k => $v) {
            $listaKodowOP[] = $v['kod_str'];
            if ($v['timestamp'] < $minTimeStamp)
                $minTimeStamp = $v['timestamp'];
            if ($v['timestamp'] > $maxTimeStamp)
                $maxTimeStamp = $v['timestamp'];
        }

        // lista identyfikatorow cache ktore znalazlem w bazie
        $cacheIdList = array();

        // dociagam informacje o nazwie i id skrzynki...
        if ( count($listaKodowOP) > 0) {
            $rs = XDb::xSql(
                "SELECT c.*,u.`username`
                FROM `caches` as c LEFT JOIN `user` as u ON u.`user_id` = c.`user_id`
                WHERE c.`wp_oc` IN ('" . XDb::xEscape( implode("','",$listaKodowOP )) . "')");

            while ( $record = XDb::xFetchArray($rs) ){

                // dodanie dodatkowych info do odpowiedniej skrzynki:
                foreach ($dane as $k => $v) {
                    if ($v['kod_str'] == $record['wp_oc']) {
                        $v['got_sql_info'] = true;
                        $v['cache_id'] = $record['cache_id'];
                        $v['cache_type'] = $record['type'];
                        $v['cache_name'] = $record['name'];
                        $v['longitude'] = $record['longitude'];
                        $v['latitude'] = $record['latitude'];
                        $v['cache_creator'] = $record['username'];
                        $v['cache_difficulty'] = $record['difficulty'];
                        $v['cache_terrain'] = $record['terrain'];
                        $dane[$k] = $v;
                        $cacheIdList[] = $record['cache_id'];
                    }
                }
            }//while
        }

        // dociagam info o ostatniej aktywnosci dla kazdej skrzynki
        if ( count($cacheIdList) > 0) {
            $rs = XDb::xSql(
                "SELECT c.*
                FROM (
                        SELECT cache_id, MAX(date) date FROM `cache_logs`
                        WHERE user_id= ?
                            AND cache_id IN (" . XDb::xEscape(implode(',',$cacheIdList)) . ")
                        GROUP BY cache_id
                    ) as x
                    INNER JOIN `cache_logs` as c ON c.cache_id = x.cache_id
                        AND c.date = x.date", $usr['userid']);

            while ($record = XDb::xFetchArray($rs)) {

                foreach ($dane as $k => $v) {
                    if (isset($v['cache_id']) && $v['cache_id'] == $record['cache_id']) {
                        $v['got_last_activity'] = true;
                        $v['last_date'] = substr($record['date'], 0, strlen($record['date']) - 3);
                        $v['last_status'] = $record['type'];
                        $dane[$k] = $v;
                    }
                }
            }//while
        }//if


        // filtrowanie...
        // wczytanie wartosci filtrow, a jesli nie ma to odpowiednio min i max wartosc z pliku tak by wszystkie byly.
        if (isset($_POST['filter_from']) && false !== strtotime($_POST['filter_from'])) {
            $filter_from = strtotime($_POST['filter_from']);
        } else if (isset($_SESSION['filter_from'])) {
            $filter_from = $_SESSION['filter_from'];
        } else {
            $filter_from = $minTimeStamp;
        }
        //jesli odjecie godziny to odejmij z filter_from tez:
        if (isset($_POST['SubmitShiftTimeMinusOne'])) {
            $filter_from = $filter_from - (60 * 60);
        }
        $_SESSION['filter_from'] = $filter_from;


        if (isset($_POST['filter_to']) && false !== strtotime($_POST['filter_to'])) {
            $filter_to = strtotime($_POST['filter_to']);
        } else if (isset($_SESSION['filter_to'])) {
            $filter_to = $_SESSION['filter_to'];
        } else {
            $filter_to = $maxTimeStamp;
        }
        // jesli dodanie godziny to dodaje do filter_to tez.
        if (isset($_POST['SubmitShiftTimePlusOne'])) {
            $filter_to = $filter_to + (60 * 60);
        }
        $_SESSION['filter_to'] = $filter_to;




        // lece po wszystkim i kolejne opracje:
        $daneFiltrowane = array();
        foreach ($dane as $k => $v) {

            if (isset($_POST['SubmitShiftTimeMinusOne'])) {
                $v['timestamp'] = $v['timestamp'] - (60 * 60);
                $v = UstawDatyZTimeStampa($v);
            }

            if (isset($_POST['SubmitShiftTimePlusOne'])) {
                $v['timestamp'] = $v['timestamp'] + (60 * 60);
                $v = UstawDatyZTimeStampa($v);
            }

            if ($v['timestamp'] <= $filter_to && $v['timestamp'] >= $filter_from) {
                $doFiltra = true;
            } else {
                $doFiltra = false;
            }

            if ($doFiltra) {
                // dodaje mass komentarze dla filtrowanych skrzynek:
                if (isset($_POST['submitCommentsForm']) && isset($_POST['logtext'])) {
                    $v['koment'] .= " " . $_POST['logtext'];
                }
            }
            $dane[$k] = $v;
            if ($doFiltra) {
                $daneFiltrowane[$k] = $v; // uzywam $k by miec te same klucze co oryginalna tablica, przyda sie pozniej.
            }
        }

        // odswiezone dane do sesji:
        $_SESSION['log_cache_multi_data'] = $dane;
        $_SESSION['log_cache_multi_filteredData'] = $daneFiltrowane;
        // oryginalna tablice mam zapisana w sesji, wiec tu spokojnie moge nadpisac do prezentacji.
        $dane = $daneFiltrowane;
    }

    tpl_set_var('filter_from', date("d-m-Y H:i", $filter_from));
    tpl_set_var('filter_to', date("d-m-Y H:i", $filter_to));
    tpl_set_var('log_cache_multi_html', $myHtml);
} // EOF user logged i jest plik

if ($no_tpl_build == false) {
    //make the template and send it out
    tpl_BuildTemplate(false);
}

function UstawDatyZTimeStampa($rekord)
{
    $rekord['rok'] = date("Y", $rekord['timestamp']);
    $rekord['msc'] = date("m", $rekord['timestamp']);
    $rekord['dzien'] = date("d", $rekord['timestamp']);
    $rekord['godz'] = date("H", $rekord['timestamp']);
    $rekord['min'] = date("i", $rekord['timestamp']);
    $rekord['data'] = date("d-m-Y H:i", $rekord['timestamp']);
    return $rekord;
}

function utf16_to_utf8($str)
{
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);

    if ($c0 == 0xFE && $c1 == 0xFF) {
        $str = substr($str, 2);
        $be = true;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $str = substr($str, 2);
        $be = false;
    } else if ($c0 != 0x00 && $c1 == 0x00) {
        $be = false;
    } else if ($c0 == 0x00 && $c1 != 0x00) {
        $be = true;
    } else {
        return $str;
    }

    $len = strlen($str);
    $dec = '';
    for ($i = 0; $i < $len; $i += 2) {
        $c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) : ord($str[$i + 1]) << 8 | ord($str[$i]);

        if ($c == 0xFEFF) {
            // Blad w GeoBeagle - wstawia BOM w srodku pliku - trzeba go ignorowac
        } else if ($c >= 0x0001 && $c <= 0x007F) {
            $dec .= chr($c);
        } else if ($c > 0x07FF) {
            $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
            $dec .= chr(0x80 | (($c >> 6) & 0x3F));
            $dec .= chr(0x80 | (($c >> 0) & 0x3F));
        } else {
            $dec .= chr(0xC0 | (($c >> 6) & 0x1F));
            $dec .= chr(0x80 | (($c >> 0) & 0x3F));
        }
    }
    return $dec;
}

function fcGetStatusyEn()
{
    $statusy = array();
    $statusy['Found it'] = 1; // Znaleziona
    $statusy['Didn\'t find it'] = 2; // Nie znaleziona
    $statusy['Unattempted'] = 3; // Komentarz
    $statusy['Needs Maintenance'] = 5; // Potrzebny serwis
    return $statusy;
}

?>
