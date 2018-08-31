<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
  <title><?php echo isset($tpl_subtitle) ? $tpl_subtitle : ''; ?>{title}</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">
  <?php foreach( $view->getLocalCss() as $css ) { ?>
    <link rel="stylesheet" type="text/css" href="<?=$css?>">
  <?php } //foreach-css ?>

  {htmlheaders}
  {cachemap_header}

  <?php
    if( $view->isGoogleAnalyticsEnabled() ) {
        $view->callChunkOnce( 'googleAnalytics', $view->getGoogleAnalyticsKey() );
    }

    if( $view->isjQueryEnabled()) {
        $view->callChunk('jQuery');
    }

    if( $view->isjQueryUIEnabled()) {
        $view->callChunk('jQueryUI');
    }
    if ($view->isVueJsEnabled()) {
        $view->callChunk('vuejs');
    }
  ?>

</head>
<body{bodyMod}>
  {template}
</body>
</html>
