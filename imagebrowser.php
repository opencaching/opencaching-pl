<?php
  // Unicode Reminder メモ

    require('./lib/common.inc.php');
    if ($error) {tpl_BuildTemplate(); exit;}

    $_REQUEST['popup'] = 'y';
    require($stylepath . '/imagebrowser.inc.php');

    $tplname = 'imagebrowser';
    $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;

    $rs = sql('SELECT `name` FROM `caches` WHERE `cache_id`=&1', $cacheid);
    $r = sql_fetch_assoc($rs);
    mysql_free_result($rs);

    if ($r !== false)
    {
        $pictures = '';
        $colcount = 0;

        $rsPictures = sql('SELECT `uuid`, `url`, `title` FROM `pictures` WHERE `object_id`=&1 AND `object_type`=2', $cacheid);
        while ($rPictures = sql_fetch_assoc($rsPictures))
        {
            if ($colcount == 0)
                $pictures .= '<tr height="' . ($thumb_max_height + 5) . 'px">';

            $pictures .= '<td valign="middle" align="center" width="' . ($thumb_max_width + 5) . 'px"><a href="javascript:SelectFile(\'' . $rPictures['url'] . '\', \'' . $absolute_server_URI . 'thumbs.php?showspoiler=1&uuid=' . $rPictures['uuid'] . '\');"><img border="0" rel="lightbox" src="thumbs.php?showspoiler=1&uuid=' . $rPictures['uuid'] . '" title="' . addslashes($rPictures['title']) . '" alt="' . addslashes($rPictures['title']) . '" /></a></td>';

            $colcount++;
            if ($colcount == 2)
            {
                $pictures .= '</tr>' . "\n";
                $colcount = 0;
            }
        }
        mysql_free_result($rsPictures);

        if ($pictures == '')
            $pictures = $nopictures;

        tpl_set_var('pictures', $pictures);
        tpl_set_var('cachename', $r['name']);
    }
    else
    {
        tpl_set_var('pictures', $nopictures);
        tpl_set_var('cachename', $cachenotexist);
    }

    tpl_set_var('cacheid', $cacheid);
    tpl_BuildTemplate();
?>
