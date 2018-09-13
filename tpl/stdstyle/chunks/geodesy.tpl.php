<?php
use Utils\Uri\Uri;

return function (){
    $baseUrl = "/lib/js/geodesy/1.1.3";
?>

<!-- jQuery chunk -->
<script src="<?=Uri::getLinkWithModificationTime($baseUrl."/vector3d.js")?>"></script>
<script src="<?=Uri::getLinkWithModificationTime($baseUrl."/latlon-ellipsoidal.js")?>"></script>
<script src="<?=Uri::getLinkWithModificationTime($baseUrl."/latlon-vincenty.js")?>"></script>
<script src="<?=Uri::getLinkWithModificationTime($baseUrl."/dms.js")?>"></script>
<!-- End of jQuery chunk -->

<?php
}; //end of chunk
