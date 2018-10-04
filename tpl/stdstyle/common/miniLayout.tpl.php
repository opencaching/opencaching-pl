<!DOCTYPE html>
<html lang="<?=$view->getLang()?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?=$view->_title?></title>

    <link rel="shortcut icon" href="<?=$view->_favicon?>">
    <link rel="apple-touch-icon-precomposed" href="<?=$view->_appleLogo?>">

    <?php foreach ( $view->getHeaderChunks() as $chunkName => $args ) {?>
        <!-- load chunk $chunkName -->
        <?php $view->callChunk($chunkName, ...$args); ?>
    <?php } //foreach getHeaderChunks ?>

    <?php foreach( $view->getLocalCss() as $css ) { ?>
      <link rel="stylesheet" type="text/css" href="<?=$css?>">
    <?php } //foreach-css ?>

    <?php
        $view->callChunk('bootstrapCss'); // always load bootstrap

        if( $view->isGoogleAnalyticsEnabled() ){
            $view->callChunkOnce( 'googleAnalytics', $view->getGoogleAnalyticsKey() );
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

        foreach( $view->getLocalJs() as $js ) {
            if (! $js['defer']) {?>
              <script src="<?=$js['url']?>"<?=$js['async'] ? ' async' : ''?>></script>
    <?php   }
        } //foreach-js ?>

    <?php
        // JS scripts to loaded at the end
        $view->callChunk('jQuery');      // always load jQuery
        $view->callChunk('bootstrapJs'); // always load bootstrap
        if( $view->isFancyBoxEnabled()){
            $view->callChunk('fancyBoxLoader', false, true);
        }
    ?>
<!-- (C) The Opencaching Project 2018 -->

</head>

<body>

    <?php $view-> _callTemplate(); ?>

</body>
</html>

