<?php
/**
 * This chunk is used to load chartsJs to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load chartsJs (this chunk) in template header is to call:
 *
 *     $this->view->addHeaderChunk('chartsJs')
 */
return function (){
    //start of chunk
    ?>

<!-- chartsJs chunk -->
<script src="/js/libs/chartsJs/Chart.bundle.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/libs/chartsJs/Chart.min.css">
<!-- end of chartsJs chunk -->

<?php
}; //end of chunk
