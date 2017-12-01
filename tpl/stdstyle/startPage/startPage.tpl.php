<?php
?>
<script type="text/javascript">

//image swapping function:
    function Lite(nn) {
        document.getElementById('smallmark' + nn).style.visibility = 'hidden';
        document.getElementById('bigmark' + nn).style.visibility = 'visible';
    }

    function Unlite(nn) {
        document.getElementById('bigmark' + nn).style.visibility = 'hidden';
        document.getElementById('smallmark' + nn).style.visibility = 'visible';
    }

</script>

<!-- Page title -->
<div class="content2-pagetitle">{{what_do_you_find}}</div>

<div class="content-txtbox-noshade line-box">
    <p style="line-height: 1.6em;">{what_do_you_find_intro}<br></p>

</div>
{display_news}
<!-- Text container -->
<p class="main-totalstats">{{total_of_caches}}: <span class="content-title-noshade">{total_hiddens}</span> {{active_caches}}: <span class="content-title-noshade">{hiddens}</span> | {{number_of_founds}}: <span class="content-title-noshade">{founds}</span> | {{number_of_active_users}}: <span class="content-title-noshade">{users} </span></p>
<div class="content2-container">
    <div class="content2-container-2col-left" id="new-caches-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache">&nbsp;{{newest_caches}}</p>
        <div class="content-txtbox-noshade">
            <?php
            global $dynstylepath;
            $tmpTxt = file_get_contents($dynstylepath . "start_newcaches.inc.php");
            $tmpTxt = str_replace('hidden_by', tr('hidden_by'), $tmpTxt);
            echo $tmpTxt;
            unset($tmpTxt);
            ?>
        </div>
    </div>
    <div class="content2-container-2col-right" id="main-cachemap-block">
        <div class="img-shadow" style="position: relative">
            <?php
            global $dynstylepath;
            include ($dynstylepath . "main_cachemap.inc.php");
            ?>
        </div>
    </div>
    <div class="content2-container-2col-left" id="new-events-area">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="Event" title="Event">&nbsp;{{incomming_events}}</p>
        <?php
        global $dynstylepath;
        $tmpTxt = file_get_contents($dynstylepath . "nextevents.inc.php");
        if ($tmpTxt == '') {
            $tmpTxt = tr('list_of_events_is_empty');
        }
        $tmpTxt = str_replace('hidden_by', tr('org1'), $tmpTxt);
        echo $tmpTxt;
        unset($tmpTxt);
        ?>
    </div>

        <div class="content2-container-2col-left" id="cacheTitled" style="display: {ptDisplay};">

            <?php global $is_titled, $titled_cache_period_prefix;

                if ($is_titled == '1')
                {
                    $ntitled_cache = $titled_cache_period_prefix.'_titled_cache';
                    $tmpTxt = '<p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/TitledCache.png" class="icon32" alt="Titled Cache" title="Titled Cache">&nbsp;'.tr($ntitled_cache).'</p>';
                    $tmpTxt .= '<div class="cache-titled-content">';
                    echo $tmpTxt;
                } ?>

                {TitledCaches}

            <?php    global $is_titled;

                if ($is_titled == '1')
                {
                    $tmpTxt = '<p class="show-more"><a href="cache_titled.php" class="links">' . tr("show_more_titled_caches") . '...</a></p>';
                    $tmpTxt .= '</div><br>';
                    echo $tmpTxt;
                }
            ?>
    </div>

    <div class="content2-container-2col-left" id="ptPromo" style="display: {ptDisplay}; width: 100%">
        <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/050242-blue-jelly-icon-natural-wonders-flower13-sc36_32x32.png" class="icon32" alt="GeoPath" title="GeoPath">&nbsp;{{pt137}}</p>
        <?php
        if (file_exists($dynstylepath . 'ptPromo.inc-' . $lang . '.php'))
            include ($dynstylepath . 'ptPromo.inc-' . $lang . '.php');
        ?>
    </div>
{Feeds}
</div>
<!-- End Text Container -->

