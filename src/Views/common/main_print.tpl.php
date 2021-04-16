<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
  <title><?=$view->getSubtitle()?>{title}</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" type="text/css" media="print" href="<?=$view->printCss?>">
  <?php foreach( $view->getLocalCss() as $css ) { ?>
    <link rel="stylesheet" type="text/css" href="<?=$css?>">
  <?php } //foreach-css ?>

  <?php
    if ($view->isGoogleAnalyticsEnabled()) {
        $view->callChunkOnce( 'googleAnalytics', $view->getGoogleAnalyticsKey() );
    }

    if ($view->isjQueryEnabled()) {
        $view->callChunk('jQuery');
    }

    if ($view->isjQueryUIEnabled()) {
        $view->callChunk('jQueryUI');
    }
  ?>

</head>
<body {bodyMod}>
  {template}
</body>
</html>
