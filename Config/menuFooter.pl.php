<?php

/**
 * This is simple configuration of links presented in footer of the OCPL page.
 * This is configuration for OCPL only.
 *
 * Every record of $menu table should be an inline array in form:
 *
 *  [ '<translation-key-used-as-link-text>', '<url>' ],
 *
 */

// DON'T CHANGE $config var name!
$config = [

 /* [ 'translation key', 'url'                              ] */

    [ 'impressum',      $GLOBALS['wikiLinks']['impressum']  ],
    [ 'history',        $GLOBALS['wikiLinks']['history']    ],

    [ 'api',            '/okapi'                            ],
    [ 'rss',            'articles.php?page=rss'             ],
    [ 'contact',        'articles.php?page=contact'         ],
    [ 'main_page',      '/index.php?page=sitemap'           ],

];


