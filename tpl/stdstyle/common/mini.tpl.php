<!DOCTYPE html>
<html lang="<?=$view->getLang()?>" xml:lang="<?=$view->getLang()?>">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <meta name="keywords" content="<?=$view->_keywords?>">
        <meta name="author" content="<?=$view->_siteName?>">

        <link rel="stylesheet" type="text/css" media="screen" href="<?=$view->screenCss?>">
        <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">


        <link rel="shortcut icon" href="/images/<?=$config['headerFavicon']?>">
        <link rel="apple-touch-icon-precomposed" href="/images/oc_logo_144.png">


        <link rel="stylesheet" type="text/css" media="screen,projection" href="<?=$view->screenCss?>" />
        <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>" />

        <link rel="SHORTCUT ICON" href="favicon.ico" />
        <link rel="apple-touch-icon-precomposed" href="/images/oc_logo_144.png" />

        <?php foreach( $view->getLocalCss() as $css ) { ?>
          <link rel="stylesheet" type="text/css" href="<?=$css?>">
        <?php } //foreach-css ?>

        <?php foreach( $view->getLocalJs() as $js ) { ?>
          <script async="async" src="<?=$js?>"></script>
        <?php } //foreach-css ?>


        <?php
            if( $view->isGoogleAnalyticsEnabled() ){
                $view->callChunkOnce( 'googleAnalytics', $view->getGoogleAnalyticsKey() );
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
