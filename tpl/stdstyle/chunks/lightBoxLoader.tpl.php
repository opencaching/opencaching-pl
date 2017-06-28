<?php
/**
 * This chunk is used to load lightbox script to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load lightbox (this chunk) in template header is to call:
 *
 *     $view->loadLightBox()
 *
 * This chunk is autoloaded by View class
 */
return function ($loadCss=false, $loadJs=false){
    //start of chunk
?>

<!-- lightbox chunk -->
<?php if($loadCss){ ?>
	<link rel="stylesheet" href="/tpl/stdstyle/js/lightbox2/dist/css/lightbox.min.css">
<?php } ?>

<?php if($loadJs){ ?>
	<script src="/tpl/stdstyle/js/lightbox2/dist/js/lightbox.min.js"></script>
<?php } ?>

<!-- End of lightbox chunk -->

<?php
}; //end of chunk
