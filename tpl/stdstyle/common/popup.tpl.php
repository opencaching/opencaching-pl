<?php
use Utils\I18n\I18n;
?>
<!DOCTYPE html>
<html lang="<?=I18n::getCurrentLang()?>">
<head>
  <title><?php echo isset($tpl_subtitle) ? $tpl_subtitle : ''; ?>{title}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="shortcut icon" href="/images/<?=$config['headerFavicon']?>">
  <link rel="stylesheet" type="text/css" href="tpl/stdstyle/css/popup.css">

  <?php foreach( $view->getLocalCss() as $css ) { ?>
    <link rel="stylesheet" type="text/css" href="<?=$css?>">
  <?php } //foreach-css ?>

  {htmlheaders}

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
  ?>
</head>
<body{bodyMod}>
  <div id="content">
    {template}
  </div>

  <?php
      // fancyBox js should be loaded at th end of page
      if( $view->isFancyBoxEnabled()){
          $view->callChunk('fancyBoxLoader', false, true);
      }
  ?>
</body>
</html>
