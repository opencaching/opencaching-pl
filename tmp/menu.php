<?php
/***************************************************************************
											./stdstyle/lib/menu.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
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
include('lib/language.inc.php');
global $menu, $usr, $lang;

$menu = array(
	array(
		'title' => '',
		'menustring' => $language[$lang]['main_page'],
		'siteid' => 'start',
		'navicolor' => '#E8DDE4',
		'visible' => true,
		'filename' => 'index.php',
		'quicklinks' => array(
			array('href' => 'http://forum.opencaching.pl', 'text' => 'Forum Geocaching'),
			array('href' => 'http://www.geocache.pl', 'text' => 'Polska strona Geocaching'),
			array('href' => 'http://www.geolutins.com/langue_valid.php?L=7', 'text' => 'GeoLutin - Darmowe TB')
		),
		'submenu' => array(
			array(
				'title' => $language[$lang]['what_is_geocaching'],
				'menustring' => $language[$lang]['what_is_geocaching'],
				'visible' => true,
        'filename' => 'http://wiki.opencaching.pl/index.php/Co_to_jest_geocaching_%3F',
        'newwindow' => true,
				'siteid' => 'articles/aboutgc'
			),
			array(
				'title' => $language[$lang]['registration'],
				'menustring' => $language[$lang]['registration'],
				'visible' => true,
				'filename' => 'register.php',
				'siteid' => 'register',
				'submenu' => array(
					array(
						'title' => $language[$lang]['account_activation'],
						'menustring' => $language[$lang]['account_activation'],
						'visible' => false,
						'filename' => 'activation.php',
						'siteid' => 'activation'
					),
					array(
						'title' => $language[$lang]['account_activation'],
						'menustring' => $language[$lang]['account_activation'],
						'visible' => false,
						'filename' => 'activation.php',
						'siteid' => 'activation_confirm'
					)
				)
			),
			array(
				'title' => $language[$lang]['news'],
				'menustring' => $language[$lang]['news'],
				'visible' => true,
				'filename' => 'news.php',
				'siteid' => 'news'
			),
			array(
				'title' => $language[$lang]['new_caches'],
				'menustring' => $language[$lang]['new_caches'],
				'visible' => true,
				'filename' => 'newcaches.php',
				'siteid' => 'newcaches',
				'submenu' => array(
					array(
						'title' => $language[$lang]['abroad_poland'],
						'menustring' => $language[$lang]['abroad_poland'],
						'visible' => true,
						'filename' => 'newcachesrest.php',
						'siteid' => 'newcachesrest'
					),
						array(
						'title' => 'RSS feed',
						'menustring' => 'RSS feed',
						'siteid' => 'RSS feed',
						'visible' => true,
						'filename' => 'http://www.opencaching.pl/rss/newcaches.xml'
					)
				)
			),
		array(
				'title' => $language[$lang]['recommended_caches'],
				'menustring' => $language[$lang]['recommended_caches'],
				'visible' => true,
				'filename' => 'newcacherating.php',
				'siteid' => 'ratings'
			),
				array(
				'title' => $language[$lang]['new_logs'],
				'menustring' => $language[$lang]['new_logs'],
				'visible' => true,
				'filename' => 'newlogs.php',
				'siteid' => 'newlogs'
			),
			array(
				'title' => $language[$lang]['statistics'],
				'menustring' => $language[$lang]['statistics'],
				'visible' => true,
				'filename' => 'articles.php?page=stat',
				'siteid' => 'articles/stat'
			),
			array(
				'title' => $language[$lang]['rules'],
				'visible' => true,
				'filename' => $language[$lang]['filename_rules'],
				'menustring' => $language[$lang]['rules'],
				'siteid' => 'articles/regulamin'
			),
	array(
		'title' => $language[$lang]['links'],
		'menustring' => $language[$lang]['links'],
		'siteid' => 'articles/links',
		'navicolor' => '#FFFFC5',
		'visible' => false,
		'filename' => 'articles.php?page=links'
	),
	array(
		'title' => $language[$lang]['geochat'],
		'menustring' => $language[$lang]['geochat'],
		'siteid' => 'chat',
		'visible' => true,
		'filename' => 'chat.php'
	),
	array(
		'title' => 'Kanał IRC',
		'menustring' => 'Kanał IRC',
		'siteid' => 'irc',
		'visible' => true,
		'filename' => 'irc://chat.eu.freenode.net/opencaching.pl',
		'newwindow' => true
	),
	array(
		'title' => $language[$lang]['statistics'],
		'menustring' => $language[$lang]['statistics'],
		'siteid' => 'articles/s1',
		'visible' => false,
		'filename' => 'articles.php?page=s1'
	),
	array(
		'title' => $language[$lang]['statistics'],
		'menustring' => $language[$lang]['statistics'],
		'siteid' => 'articles/s2',
		'visible' => false,
		'filename' => 'articles.php?page=s2'
	),
	array(
		'title' => $language[$lang]['statistics'],
		'menustring' => $language[$lang]['statistics'],
		'siteid' => 'articles/s3',
		'visible' => false,
		'filename' => 'articles.php?page=s3'
	),
	array(
		'title' => $language[$lang]['statistics'],
		'menustring' => $language[$lang]['statistics'],
		'siteid' => 'articles/s4',
		'visible' => false,
		'filename' => 'articles.php?page=s4'
	),
	array(
		'title' => $language[$lang]['statistics'],
		'menustring' => $language[$lang]['statistics'],
		'siteid' => 'articles/s5',
		'visible' => false,
		'filename' => 'articles.php?page=s5'
	),
	array(
		'title' => $language[$lang]['contact'],
		'visible' => true,
		'filename' => 'articles.php?page=contact',
		'menustring' => $language[$lang]['contact'],
		'siteid' => 'articles/contact'
			)			
		)
	),

	array(
		'title' => $language[$lang]['my_account'],
		'menustring' => $language[$lang]['my_account'],
		'siteid' => 'myhome',
		'visible' => true,
		'filename' => 'myhome.php',
		'navicolor' => '#D5D9FF',
//		'navicolor' => '#E8DDE4',
		'submenu' => array(
			array(
				'title' => $language[$lang]['general'],
				'menustring' => $language[$lang]['general'],
				'visible' => true,
				'filename' => 'myhome.php',
				'siteid' => 'myhome'
			),
			array(
				'title' => $language[$lang]['my_logs'],
				'menustring' => $language[$lang]['my_logs'],
				'visible' => false,
				'filename' => 'myhome2.php',
				'siteid' => 'myhome2'
			),
			array(
				'title' => $language[$lang]['my_account'],
				'menustring' => $language[$lang]['my_account'],
				'visible' => true,
				'filename' => 'myprofile.php',
				'siteid' => 'myprofile',
				'submenu' => array(
					array(
						'title' => $language[$lang]['change_data'],
						'menustring' => $language[$lang]['change_data'],
						'visible' => true,
						'filename' => 'myprofile.php?action=change',
						'siteid' => 'myprofile_change'
					),
					array(
						'title' => $language[$lang]['change_email'],
						'menustring' => $language[$lang]['change_email'],
						'visible' => true,
						'filename' => 'newemail.php',
						'siteid' => 'newemail'
					),
					array(
						'title' => $language[$lang]['change_password'],
						'menustring' => $language[$lang]['change_password'],
						'visible' => true,
						'filename' => 'newpw.php',
						'siteid' => 'newpw'
					),
					array(
						'title' => $language[$lang]['choose_statpic'],
						'menustring' => $language[$lang]['choose_statpic'],
						'visible' => true,
						'filename' => 'change_statpic.php',
						'siteid' => 'change_statpic'),
				)
			),
			array(
				'title' => $language[$lang]['collected_queries'],
				'menustring' => $language[$lang]['collected_queries'],
				'visible' => true,
				'filename' => 'query.php',
				'siteid' => 'viewqueries'
			),
			array(
				'title' => $language[$lang]['watched_caches'],
				'menustring' => $language[$lang]['watched_caches'],
				'visible' => true,
				'filename' => 'mywatches.php',
				'siteid' => 'mywatches',
				'submenu' => array(
					array(
						'title' => $language[$lang]['settings'],
						'menustring' => $language[$lang]['settings'],
						'visible' => true,
						'filename' => 'mywatches.php?rq=properties',
						'siteid' => 'mywatches_properties'
					)
				)
			),

			array(
				'title' => $language[$lang]['ignored_caches'],
				'menustring' => $language[$lang]['ignored_caches'],
				'visible' => true,
				'filename' => 'myignores.php',
				'siteid' => 'myignores'
			),
			array(
				'title' => $language[$lang]['my_recommendations'],
				'menustring' => $language[$lang]['my_recommendations'],
				'visible' => true,
				'filename' => 'mytop5.php',
				'siteid' => 'mytop5'
			)
		)
	),
	array(
		'title' => $language[$lang]['search_cache'],
		'menustring' => $language[$lang]['caches'],
		'siteid' => 'search',
		'visible' => true,
		'filename' => 'search.php',
		'navicolor' => '#BDE3E7',
		'submenu' => array(
			array(
				'title' => $language[$lang]['search'],
				'menustring' => $language[$lang]['search'],
				'visible' => true,
				'filename' => 'search.php',
				'siteid' => 'search',
				'submenu' => array(
					array(
						'title' => $language[$lang]['view_cache'],
						'menustring' => $language[$lang]['view_cache'],
						'visible' => false,
						'filename' => 'viewcache.php',
						'siteid' => 'viewcache',
						'submenu' => array(
							array(
								'title' => $language[$lang]['new_log_entry'],
								'menustring' => $language[$lang]['new_log_entry'],
								'visible' => false,
								'filename' => 'log.php',
								'siteid' => 'log_cache'
							),
							array(
								'title' => $language[$lang]['edit_log'],
								'menustring' => $language[$lang]['edit_log'],
								'visible' => false,
								'filename' => 'editlog.php',
								'siteid' => 'editlog'
							),
							array(
								'title' => $language[$lang]['remove_log'],
								'menustring' => $language[$lang]['remove_log'],
								'visible' => false,
								'filename' => 'removelog.php',
								'siteid' => 'removelog_logowner'
							),
							array(
								'title' => $language[$lang]['remove_log'],
								'menustring' => $language[$lang]['remove_log'],
								'visible' => false,
								'filename' => 'removelog.php',
								'siteid' => 'removelog_cacheowner'
							),
							array(
								'title' => $language[$lang]['edit_cache'],
								'menustring' => $language[$lang]['edit_cache'],
								'visible' => false,
								'filename' => 'editcache.php',
								'siteid' => 'editcache'
							),
							array(
								'title' => $language[$lang]['new_desc'],
								'menustring' => $language[$lang]['new_desc'],
								'visible' => false,
								'filename' => 'newdesc.php',
								'siteid' => 'newdesc'
							),
							array(
								'title' => $language[$lang]['edit_desc'],
								'menustring' => $language[$lang]['edit_desc'],
								'visible' => false,
								'filename' => 'editdesc.php',
								'siteid' => 'editdesc'
							),
							array(
								'title' => $language[$lang]['remove_desc'],
								'menustring' => $language[$lang]['remove_desc'],
								'visible' => false,
								'filename' => 'removedesc.php',
								'siteid' => 'removedesc'
							)
						)
					),
					array(
						'title' => $language[$lang]['search_loc'],
						'menustring' => $language[$lang]['search_loc'],
						'visible' => false,
						'filename' => 'search.php',
						'siteid' => 'selectlocid',
					),
					array(
						'title' => $language[$lang]['search_results'],
						'menustring' => $language[$lang]['search'],
						'visible' => false,
						'filename' => 'search.php',
						'siteid' => 'search.result.caches',
					),
					array(
						'title' => $language[$lang]['show_log'],
						'menustring' => $language[$lang]['show_log'],
						'visible' => false,
						'filename' => 'viewlogs.php',
						'siteid' => 'viewlogs',
					),
					array(
						'title' => $language[$lang]['store_queries'],
						'menustring' => $language[$lang]['store_queries'],
						'visible' => false,
						'filename' => 'query.php?action=save',
						'siteid' => 'savequery'
					),
					array(
						'title' => $language[$lang]['cache_recommendation'],
						'menustring' => $language[$lang]['cache_recommendation'],
						'visible' => false,
						'filename' => 'recommendations.php',
						'siteid' => 'recommendations'
					)
				)
			),
			array(
				'title' => $language[$lang]['new_cache'],
				'menustring' => $language[$lang]['new_cache'],
				'visible' => true,
				'filename' => 'newcache.php',
				'siteid' => 'newcache',
				'submenu' => array(
					array(
						'title' => $language[$lang]['cache_descriptions'],
						'menustring' => $language[$lang]['cache_descriptions'],
						'visible' => true,
						'filename' => $language[$lang]['filename_describe_cache'],
						'siteid' => 'articles/cacheinfo'
					),
					array(
						'title' => $language[$lang]['html_preview'],
						'menustring' => $language[$lang]['html_preview'],
						'visible' => false,
						'filename' => 'htmlprev.php',
						'siteid' => 'htmlprev',
						'submenu' => array(
							array(
								'title' => $language[$lang]['html_preview'],
								'menustring' => $language[$lang]['html_preview'],
								'visible' => false,
								'filename' => 'htmlprev.php',
								'siteid' => 'htmlprev_step2'
							),
							array(
								'title' => $language[$lang]['html_preview'],
								'menustring' => $language[$lang]['html_preview'],
								'visible' => false,
								'filename' => 'htmlprev.php',
								'siteid' => 'htmlprev_step3'
							),
							array(
								'title' => $language[$lang]['html_preview'],
								'menustring' => $language[$lang]['html_preview'],
								'visible' => false,
								'filename' => 'htmlprev.php',
								'siteid' => 'htmlprev_step3err'
							)
						)
					),
					array(
						'title' => $language[$lang]['allowed_html_tags'],
						'menustring' => $language[$lang]['allowed_html_tags'],
						'visible' => true,
						'filename' => 'articles.php?page=htmltags',
						'siteid' => 'articles/htmltags'
					)
				)
			),
			array(
				'title' => $language[$lang]['special_caches'],
				'menustring' => $language[$lang]['special_caches'],
				'visible' => false,
				'filename' => 'articles.php?page=specialcaches',
				'siteid' => 'articles/specialcaches'
			),
			array(
				'title' => $language[$lang]['user_ident'],
				'menustring' => $language[$lang]['user_ident'],
				'filename' => 'viewprofile.php',
				'siteid' => 'viewprofile',
				'visible' => false
			),
			array(
				'title' => $language[$lang]['recommendations'],
				'menustring' => $language[$lang]['recommendations'],
				'filename' => 'usertops.php',
				'siteid' => 'usertops',
				'visible' => false
			)
		)
	),
	array(
		'title' => $language[$lang]['cache_map'],
		'menustring' => $language[$lang]['cache_map'],
		'siteid' => 'cachemap2',
		'visible' => true,
		'filename' => 'cachemap2.php',
		'navicolor' => '#FFFFC5',
//		'navicolor' => '#E8DDE4',
		'submenu' => array(
/* Nowa mapa tymczasowo zablokowana
			array(
			'title' => $language[$lang]['cache_map'],
			'menustring' => $language[$lang]['cache_map'],
			'siteid' => 'cachemap3',
			'visible' => true,
			'filename' => 'cachemap3.php'
		),
*/
			array(
			'title' => $language[$lang]['cache_map'],
			'menustring' => $language[$lang]['old_cache_map'],
			'siteid' => 'cachemap2',
			'visible' => true,
			'filename' => 'cachemap2.php'
			),
	    )
	),
	array(
		'title' => $language[$lang]['abc'],
		'menustring' => $language[$lang]['abc'],
		'siteid' => 'articles/info',
		'visible' => true,
		'filename' => 'http://wiki.opencaching.pl',
		'newwindow' => true
	),
	array(
		'title' => $language[$lang]['forum'],
		'menustring' => $language[$lang]['forum'],
		'siteid' => 'forum',
		'visible' => true,
		'filename' => 'http://forum.opencaching.pl',
		'newwindow' => true
	),
	array(
		'title' => $language[$lang]['geokrets'],
		'menustring' => $language[$lang]['geokrets'],
		'siteid' => 'GeoKrety',
		'visible' => true,
		'filename' => 'http://geokrety.org/index.php?lang=pl_PL.UTF-8',
		'newwindow' => 'true'
	),
	array(
		'title' => $language[$lang]['geoblog'],
		'menustring' => $language[$lang]['geoblog'],
		'siteid' => 'GeoBlog',
		'visible' => true,
		'filename' => 'http://www.geoblog.com.pl',
		'newwindow' => 'true'
	),
		array(
		'title' => $language[$lang]['links'],
		'menustring' => $language[$lang]['links'],
		'navicolor' => '#DDDDDD',
		'siteid' => 'articles/links',
		'visible' => true,
		'filename' => 'articles.php?page=links'
	),
	array(
		'title' => $language[$lang]['clipboard'],
		'menustring' => $language[$lang]['clipboard'],
		'siteid' => 'dowydruku',
		'visible' => false,
		'filename' => 'mylist.php'
	),
	array(
		'title' => 'Zarządzanie OC PL',
		'menustring' => 'Zarządzanie OC PL',
		'siteid' => 'viewreports',
		'visible' => false,
		'filename' => 'viewreports.php',
		'submenu' => array(
			array(
			'title' => $language[$lang]['reports'],
			'menustring' => $language[$lang]['reports'],
			'siteid' => 'viewreports',
			'visible' => true,
			'filename' => 'viewreports.php'
			),
			array(
			'title' => 'Skrzynki nieznalezione',
			'menustring' => 'Skrzynki nieznalezione',
			'siteid' => 'admin_cachenotfound',
			'visible' => true,
			'filename' => 'admin_cachenotfound.php'
			),
			array(
			'title' => 'Szukaj użytkownika',
			'menustring' => 'Szukaj użytkownika',
			'siteid' => 'admin_searchuser',
			'visible' => true,
			'filename' => 'admin_searchuser.php'
			),
			array(
			'title' => $language[$lang]['bugs'],
			'menustring' => $language[$lang]['bugs'],
			'siteid' => 'bledy',
			'visible' => true,
			'filename' => 'http://bugs.opencaching.pl',
      'newwindow' => true			
			),
			array(
			'title' => 'Dodaj newsa',
			'menustring' => 'Dodaj newsa',
			'siteid' => 'admin_addnews',
			'visible' => true,
			'filename' => 'admin_addnews.php'
			),
			array(
			'title' => 'Wyślij biuletyn',
			'menustring' => 'Wyślij biuletyn',
			'siteid' => 'admin_bulletin',
			'visible' => true,
			'filename' => 'admin_bulletin.php'
			),
	),

	),
	array(
		'title' => $language[$lang]['login'].'/'.$language[$lang]['logout'],
		'visible' => false,
		'filename' => 'login.php',
		'menustring' => $language[$lang]['login'].'/'.$language[$lang]['logout'],
		'navicolor' => '#E8DDE4',
		'siteid' => 'login'
	),
	array(
		'title' => $language[$lang]['add_picture'],
		'visible' => false,
		'filename' => 'newpic.php',
		'menustring' => $language[$lang]['add_picture'],
		'siteid' => 'newpic'
	),
	array(
		'title' => $language[$lang]['edit_picture'],
		'visible' => false,
		'filename' => 'editpic.php',
		'menustring' => $language[$lang]['edit_picture'],
		'siteid' => 'editpic'
	),
	array(
		'title' => $language[$lang]['new_password'],
		'visible' => false,
		'filename' => 'newpw.php',
		'menustring' => $language[$lang]['new_password'],
		'siteid' => 'newpw'
	),
	array(
		'title' => $language[$lang]['new_topic'],
		'visible' => false,
		'filename' => 'newstopic.php',
		'menustring' => $language[$lang]['new_topic'],
		'siteid' => 'newstopic',
		'showsitemap' => false
	),
	array(
		'title' => $language[$lang]['page_error'],
		'menustring' => $language[$lang]['page_error'],
		'visible' => false,
		'filename' => 'index.php',
		'siteid' => 'error'
	),
	array(
		'title' => $language[$lang]['contact'],
		'visible' => false,
		'filename' => 'articles.php?page=contact',
		'menustring' => $language[$lang]['contact'],
		'siteid' => 'articles/contact'
	),
	array(
		'title' => $language[$lang]['personal_data'],
		'visible' => false,
		'filename' => 'articles.php?page=dsb',
		'menustring' => $language[$lang]['personal_data'],
		'siteid' => 'articles/dsb'
	),
	array(
		'title' => $language[$lang]['message'],
		'visible' => false,
		'filename' => 'index.php',
		'menustring' => $language[$lang]['message'],
		'siteid' => 'message'
	),
	array(
		'title' => $language[$lang]['register_confirm'],
		'visible' => false,
		'filename' => 'register.php',
		'menustring' => $language[$lang]['register_confirm'],
		'siteid' => 'register_confirm'
	),
	array(
		'title' => $language[$lang]['cache_map'],
		'visible' => false,
		'filename' => 'cachemap.php',
		'menustring' => $language[$lang]['cache_map'],
		'siteid' => 'mapa1'
	),
	array(
		'title' => $language[$lang]['main_page'],
		'visible' => false,
		'filename' => 'index.php',
		'menustring' => $language[$lang]['main_page'],
		'siteid' => 'sitemap'
	)
);

/*
 * mnu_MainMenuIndexFromPageId - returns the top level menu
 *
 * menustructure   normally $menu
 * pageid          siteid to search for
 */
function mnu_MainMenuIndexFromPageId($menustructure, $pageid)
{
	/* selmenuitem contains the selected (bold) menu item */
	global $mnu_selmenuitem;

	for ($i = 0, $ret = -1; ($i < count($menustructure)) && ($ret == -1); $i++)
	{
		if ($menustructure[$i]['siteid'] == $pageid)
		{
			$mnu_selmenuitem = $menustructure[$i];
			return $i;
		}
		else
		{
			if (isset($menustructure[$i]['submenu']))
			{
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
function mnu_EchoMainMenu($selmenuid)
{
	global $menu;
	$c = 0;
	for ($i = 0; $i < count($menu); $i++)
	{
		if ($menu[$i]['visible'] == true)
		{
			if ($c > 0) echo '&nbsp;|&nbsp;';

			if( $menu[$i]['newwindow'] == true ) 
				$target_blank = "target='_blank'";
			else
				$target_blank = "";
				
			if ($menu[$i]['siteid'] == $selmenuid)
			{
				echo '<b><a href="' . $menu[$i]['filename'] . '">' . htmlspecialchars($menu[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></b>' . "\n";
			}
			else
			{
				echo '<a '.$target_blank.' href="' . $menu[$i]['filename'] . '">' . htmlspecialchars($menu[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a>' . "\n";
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
function mnu_EchoSubMenu($menustructure, $pageid, $level, $bHasSubmenu)
{
	/* enthĂ¤lt die Hintergrundfarbe des MenĂźs */
	global $mnu_bgcolor;

	if (!$bHasSubmenu)
	{
		/* prĂźfen, ob ein SubmenĂź vorhanden ist */
		for ($i = 0, $bSubmenu = false; ($i < count($menustructure)) && ($bSubmenu == false); $i++)
		{
			if (isset($menustructure[$i]['submenu']))
			{
				$bSubmenu = true;
			}
		}
	}

	if (!$bHasSubmenu)
	{
		$cssclass = 'navilevel2n';
	}
	else
	{
		if ($level == 1)
		{
			$cssclass = 'navilevel2';
		}
		else
		{
			$cssclass = 'navilevel3';
		}
	}

	for ($i = 0; $i < count($menustructure); $i++)
	{
		if ($menustructure[$i]['visible'] == true)
		{
			if ($menustructure[$i]['siteid'] == $pageid)
			{
				echo '<tr><td class="' . $cssclass . '"><b><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></b></td></tr>' . "\n";
			}
			else
			{
				if (isset($menustructure[$i]['submenu']))
				{
					if (mnu_IsMenuParentOf($menustructure[$i]['submenu'], $pageid))
					{
						echo '<tr><td class="' . $cssclass . '"><b><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></b></td></tr>' . "\n";
					}
					else
					{
						echo '<tr><td class="' . $cssclass . '"><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></td></tr>' . "\n";
					}
				}
				else
				{
					echo '<tr><td class="' . $cssclass . '"><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8') . '</a></td></tr>' . "\n";
				}
			}

			if (isset($menustructure[$i]['submenu']))
			{
				/* rekursiver Aufruf zur Ausgabe der 3. Ebene */
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
function mnu_IsMenuParentOf($parentmenuitems, $menuitemid)
{
	for ($i = 0; $i < count($parentmenuitems); $i++)
	{
		if ($parentmenuitems[$i]['siteid'] == $menuitemid) return true;

		if (isset($parentmenuitems[$i]['submenu']))
		{
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
function mnu_EchoBreadCrumb($pageid, $mainmenuindex)
{
	global $menu;

	echo htmlspecialchars($menu[$mainmenuindex]['menustring'], ENT_COMPAT, 'UTF-8');

	if (isset($menu[$mainmenuindex]['submenu']) && ($menu[$mainmenuindex]['siteid'] != $pageid))
	{
		mnu_prv_EchoBreadCrumbSubItem($pageid, $menu[$mainmenuindex]['submenu']);
	}
}

/*
 * mnu_prv_EchoBreadCrumbSubItem - private helper function
 */
function mnu_prv_EchoBreadCrumbSubItem($pageid, $menustructure)
{
	for ($i = 0; $i < count($menustructure); $i++)
	{
		if ($menustructure[$i]['siteid'] == $pageid)
		{
			echo '&nbsp;&gt;&nbsp;' . htmlspecialchars($menustructure[$i]['menustring'], ENT_COMPAT, 'UTF-8');
			return;
		}
		else
		{
			if (isset($menustructure[$i]['submenu']))
			{
				if (mnu_IsMenuParentOf($menustructure[$i]['submenu'], $pageid))
				{
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
function mnu_EchoQuicklinks($selmenuitem)
{
	if (isset($selmenuitem['quicklinks']))
	{
		echo '<div style="float:right;font-family:sans-serif;font-size:small;">' . "\n";
		echo '<br/>' . "\n";
		echo '<table cellpadding="0" cellspacing="0" id="quicklinksbox">' . "\n";
		echo '<tr><td id="quicklinkstop">Strony zwiazane z OC.pl:</td></tr>' . "\n";

		for ($i = 0; $i < count($selmenuitem['quicklinks']); $i++)
		{
			if ($i == 0)
			{
				echo '<tr><td class="navilevel2n" style="padding-top:1ex;">';
			}
			else
			{
				echo '<tr><td class="navilevel2n">';
			}

			if (mb_substr($selmenuitem['quicklinks'][$i]['href'], 0, 7) == "http://")
			{
				echo '<a href="' . $selmenuitem['quicklinks'][$i]['href'] . '" target="_blank">';
			}
			else
			{
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
