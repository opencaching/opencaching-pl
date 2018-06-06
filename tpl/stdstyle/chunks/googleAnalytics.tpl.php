<?php
/**
 * This chunk is used to load GoogleAnalytics to a few main templates (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * This chunk is autoloaded in View class
 */
return function ($googleAnalyticsKey) {
    //start of chunk
?>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?=$googleAnalyticsKey?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?=$googleAnalyticsKey?>', { 'anonymize_ip': true });
</script>
<!-- End Google Analytics -->

<?php
}; //end of chunk