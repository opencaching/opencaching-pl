<?php

/**
 * This chunk allow to load CSS dynamically by JS
 */

return function ($cssUrl) {

?>

<script>
var linkElement = document.createElement("link");
linkElement.rel = "stylesheet";
linkElement.href = "<?=$cssUrl?>";
linkElement.type = "text/css";
document.head.appendChild(linkElement);
</script>

<?php
};

//end of chunk - nothing should be after this line
