<?php
/*
 * This chunk is used to load fancyBox script to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load fancyBox (this chunk) in template header is to call:
 *
 *     $view->loadFancyBox()
 *
 * This chunk is autoloaded by View class
 */
return function ($loadCss = false, $loadJs = false) {
    //start of chunk
    ?>

<!-- lightbox chunk -->
<?php if($loadCss){ ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css">
<?php } ?>

<?php if($loadJs){ ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
<?php } ?>

<!-- End of fancyBox chunk -->

<?php
}; //end of chunk