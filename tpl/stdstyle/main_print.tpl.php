<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo isset($tpl_subtitle) ? $tpl_subtitle : ''; ?>{title}</title>
        <meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta http-equiv="Content-Language" content="{lang}" />
        <meta http-equiv="gallerimg" content="no" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="cache-control" content="no-cache" />
        <!-- Favicon noch nicht vorhanden <link rel="shortcut icon" href="favicon.ico" />-->
        <link rel="stylesheet" type="text/css" href="tpl/{style}/css/style_print.css" />
        {htmlheaders}
        {cachemap_header}

        <?php
            if( $view->isGoogleAnalyticsEnabled() ){
                $view->googleAnalyticsChunk( $view->getGoogleAnalyticsKey() );
            }

            if( $view->isjQueryEnabled()){
                $view->callChunk('jQuery');
            }
        ?>

    </head>
    <body onload="load()" onunload="GNuload()">
        {template}
    </body>
</html>
