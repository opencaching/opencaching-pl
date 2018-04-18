<?php

use Utils\Uri\Uri;

/**
 * This chunk is used to load vue.js to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load vue.js (this chunk) in template header is to call:
 *
 *     $view->loadVueJs()
 *
 * This chunk is autoloaded in View class
 */
return function (){
    //start of chunk
?>

<!-- Start of vue.js chunk -->
<script src="<?=Uri::getLinkWithModificationTime('/tpl/stdstyle/js/vue/vue.min.js')?>"></script>
<script src="<?=Uri::getLinkWithModificationTime('/tpl/stdstyle/js/vue/vue-resource.min.js')?>"></script>
<script src="<?=Uri::getLinkWithModificationTime('/tpl/stdstyle/js/vue/vuex.min.js')?>"></script>
<!-- End of vue.js chunk -->

<?php
}; //end of chunk
