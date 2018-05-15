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
<script
    src="https://unpkg.com/vue@2.5.16/dist/vue.min.js"
    integrity="sha384-DwsLA0O/He+RjlS7pFkqEHfsCgdTMU+nSuUq/qkxvKSTED+s4vRttKEZtf4xTW1+"
    crossorigin="anonymous"
></script>
<script
    src="https://unpkg.com/vue-resource@1.5.0/dist/vue-resource.min.js"
    integrity="sha384-w6PAHp9EeONrNm12NmQNxnIyu3xA6xGondegkDlwhGvcx1TvCdryOXnyCUcZDCCo"
    crossorigin="anonymous"
></script>
<script
    src="https://unpkg.com/vuex@2.0.0/dist/vuex.min.js"
    integrity="sha384-VTkbLdY9G8IFsCoUDQVwFpReiiIuYpuIlclIXSmsUDeka16ux+be4CduSohbgkxO"
    crossorigin="anonymous"
></script>
<script>
<!--
if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) {
    document.write(
'<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js" integrity="sha384-2aj/bCHsl/sW9Br7V4KFv8G2BBHuiQ7ZOzY+OxTo7whNRvJY4FauRJbna1IVAvos" crossorigin="anonymous"></script>'
+'<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js" integrity="sha384-tQnfZyyk7ZX5leaWDkq9qAvwSkSvH0ouVfrxLn12X9Y2DS8nDa8pHXFH9LLKJdo/" crossorigin="anonymous"></script>'
    );
}
// -->
</script>
<!-- End of vue.js chunk -->

<?php
}; //end of chunk
