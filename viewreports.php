<?php
global $bgcolor1, $bgcolor2;
    function writeReason($type)
    {
        switch( $type )
        {
            case '1':
                return tr('cache_reports_12');
            case '2':
                return tr('cache_reports_13');
            case '3':
                return tr('cache_reports_14');
            case '4':
                return tr('cache_reports_15');
        }
    }

    function writeStatus($status)
    {
        switch( $status )
        {
            case '0':
                return "<font color='red'>".tr('cache_reports_16')."</font>";
            case '1':
                return "<font color='orange'>".tr('cache_reports_17')."</font>";
            case '2':
                return "<font color='green'>".tr('cache_reports_18')."</font>";
            case '3':
                return "<font color='blue'>".tr('cache_reports_19')."</font>";
        }
    }

    function colorCacheStatus($text, $id )
    {
        switch( $id )
        {
            case '1':
                return "<font color='green'>$text</font>";
            case '2':
                return "<font color='orange'>$text</font>";
            case '3':
                return "<font color='red'>$text</font>";
            default:
                return "<font color='gray'>$text</font>";
        }
    }

    function nonEmptyCacheName($cacheName)
    {
        if( str_replace(" ", "", $cacheName) == "" )
            return "[bez nazwy]";
        return $cacheName;
    }

    function getUsername($userid)
    {
        $sql = "SELECT username FROM user WHERE user_id='".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return null;
    }

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
    $tplname = 'viewreports';
    $content = '';
    // tylko dla członków Rady
    if ($error == false && $usr['admin'])
    {
        if( $_GET['archiwum'] == 1 )
        {
            tpl_set_var('arch_curr', "bieżących zgłoszeń");
            tpl_set_var('archiwum', 0);
            $show_archive = " reports.status = 2 AND ";
            $sorting_order = "DESC";
        }
        else
        {
            tpl_set_var('arch_curr', "archiwum");
            tpl_set_var('archiwum', 1);
            $show_archive = " reports.status <> 2 AND ";
            $sorting_order = "DESC";
        }
        $sql = "SELECT cache_status.id AS cs_id, caches.last_modified AS lastmodified,caches.user_id AS cache_ownerid,cache_status.$lang AS cache_status, reports.id as report_id, reports.user_id as user_id, reports.changed_by as changed_by, reports.changed_date as changed_date, reports.cache_id as cache_id, reports.type as type, reports.text as text, reports.submit_date as submit_date, reports.responsible_id as responsible_id, reports.status as status, user.username as username, user.user_id as user_id, caches.name as cachename,IFNULL(`cache_location`.`adm3`, '') AS `adm3`, caches.status AS c_status FROM cache_status, reports, user, (`caches` LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`) WHERE cache_status.id = caches.status AND ".sql_escape($show_archive)." user.user_id = reports.user_id AND caches.cache_id = reports.cache_id ORDER BY submit_date ".sql_escape($sorting_order);
        $query = mysql_query($sql) or die("DB error");
        $row_num = 0;
        while( $report = mysql_fetch_array($query) )
        {
            if( $row_num % 2 )
                $bgcolor = "bgcolor1";
            else
                $bgcolor = "bgcolor2";

            $content .= "<tr>\n";
            $userloginsql = "SELECT last_login FROM user WHERE user_id='".sql_escape($report['cache_ownerid'])."'";
            $userlogin_query = mysql_query($userloginsql) or die("DB error");
            if(mysql_result($userlogin_query,0)=="0000-00-00 00:00:00"){
            $userlogin="brak danych lub więcej niż 12 miesięcy temu";} else {
            $userlogin = strftime("%Y-%m-%d", strtotime(mysql_result($userlogin_query,0)));}
            if( $usr['userid'] == $report['responsible_id'])
                $addborder = "style='border-width:2px;'";
            else
                $addborder = "";
            $content .= "<td ".$addborder." class='".$bgcolor."'><span class='content-title-noshade-size05'>".$report['report_id']."</span></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><span class='content-title-noshade-size05'>".$report['submit_date']."</span></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><a class='content-title-noshade-size04' title=\"Skrzynka ostatnio modyfikowana: ".strftime("%Y-%m-%d", strtotime($report['lastmodified']))."\" href='viewcache.php?cacheid=".$report['cache_id']."'>".nonEmptyCacheName($report['cachename'])."</a> <br/> <a title=\"Użytkownik logowal się ostatnio: ".$userlogin."\" class=\"links\" href=\"viewprofile.php?userid=".$report['cache_ownerid']."\">".getUsername($report['cache_ownerid'])."</a><br/><span style=\"font-weight:bold;font-size:10px;color:blue;\">".$report['adm3']."</span></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><span class='content-title-noshade-size05'>".colorCacheStatus($report['cache_status'], $report['c_status'])."</span></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><a class='content-title-noshade-size05' href='viewreport.php?reportid=".$report['report_id']."'>".writeReason($report['type'])."</a></td></font>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><a class='content-title-noshade-size05' href='viewprofile.php?userid=".$report['user_id']."'>".$report['username']."</a></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><a class='content-title-noshade-size05' href='viewprofile.php?userid=".$report['responsible_id']."'>".getUsername($report['responsible_id'])."</a></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."' width='60'><span class='content-title-noshade-size05'>".writeStatus($report['status'])."</span></td>\n";
            $content .= "<td ".$addborder." class='".$bgcolor."'><span class='content-title-noshade-size05'>".($report['changed_by']=='0'?'':(getUsername($report['changed_by']).'<br/>('.($report['changed_date']).')'))."</span></td>\n";
            $content .= "</tr>\n";
            $row_num++;
        }
        tpl_set_var('content', $content);
    }
    else
    {
        $tplname = 'viewreports_error';
    }
    tpl_BuildTemplate();

?>
