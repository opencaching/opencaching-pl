<?php

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Language" content="{lang}" />
        <meta http-equiv="gallerimg" content="no" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta name="KEYWORDS" content="geocaching, opencaching, skarby, poszukiwania, geocashing, longitude, latitude, utm, coordinates, treasure hunting, treasure, GPS, global positioning system, garmin, magellan, mapping, geo, hiking, outdoors, sport, hunt, stash, cache, geocaching, geocache, cache, treasure, hunting, satellite, navigation, tracking, bugs, travel bugs" />
        <meta name="author" content="Opencaching.pl " />

        <link rel="stylesheet" type="text/css" media="screen,projection" href="<?=$view->screenCss?>" />
        <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>" />
        <link rel="SHORTCUT ICON" href="favicon.ico" />
        <link rel="apple-touch-icon-precomposed" href="/images/oc_logo_144.png" />

        <?php foreach( $view->getLocalCss() as $css ) { ?>
          <link rel="stylesheet" type="text/css" href="<?=$css?>">
        <?php } //foreach-css ?>

        <?php
            if( $view->isGoogleAnalyticsEnabled() ){
                $view->googleAnalyticsChunk( $view->getGoogleAnalyticsKey() );
            }

            if( $view->isjQueryEnabled()){
                $view->callChunk('jQuery');
            }

            if( $view->isjQueryUIEnabled()){
                $view->callChunk('jQueryUI');
            }
            if( $view->isTimepickerEnabled()){
                $view->callChunk('timepicker');
            }
            if( $view->isLightBoxEnabled()){
                $view->callChunk('lightBoxLoader', true, false);
            }

            if( $view->isGMapApiEnabled()){
                $view->callChunk('googleMapsApi', $GLOBALS['googlemap_key'], $view->getLang());
            }
        ?>

        <script type="text/javascript" src="lib/enlargeit/enlargeit.js"></script>
        <title>{title}</title>
        {htmlheaders}
        {cachemap_header}
    </head>
    <body{bodyMod}>
        {template}
        
        <?php
        		// lightbox js should be loaded at th end of page
            if( $view->isLightBoxEnabled()){
                $view->callChunk('lightBoxLoader', false, true);
            }
        ?>
    </body>
</html>
