<?php

/**
 * This is simple configuration of links presented in sidebar of the page
 * for non-authorized users only.
 *
 * This is a DEFAULT configuration for ALL nodes.
 *
 * If you want to customize footer for your node
 * create config for your node by copied this file and changing its name.
 *
 * Every record of $menu table should be table record in form:
 *
 *  '<translation-key-used-as-link-text>', '<url>',
 *
 */

/*
 * Links config is present under $links durring load of this script
 * - feel free to use it
 */


// DON'T CHANGE $menu var name!
$menu = [
    /* 'translation key' => 'url' */

    'registration' => '/register.php',
    'news' => '/news.php',
    'rules' => $links['wiki']['rules'],

];
