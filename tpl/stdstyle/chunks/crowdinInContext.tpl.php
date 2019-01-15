<?php
/**
 * This chunk is used to load Crowdin-in-context to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * This chunk is autoloaded in View class
 */
return function (){
    //start of chunk
?>
<!-- Crowdin-in-context chunk -->
<script type="text/javascript">
  var _jipt = [];
  _jipt.push(['project', 'oc-polish-code-translations']);
</script>
<script src="//cdn.crowdin.com/jipt/jipt.js"></script>
<!-- End of Crowdin-in-context chunk -->
<?php
}; //end of chunk


