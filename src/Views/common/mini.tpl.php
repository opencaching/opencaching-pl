<?php
    use src\Models\OcConfig\OcConfig;
?>
<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" type="text/css" media="screen" href="<?=$view->screenCss?>">
  <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">

  <link rel="shortcut icon" href="<?=OcConfig::getSiteMainViewIcon('shortcutIcon')?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?=OcConfig::getSiteMainViewIcon('appleTouch')?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?=OcConfig::getSiteMainViewIcon('icon32')?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?=OcConfig::getSiteMainViewIcon('icon16')?>">
  <link rel="manifest" href="<?=OcConfig::getSiteMainViewIcon('webmanifest')?>">
  <link rel="mask-icon" href="<?=OcConfig::getSiteMainViewIcon('maskIcon')?>" color="#5bbad5">


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

      foreach ( $view->getHeaderChunks() as $chunkName => $args )
      {
          echo "<!-- load chunk $chunkName -->";
          $view->callChunk($chunkName, ...$args);
      }

      foreach ( $view->getLocalJs() as $js ) {
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
