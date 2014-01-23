<?php

// Common configuration for TinyMCE - for cache logs and cache descriptions

function tiny_mce_compressor_config() {
    return TinyMCE_Compressor::renderTag(array(
        "url" => "lib/tinymce/tiny_mce_gzip.php",
        // The list of plugins here must contain all plugins used in configuration in desc.js.php and log.js.php
        "plugins" => "advhr,contextmenu,emotions,insertdatetime,paste,table,fullscreen,inlinepopups,autosave",
        "themes" => "advanced",
        "languages" => "cs,de,en,fr,it,lv,nl,pl,sv",
        "disk_cache" => false
    ), true);
}

?>
