<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" media="screen" href="<?=$view->screenCss?>">
  <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">
  <link rel="shortcut icon" href="/images/<?=$config['headerFavicon']?>">
  <link rel="apple-touch-icon-precomposed" href="/images/oc_logo_144.png">

  <?php foreach( $view->getLocalCss() as $css ) { ?>
    <link rel="stylesheet" type="text/css" href="<?=$css?>">
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
      if( $view->isFancyBoxEnabled()){
          $view->callChunk('fancyBoxLoader', true, false);
      }
      if( $view->isGMapApiEnabled()){
          $view->callChunk('googleMapsApi', $GLOBALS['googlemap_key'], $view->getLang());
      }

      foreach( $view->getLocalJs() as $js ) {
          if (! $js['defer']) {?>
            <script src="<?=$js['url']?>"<?=$js['async'] ? ' async' : ''?>></script>
<?php     }
      } //foreach-js ?>

  <title>{title}</title>
  {htmlheaders}
  {cachemap_header}
</head>
<body{bodyMod}>

  {template}

  <?php
    // fancybox js should be loaded at th end of page
    if( $view->isFancyBoxEnabled()){
        $view->callChunk('fancyBoxLoader', false, true);
    }
    // load defer JS at the end
    foreach( $view->getLocalJs() as $js ) {
        if ($js['defer']) {?>
          <script src="<?=$js['url']?>"<?=$js['async'] ? ' async' : ''?> defer></script>
<?php   } //if
    } //foreach-js ?>
</body>
</html>