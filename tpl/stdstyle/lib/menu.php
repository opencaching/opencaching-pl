<?php
/***************************************************************************
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.

                'filename' => 'viewprofile.php?userid='.$usr['userid'].'',
*
***************************************************************************/
/****************************************************************************
 *
 *  Unicode Reminder ??
 *
 * $menu contains the entire menu structure
 *
 * possible array elements are:
 *
 * title         displayed HTML title
 * menustring    displayed menu text
 * siteid        unique id of this page
 * visible       bool, if true this site is shown in the menu structure
 * quicklinks    array of relativ pages
 *               (contains assotiativ array with href and text for each page)
 * submenu       array of submenues. Only the first 3 levels are displayed, deeper levels
 *               are only for the breadcrump. Each submenu has the same structure as $menu
 * navicolor     (only top-level menus) backgroundcolor of the menu
 * filename      filename for href
 *
        array(
        'title' => 'Mapa skrzynek OC PL',
        'menustring' => 'Mapa skrzynek OC PL',
        'siteid' => 'cachemap',
        'visible' => false,
        'filename' => 'cachemap.php'
    ),

 ****************************************************************************/
include_once('lib/language.inc.php');
global $menu, $usr, $lang, $cache_menu, $stat_menu, $wiki_url, $SiteOutsideCountryString, $config,
    $powerTrailModuleSwitchOn, $powerTrailFaqLink, $forum_url, $blogsite_url;

$menu = array(
    array(
        'title'         => tr('main_page'),
        'menustring'    => tr('main_page'),
        'siteid'        => array('start', 'articles/impressum'),
        'visible'       => true,
        'filename'      => 'index.php',
        'submenu'       => array(
            array(
                'title'         => tr('registration'),
                'menustring'    => tr('registration'),
                'visible'       => true,
                'filename'      => 'register.php',
                'siteid'        => 'register',
                'submenu'       => array(
                    array(
                        'title'         => tr('account_activation'),
                        'menustring'    => tr('account_activation'),
                        'visible'       => false,
                        'filename'      => 'activation.php',
                        'siteid'        => 'activation'
                    ),
                    array(
                        'title'         => tr('account_activation'),
                        'menustring'    => tr('account_activation'),
                        'visible'       => false,
                        'filename'      => 'activation.php',
                        'siteid'        => 'activation_confirm'
                    )
                )
            ),
            array(
                'title'         => tr('news'),
                'menustring'    => tr('news'),
                'visible'       => true,
                'filename'      => 'news.php',
                'siteid'        => 'news'
            ),
            array(
                'title'         => tr('new_caches'),
                'menustring'    => tr('new_caches'),
                'visible'       => true,
                'filename'      => 'newcaches.php',
                'siteid'        => 'newcaches',
                'submenu'       => array(
                    array(
                        'title'         => tr($SiteOutsideCountryString),
                        'menustring'    => tr($SiteOutsideCountryString),
                        'visible'       => true,
                        'onlylogged'    => true,
                        'filename'      => 'newcachesrest.php',
                        'siteid'        => 'newcachesrest'
                    )
                )
            ),
            array(
                'title'         => tr('new_logs'),
                'menustring'    => tr('new_logs'),
                'visible'       => true,
                'filename'      => 'newlogs.php',
                'siteid'        => 'newlogs'
            ),
            array(
                'title'         => tr('incomming_events'),
                'menustring'    => tr('incomming_events'),
                'visible'       => true,
                'filename'      => 'newevents.php',
                'siteid'        => 'newevents'
            ),
            array(
                'title'         => tr('cache_map'),
                'menustring'    => tr('cache_map'),
                'siteid'        => 'cachemap3',
                'visible'       => true,
                'onlylogged'    => true,
                // On touch devices use full-screen map by default
                // **** Check for touch device below should be kept in sync with analogous check in lib/cachemap3.js ****
                'filename'      => 'javascript:window.location=(((\'ontouchstart\' in window) || (navigator.msMaxTouchPoints > 0)) ? \'cachemap-full.php\' : \'cachemap3.php\');',
                'submenu'       => array(
                    array(
                        'title'         => tr('cache_mapv2'),
                        'menustring'    => tr('cache_mapv2'),
                        'siteid'        => 'cachemap2',
                        'visible'       => $config['map2SwithedOn'],
                        'onlylogged'    => true,
                        'filename'      => 'cachemap2.php'
                    ),
                    array(
                        'title'         => tr('logmap_04'),
                        'menustring'    => tr('logmap_04'),
                        'siteid'        => 'logmap',
                        'visible'       => true,
                        'onlylogged'    => true,
                        'filename'      => 'logmap.php'
                    )

                )
            ),
            array(
                'title'         => tr('search_cache'),
                'menustring'    => tr('search_cache'),
                'siteid'        => 'search',
                'onlylogged'    => true,
                'visible'       => true,
                'filename'      => 'search.php'
            ),
            array(
                'title'         => tr('recommended_caches'),
                'menustring'    => tr('recommended_caches'),
                'visible'       => true,
                'onlylogged'    => true,
                'filename'      => 'cacheratings.php',
                'siteid'        => 'cacheratings'
            ),
            array(
                'title'         => tr('statistics'),
                'menustring'    => tr('statistics'),
                'visible'       => true,
                'onlylogged'    => true,
                'filename'      => 'articles.php?page=stat',
                'siteid'        => array (
                    'articles/stat',
                    'articles/s1',
                    'articles/s1b',
                    'articles/s2',
                    'articles/s3',
                    'articles/s4',
                    'articles/s5',
                    'articles/s6',
                    'articles/s7',
                    'articles/s8',
                    'articles/s11a',
                    'articles/s12',
                    'articles/s102'
                )
            ),
            array(
                'title'         => tr('rules'),
                'visible'       => true,
                'filename'      => tr('filename_rules'),
                'menustring'    => tr('rules'),
                'newwindow'     => true,
                'siteid'        => 'articles/regulamin'
            ),
            array(
                'title'         => tr('links'),
                'menustring'    => tr('links'),
                'siteid'        => 'articles/links',
                'navicolor'     => '#FFFFC5',
                'visible'       => false,
                'filename'      => 'articles.php?page=links'
            ), 
            array(
                'title'         => tr('pt001'),
                'menustring'    => tr('pt001'),
                'siteid'        => 'powerTrail',
                'visible'       => $powerTrailModuleSwitchOn,
                'filename'      => 'powerTrail.php',
                'submenu'       => array(
                    array(
                        'title'         => tr('pt155'),
                        'menustring'    => tr('pt155'),
                        'siteid'        => 'geoSciezkiFAQ',
                        'visible'       => $powerTrailModuleSwitchOn,
                        'filename'      => $powerTrailFaqLink,
                    ),),
            )
        )
    ),
    array(
        'title'         => tr('abc'),
        'menustring'    => tr('abc'),
        'siteid'        => 'articles/info',
        'visible'       => true,
        'filename'      => $wiki_url,
        'newwindow'     => true
    ),
    array(
        'title'         => tr('forum'),
        'menustring'    => tr('forum'),
        'siteid'        => 'forum',
        'visible'       => true,
        'filename'      => $forum_url,
        'newwindow'     => true
    ),
    array(
        'title'         => 'Blog',
        'menustring'    => 'Blog',
        'siteid'        => 'blog',
        'visible'       => true,
        // TODO: remove isset() condition once settings.inc.php are updated on production
        'filename'      => isset($blogsite_url) ? $blogsite_url : 'http://blog.opencaching.pl',
        'newwindow'     => true
    ),
    array(
        'title'         => 'ChatBox',
        'menustring'    => 'ChatBox',
        'siteid'        => 'chatbox',
        'visible'       => false, // Chat disabled because of server performance issues
        'filename'      => 'http://opencaching.pl/chat',
        'newwindow'     => true
    ),
    array(
        'title'         => 'IRC',
        'menustring'    => 'IRC',
        'siteid'        => 'irc',
        'visible'       => false,
        'filename'      => isset($usr['username'])?'http://webchat.freenode.net/?nick='.$usr['username'].'&amp;channels=opencaching.pl&amp;prompt=1':'',
        'newwindow'     => true
    ),
    array(
        'title'         => tr('geokrets'),
        'menustring'    => tr('geokrets'),
        'siteid'        => 'GeoKrety',
        'visible'       => true,
        'filename'      => 'http://geokrety.org/',
        'newwindow'     => 'true'
    ),
    array(
        'title'         => 'GeoCoin',
        'menustring'    => 'GeoCoin',
        'siteid'        => 'GeoCoin',
        'visible'       => false,
        'filename'      => 'http://opengeocoin.org/pl/',
        'newwindow'     => 'true'
    ),
    array(
        'title'         => 'Download',
        'menustring'    => 'Download',
        'siteid'        => 'Download',
        'visible'       => true,
        'filename'      => $wiki_url.'/index.php/U%C5%BCyteczne_pliki_zwi%C4%85zane_z_OC_PL',
        'newwindow'     => 'true'
    ),

        array(
        'title'         => tr('links'),
        'visible'       => true,
        'filename'      => 'articles.php?page=links',
        'menustring'    => tr('links'),
        'siteid'        => 'articles/links'
    ),
    array(
        'title'         => 'RSS',
        'menustring'    => 'RSS',
        'siteid'        => 'articles/rss',
        'visible'       => true,
        'filename'      => 'articles.php?page=rss'
    ),
    array(
        'title'         => tr('contact'),
        'visible'       => true,
        'filename'      => 'articles.php?page=contact',
        'menustring'    => tr('contact'),
        'siteid'        => 'articles/contact'
    ),
    array(
        'title'         => tr('guides'),
        'menustring'    => tr('guides'),
        'visible'       => true,
        'filename'      => 'cacheguides.php',
        'siteid'        => 'cacheguides'
    ),
    array(
        'title'         => 'API',
        'menustring'    => 'API',
        'visible'       => true,
        'filename'      => '/okapi',
        'siteid'        => 'api'
    ),
    array(
        'title'         => tr('clipboard'),
        'menustring'    => tr('clipboard'),
        'siteid'        => 'mylist',
        'visible'       => false,
        'filename'      => 'mylist.php'
    ),
    array(
        'title'         => tr('login').'/'.tr('logout'),
        'visible'       => false,
        'filename'      => 'login.php',
        'menustring'    => tr('login').'/'.tr('logout'),
        'navicolor'     => '#E8DDE4',
        'siteid'        => 'login'
    ),
    array(
        'title'         => tr('add_picture'),
        'visible'       => false,
        'filename'      => 'newpic.php',
        'menustring'    => tr('add_picture'),
        'siteid'        => 'newpic'
    ),
    array(
        'title'         => tr('edit_picture'),
        'visible'       => false,
        'filename'      => 'editpic.php',
        'menustring'    => tr('edit_picture'),
        'siteid'        => 'editpic'
    ),
    array(
        'title'         => tr('new_password'),
        'visible'       => false,
        'filename'      => 'newpw.php',
        'menustring'    => tr('new_password'),
        'siteid'        => 'newpw'
    ),
    array(
        'title'         => tr('new_topic'),
        'visible'       => false,
        'filename'      => 'newstopic.php',
        'menustring'    => tr('new_topic'),
        'siteid'        => 'newstopic',
        'showsitemap'   => false
    ),
    array(
        'title'         => tr('page_error'),
        'menustring'    => tr('page_error'),
        'visible'       => false,
        'filename'      => 'index.php',
        'siteid'        => 'error'
    ),
    array(
        'title'         => tr('contact'),
        'visible'       => false,
        'filename'      => 'articles.php?page=contact',
        'menustring'    => tr('contact'),
        'siteid'        => 'articles/contact'
    ),
    array(
        'title'         => tr('personal_data'),
        'visible'       => false,
        'filename'      => 'articles.php?page=dsb',
        'menustring'    => tr('personal_data'),
        'siteid'        => 'articles/dsb'
    ),
    array(
        'title'         => tr('message'),
        'visible'       => false,
        'filename'      => 'index.php',
        'menustring'    => tr('message'),
        'siteid'        => 'message'
    ),
    array(
        'title'         => tr('register_confirm'),
        'visible'       => false,
        'filename'      => 'register.php',
        'menustring'    => tr('register_confirm'),
        'siteid'        => 'register_confirm'
    ),
    array(
        'title'         => tr('cache_map'),
        'visible'       => false,
        'filename'      => 'cachemap.php',
        'menustring'    => tr('cache_map'),
        'siteid'        => 'mapa1'
    ),
    array(
        'title'         => tr('main_page'),
        'visible'       => false,
        'filename'      => 'index.php',
        'menustring'    => tr('main_page'),
        'siteid'        => 'sitemap'
    ),
    array(
        'title'         => tr('add_newmp3'),
        'visible'       => false,
        'filename'      => 'newmp3.php',
        'menustring'    => tr('add_newmp3'),
        'siteid'        => 'newmp3'
    ),

    // OC management
    array(
        'title'         => tr('administration'),
        'menustring'    => tr('administration'),
        'siteid'        => 'viewreports',
        'visible'       => false,
        'filename'      => 'viewreports.php',
        'submenu'       => array(
            array(
                'title'         => tr('reports'),
                'menustring'    => tr('reports'),
                'siteid'        => 'viewreports',
                'visible'       => true,
                'filename'      => 'viewreports.php'
            ),
            array(
                'title'         => tr('pendings'),
                'menustring'    => tr('pendings'),
                'siteid'        => 'viewpendings',
                'visible'       => true,
                'filename'      => 'viewpendings.php'
            ),
            array(
                'title'         => tr('stat_octeam'),
                'menustring'    => tr('stat_octeam'),
                'siteid'        => 'articles/bog',
                'visible'       => true,
                'filename'      => 'articles.php?page=cog'
            ),
            array(
                'title'         => tr('cache_notfound'),
                'menustring'    => tr('cache_notfound'),
                'siteid'        => 'admin_cachenotfound',
                'visible'       => true,
                'filename'      => 'admin_cachenotfound.php'
            ),
            array(
                'title'         => tr('search_user'),
                'menustring'    => tr('search_user'),
                'siteid'        => 'admin_searchuser',
                'visible'       => true,
                'filename'      => 'admin_searchuser.php'
            ),
            array(
                'title'         => tr('add_news'),
                'menustring'    => tr('add_news'),
                'siteid'        => 'admin_addnews',
                'visible'       => true,
                'filename'      => 'admin_addnews.php'
            ),
            array(
                'title'         => tr('send_bulletin'),
                'menustring'    => tr('send_bulletin'),
                'siteid'        => 'admin_bulletin',
                'visible'       => true,
                'filename'      => 'admin_bulletin.php'
            ),
            array(
                'title'         => tr('pt208'),
                'menustring'    => tr('pt208'),
                'siteid'        => 'powerTrailCOG',
                'visible'       => $powerTrailModuleSwitchOn,
                'filename'      => 'powerTrailCOG.php'
            )
        )
    ),
    // My profile (my home) my_neighborhood
    array(
        'title'         => tr('user_menu'),
        'menustring'    => tr('user_menu'),
        'siteid'        => 'myhome',
        'visible'       => false,
        'filename'      => isset($usr['userid'])?'viewprofile.php?userid='.$usr['userid']:'',
        'navicolor'     => '#D5D9FF',
        'submenu'       => array(
            array(
                'title'         => tr('new_cache'),
                'menustring'    => tr('new_cache'),
                'visible'       => true,
                'filename'      => 'newcache.php',
                'siteid'        => array('newcache_info', 'newcache_forbidden', 'newcache_beginner', 'newcache')
            ),

            array(
                'title'         => tr('my_caches'),
                'menustring'    => tr('my_caches'),
                'visible'       => true,
                'filename'      => 'mycaches.php',
                'siteid'        => 'mycaches'
            ),
            array(
                'title'         => tr('my_neighborhood'),
                'menustring'    => tr('my_neighborhood'),
                'visible'       => true,
                'filename'      => 'myneighborhood.php',
                'siteid'        => 'myneighborhood'
            ),
            array(
                'title'         => tr('my_neighborhood'),
                'menustring'    => tr('my_neighborhood_v2'),
                'visible'       => false,
                'filename'      => 'myneighborhood_jg.php',
                'siteid'        => 'myneighborhood_jg'
            ),
            array(
                'title'         => tr('myroutes'),
                'menustring'    => tr('myroutes'),
                'visible'       => true,
                'filename'      => 'myroutes.php',
                'siteid'        => 'myroutes'
            ),
            array(
                'title'         => tr('mycache_note'),
                'menustring'    => tr('mycache_note'),
                'visible'       => true,
                'filename'      => 'mycache_notes.php',
                'siteid'        => 'mycache_notes'
            ),
            array(
                'title'         => tr('my_statistics'),
                'menustring'    => tr('my_statistics'),
                'visible'       => true,
                'filename'      => isset($usr['userid'])?'viewprofile.php?userid='.$usr['userid']:'',
                'siteid'        => array('myhome', 'viewprofile', 'ustat', 'my_logs')
            ),
            array(
                'title'         =>'Field Notes',
                'menustring'    => 'Field Notes',
                'visible'       => true,
                'filename'      => 'log_cache_multi_send.php',
                'siteid'        => 'log_cache_multi_send'   //JG 2013-10-25 było 'autolog'
            ),
            array(
                'title'         => tr('new_logs'),
                'menustring'    => tr('new_logs'),
                'visible'       => false,
                'filename'      => 'myn_newlogs.php',
                'siteid'        => 'myn_newlogs'
                ),

            array(
                'title'         => tr('new_logs'),
                'menustring'    => tr('new_logs'),
                'visible'       => false,
                'filename'      => 'myn_newcaches.php',
                'siteid'        => 'myn_newcaches'
                ),

            array(
                'title'         => tr('new_logs'),
                'menustring'    => tr('new_logs'),
                'visible'       => false,
                'filename'      => 'myn_newcaches.php',
                'siteid'        => 'myn_topcaches'
                ),
            array(
                'title'         => tr('my_logs'),
                'menustring'    => tr('my_logs'),
                'visible'       => false,
                'filename'      => 'myhome2.php',
                'siteid'        => 'myhome2'
            ),
            array(
                'title'         => tr('my_account'),
                'menustring'    => tr('my_account'),
                'visible'       => true,
                'filename'      => 'myprofile.php',
                'siteid'        => 'myprofile',
                'submenu'       => array(
                    array(
                        'title'         => tr('change_data'),
                        'menustring'    => tr('change_data'),
                        'visible'       => false,
                        'filename'      => 'myprofile.php?action=change',
                        'siteid'        => 'myprofile_change',
                    ),
                    array(
                        'title'         => tr('change_email'),
                        'menustring'    => tr('change_email'),
                        'visible'       => false,
                        'filename'      => 'newemail.php',
                        'siteid'        => 'newemail',
                    ),
                    array(
                        'title'         => tr('change_password'),
                        'menustring'    => tr('change_password'),
                        'visible'       => false,
                        'filename'      => 'newpw.php',
                        'siteid'        => 'newpw',
                    ),
                    array(
                        'title'         => tr('choose_statpic'),
                        'menustring'    => tr('choose_statpic'),
                        'visible'       => false,
                        'filename'      => 'change_statpic.php',
                        'siteid'        => 'change_statpic')
                )
            ),
            array(
                'title'         => tr('settings_notifications'),
                'menustring'    => tr('settings_notifications'),
                'visible'       => true,
                'filename'      => 'mywatches.php?rq=properties',
                'siteid'        => 'mywatches_properties'

            ),
            array(
                'title'         => tr('clipboard'),
                'menustring'    => tr('clipboard'),
                'siteid'        => 'mylist',
                'visible'       => false,
                'filename'      => 'mylist.php'
            ),
            array(
                'title'         => tr('collected_queries'),
                'menustring'    => tr('collected_queries'),
                'visible'       => true,
                'filename'      => 'query.php',
                'siteid'        => 'viewqueries'
            ),
            array(
                'title'         => tr('watched_caches'),
                'menustring'    => tr('watched_caches'),
                'visible'       => true,
                'filename'      => 'mywatches.php',
                'siteid'        => 'mywatches',
                'submenu'       => array(
                    array(
                        'title'         => tr('map_watched_caches'),
                        'menustring'    => tr('map_watched_caches'),
                        'visible'       => true,
                        'filename'      => 'mywatches.php?rq=map',
                        'siteid'        => 'mywatches_map',
                    ),
                ),
            ),
            array(
                'title'         => tr('ignored_caches'),
                'menustring'    => tr('ignored_caches'),
                'visible'       => true,
                'filename'      => 'myignores.php',
                'siteid'        => 'myignores'
            ),
            array(
                'title'         => tr('my_recommendations'),
                'menustring'    => tr('my_recommendations'),
                'visible'       => true,
                'filename'      => 'mytop5.php',
                'siteid'        => 'mytop5'
            ),
            array(
                'title'         => tr('adoption_cache'),
                'menustring'    => tr('adoption_cache'),
                'visible'       => true,
                'filename'      => 'chowner.php',
                'siteid'        => 'chowner'
            ),
            array(
                'title'         => tr('search_user'),
                'menustring'    => tr('search_user'),
                'siteid'        => 'searchuser',
                'visible'       => true,
                'filename'      => 'searchuser.php'
            ),
            array(
                'title'         => tr('Open_Sprawdzacz'),
                'menustring'    => tr('Open_Sprawdzacz'),
                'visible'       => true,
                'filename'      => 'opensprawdzacz.php',
                'siteid'        => 'opensprawdzacz'
            ),
            array(
                'title'         => tr('okapi_apps'),
                'menustring'    => tr('okapi_apps'),
                'siteid'        => 'okapi_apps',
                'visible'       => true,
                'filename'      => 'okapi/apps/?langpref='.$GLOBALS['lang']
            ),
            array(
                'title'         => 'QR Code',
                'menustring'    => 'QR Code',
                'siteid'        => 'qrcode',
                'visible'       => true,
                'filename'      => 'qrcode.php'
            )
        )
    ),
    // Caches
    array(
        'title'         => tr('caches'),
        'menustring'    => tr('caches'),
        'siteid'        => 'search',
        'visible'       => false,
        'filename'      => 'search.php',
        'navicolor'     => '#BDE3E7',
        'submenu'       => array(
            array(
                'title'         => tr('search'),
                'menustring'    => tr('search'),
                'visible'       => true,
                'filename'      => 'search.php',
                'siteid'        => 'search',
                'submenu'       => array(
                    array(
                        'title'         => tr('view_cache'),
                        'menustring'    => tr('view_cache'),
                        'visible'       => false,
                        'filename'      => 'viewcache.php',
                        'siteid'        => 'viewcache',
                        'submenu'       => array(
                            array(
                                'title'         => tr('new_log_entry'),
                                'menustring'    => tr('new_log_entry'),
                                'visible'       => false,
                                'filename'      => 'log.php',
                                'siteid'        => 'log_cache'
                            ),
                            array(
                                'title'         => tr('edit_log'),
                                'menustring'    => tr('edit_log'),
                                'visible'       => false,
                                'filename'      => 'editlog.php',
                                'siteid'        => 'editlog'
                            ),
                            array(
                                'title'         => tr('remove_log'),
                                'menustring'    => tr('remove_log'),
                                'visible'       => false,
                                'filename'      => 'removelog.php',
                                'siteid'        => 'removelog_logowner'
                            ),
                            array(
                                'title'         => tr('remove_log'),
                                'menustring'    => tr('remove_log'),
                                'visible'       => false,
                                'filename'      => 'removelog.php',
                                'siteid'        => 'removelog_cacheowner'
                            ),
                            array(
                                'title'         => tr('edit_cache'),
                                'menustring'    => tr('edit_cache'),
                                'visible'       => false,
                                'filename'      => 'editcache.php',
                                'siteid'        => 'editcache'
                            ),
                            array(
                                'title'         => tr('new_desc'),
                                'menustring'    => tr('new_desc'),
                                'visible'       => false,
                                'filename'      => 'newdesc.php',
                                'siteid'        => 'newdesc'
                            ),
                            array(
                                'title'         => tr('edit_desc'),
                                'menustring'    => tr('edit_desc'),
                                'visible'       => false,
                                'filename'      => 'editdesc.php',
                                'siteid'        => 'editdesc'
                            ),
                            array(
                                'title'         => tr('remove_desc'),
                                'menustring'    => tr('remove_desc'),
                                'visible'       => false,
                                'filename'      => 'removedesc.php',
                                'siteid'        => 'removedesc'
                            )
                        )
                    ),
                    array(
                        'title'         => tr('search_loc'),
                        'menustring'    => tr('search_loc'),
                        'visible'       => false,
                        'filename'      => 'search.php',
                        'siteid'        => 'selectlocid',
                    ),
                    array(
                        'title'         => tr('search_results'),
                        'menustring'    => tr('search'),
                        'visible'       => false,
                        'filename'      => 'search.php',
                        'siteid'        => 'search.result.caches',
                    ),
                    array(
                        'title'         => tr('show_log'),
                        'menustring'    => tr('show_log'),
                        'visible'       => false,
                        'filename'      => 'viewlogs.php',
                        'siteid'        => 'viewlogs',
                    ),
                    array(
                        'title'         => tr('store_queries'),
                        'menustring'    => tr('store_queries'),
                        'visible'       => false,
                        'filename'      => 'query.php?action=save',
                        'siteid'        => 'savequery'
                    ),
                    array(
                        'title'         => tr('cache_recommendation'),
                        'menustring'    => tr('cache_recommendation'),
                        'visible'       => false,
                        'filename'      => 'recommendations.php',
                        'siteid'        => 'recommendations'
                    )
                )
            ),
            array(
                'title'         => tr('new_cache'),
                'menustring'    => tr('new_cache'),
                'visible'       => true,
                'filename'      => 'newcache.php',
                'siteid'        => 'newcache',
                'submenu'       => array(
                    array(
                        'title'         => tr('cache_descriptions'),
                        'menustring'    => tr('cache_descriptions'),
                        'visible'       => true,
                        'filename'      => tr('filename_describe_cache'),
                        'siteid'        => 'articles/cacheinfo'
                    ),
                    array(
                        'title'         => tr('html_preview'),
                        'menustring'    => tr('html_preview'),
                        'visible'       => false,
                        'filename'      => 'htmlprev.php',
                        'siteid'        => 'htmlprev',
                        'submenu'       => array(
                            array(
                                'title'         => tr('html_preview'),
                                'menustring'    => tr('html_preview'),
                                'visible'       => false,
                                'filename'      => 'htmlprev.php',
                                'siteid'        => 'htmlprev_step2'
                            ),
                            array(
                                'title'         => tr('html_preview'),
                                'menustring'    => tr('html_preview'),
                                'visible'       => false,
                                'filename'      => 'htmlprev.php',
                                'siteid'        => 'htmlprev_step3'
                            ),
                            array(
                                'title'         => tr('html_preview'),
                                'menustring'    => tr('html_preview'),
                                'visible'       => false,
                                'filename'      => 'htmlprev.php',
                                'siteid'        => 'htmlprev_step3err'
                            )
                        )
                    ),
                    array(
                        'title'         => tr('allowed_html_tags'),
                        'menustring'    => tr('allowed_html_tags'),
                        'visible'       => true,
                        'filename'      => 'articles.php?page=htmltags',
                        'siteid'        => 'articles/htmltags'
                    )
                )
            ),
            array(
                'title'         => tr('special_caches'),
                'menustring'    => tr('special_caches'),
                'visible'       => false,
                'filename'      => 'articles.php?page=specialcaches',
                'siteid'        => 'articles/specialcaches'
            ),
            array(
                'title'         => tr('user_ident'),
                'menustring'    => tr('user_ident'),
                'filename'      => 'viewprofile.php',
                'siteid'        => 'viewprofile',
                'visible'       => false
            ),
            array(
                'title'         => tr('recommendations'),
                'menustring'    => tr('recommendations'),
                'filename'      => 'usertops.php',
                'siteid'        => 'usertops',
                'visible'       => false
            )
        )
    ),
    $cache_menu,
    $stat_menu
);


function mnu_MainMenuIndexFromPageId($menustructure, $pageid) {
    /* selmenuitem contains the selected (bold) menu item */
    global $mnu_selmenuitem;

    for ($i = 0, $ret = -1; ($i < count($menustructure)) && ($ret == -1); $i++) {
        if ($menustructure[$i]['siteid'] === $pageid 
                || is_array($menustructure[$i]['siteid']) && in_array($pageid, $menustructure[$i]['siteid']))
        {
            $mnu_selmenuitem = $menustructure[$i];
            return $i;
        }
        else  {
            if (isset($menustructure[$i]['submenu'])) {
                $ret = mnu_MainMenuIndexFromPageId($menustructure[$i]['submenu'], $pageid);
                if ($ret != -1) return $i;
            }
        }
    }
    return $ret;
}

/*
 * mnu_EchoMainMenu - echos the top level menus
 *
 * selmenuid   p.e. mnu_MainMenuIndexFromPageId($menu, $siteid)
 */
function mnu_EchoMainMenu($selmenuid) {
    global $menu;
    $c = 0;
    for ($i = 0; $i < count($menu); $i++) {
        if ($menu[$i]['visible'] == true) {
            if (!isset($menu[$i]['newwindow'])) $menu[$i]['newwindow'] = false;
            if( $menu[$i]['newwindow'] == true )
                $target_blank = "target='_blank'";
            else
                $target_blank = "";

            if ($menu[$i]['siteid'] == $selmenuid
                || is_array($menu[$i]['siteid']) && in_array($selmenuid, $menu[$i]['siteid']))
            {
                echo '<li><a class="selected bg-green06" href="' . $menu[$i]['filename'] . '">' . htmlspecialchars($menu[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></li>';
            }
            else {
                echo '<li><a '.$target_blank.' href="' . $menu[$i]['filename'] . '">' . htmlspecialchars($menu[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></li>';
            }

            $c++;
        }
    }
}

/*
 * mnu_EchoSubMenu - echos the 2. and 3. menu level
 *
 * menustructure   $menu
 * pageid          siteid to search for
 * level           has to be 1
 * bHasSubmenu     has to be false
 */
function mnu_EchoSubMenu($menustructure, $pageid, $level, $bHasSubmenu) {
    global $mnu_bgcolor;
    global $usr;

    if (!$bHasSubmenu) {
        for ($i = 0, $bSubmenu = false; ($i < count($menustructure)) && ($bSubmenu == false); $i++) {
            if (isset($menustructure[$i]['submenu'])) {
                $bSubmenu = true;
            }
        }
    }

    if (!$bHasSubmenu) {
        $cssclass = 'group';
    }
    else {
        if ($level == 1) {
            $cssclass = 'group';
        }
        else {
            $cssclass = 'subgroup';
        }
    }

    for ($i = 0; $i < count($menustructure); $i++) {
        if (!isset($menustructure[$i]['newwindow'])) $menustructure[$i]['newwindow'] = false;
        if( $menustructure[$i]['newwindow'] == true )
            $target_blank = "target='_blank'";
        else
            $target_blank = "";
        if ($menustructure[$i]['visible'] == true) {
            if (!isset($menustructure[$i]['icon'])) $menustructure[$i]['icon'] = false;
            if($menustructure[$i]['icon']) {
                $icon = 'style="background-image: url('.$menustructure[$i]['icon'].'-18.png);background-repeat:no-repeat;"';
            }
            else
                $icon = "";
            if (!isset ($menustructure[$i]['onlylogged'])) $menustructure[$i]['onlylogged'] = false;
            if($menustructure[$i]['onlylogged'] == true && $usr == false) {
                continue;
            }

            if ($menustructure[$i]['siteid'] === $pageid 
                    || is_array($menustructure[$i]['siteid']) && in_array($pageid, $menustructure[$i]['siteid']))
            {
                echo '<li class="'.$cssclass.' '.$cssclass.'_active "><a '.$target_blank.' href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></li>' . "\n";
            }
            else {
                echo '<li class="' . $cssclass . '"><a '.$icon.' '.$target_blank.' href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></li>' . "\n";
            }

            if (isset($menustructure[$i]['submenu'])) {
                mnu_EchoSubMenu($menustructure[$i]['submenu'], $pageid, $level + 1, true);
            }
        }
    }
}

/*
 * mnu_IsMenuParentOf - returns true if menuitemid is part of $parentmenuitems, otherwise false
 *
 * parentmenuitems   p.e. $menu
 * menuitemid        siteid to search for
 */
function mnu_IsMenuParentOf($parentmenuitems, $menuitemid) {
    for ($i = 0; $i < count($parentmenuitems); $i++) {
        if ($parentmenuitems[$i]['siteid'] === $menuitemid
            || is_array($parentmenuitems[$i]['siteid']) && in_array($menuitemid, $parentmenuitems[$i]['siteid']))
        {
            return true;
        }

        if (isset($parentmenuitems[$i]['submenu'])) {
            $ret = mnu_IsMenuParentOf($parentmenuitems[$i]['submenu'], $menuitemid);
            if ($ret == true) return true;
        }
    }

    return false;
}

/*
 * mnu_EchoBreadCrumb - echos the breadcrumb
 *
 * pageid          siteid to search for
 * mainmenuindex   index of the top level menu
 */
function mnu_EchoBreadCrumb($pageid, $mainmenuindex) {
    global $menu;

    echo htmlspecialchars($menu[$mainmenuindex]['menustring'], ENT_COMPAT, 'UTF-8');

    if (isset($menu[$mainmenuindex]['submenu']) && !(
            $menu[$mainmenuindex]['siteid'] === $pageid
            || is_array($menu[$mainmenuindex]['siteid']) && in_array($pageid, $menu[$mainmenuindex]['siteid'])))
    {
        mnu_prv_EchoBreadCrumbSubItem($pageid, $menu[$mainmenuindex]['submenu']);
    }
}

/*
 * mnu_prv_EchoBreadCrumbSubItem - private helper function
 */
function mnu_prv_EchoBreadCrumbSubItem($pageid, $menustructure) {
    for ($i = 0; $i < count($menustructure); $i++) {
        if ($menustructure[$i]['siteid'] == $pageid
            || is_array($menustructure[$i]['siteid']) && in_array($pageid, $menustructure[$i]['siteid'])) 
        {
            echo '&nbsp;&gt;&nbsp;' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8');
            return;
        }
        else {
            if (isset($menustructure[$i]['submenu'])) {
                if (mnu_IsMenuParentOf($menustructure[$i]['submenu'], $pageid)) {
                    echo '&nbsp;&gt;&nbsp;' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8');
                    mnu_prv_EchoBreadCrumbSubItem($pageid, $menustructure[$i]['submenu']);
                    return;
                }
            }
        }
    }
}

/*
 * mnu_EchoQuicklinks - echos the 'relative pages'
 *
 * selmenuitem   siteid
 */
function mnu_EchoQuicklinks($selmenuitem) {
    if (isset($selmenuitem['quicklinks'])) {
        echo '<div style="float:right;font-family:sans-serif;font-size:small;">' . "\n";
        echo '<br/>' . "\n";
        echo '<table cellpadding="0" cellspacing="0" id="quicklinksbox">' . "\n";
        echo '<tr><td id="quicklinkstop">Strony zwiazane z OC.pl:</td></tr>' . "\n";

        for ($i = 0; $i < count($selmenuitem['quicklinks']); $i++) {
            if ($i == 0) {
                echo '<tr><td class="navilevel2n" style="padding-top:1ex;">';
            }
            else {
                echo '<tr><td class="navilevel2n">';
            }

            if (mb_substr($selmenuitem['quicklinks'][$i]['href'], 0, 7) == "http://") {
                echo '<a href="' . $selmenuitem['quicklinks'][$i]['href'] . '" target="_blank">';
            }
            else {
                echo '<a href="' . $selmenuitem['quicklinks'][$i]['href'] . '">';
            }

            echo htmlspecialchars($selmenuitem['quicklinks'][$i]['text'], ENT_COMPAT, 'UTF-8') . '</a></td></tr>' . "\n";
        }

        echo '<tr><td class="navi2bottom">&nbsp;</td></tr>' . "\n";
        echo '</table>' . "\n";
        echo '</div>' . "\n";
    }
}
?>

