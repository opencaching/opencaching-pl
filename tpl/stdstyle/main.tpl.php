<?php

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

	//MenĂź laden
	global $mnu_bgcolor, $mnu_selmenuitem, $develwarning, $tpl_subtitle;

	require_once $stylepath . '/lib/menu.php';
	$pageidx = mnu_MainMenuIndexFromPageId($menu, $tplname);

	if (isset($menu[$pageidx]['navicolor']))
	{
		$mnu_bgcolor = $menu[$pageidx]['navicolor'];
	}
	else
	{
		$mnu_bgcolor = '#D5D9FF';
	}

	if ($tplname != 'start')
		$tpl_subtitle .= htmlspecialchars($mnu_selmenuitem['title'] . ' - ', ENT_COMPAT, 'UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="{lang}" />
	<meta http-equiv="gallerimg" content="no" />
	<meta http-equiv="pragma" content="no-cache" />
  <meta name="KEYWORDS" content="geocaching, opencaching, skarby, poszukiwania,geocashing, longitude, latitude, utm, coordinates, treasure hunting, treasure, GPS, global positioning system, garmin, magellan, mapping, geo, hiking, outdoors, sport, hunt, stash, cache, geocaching, geocache, cache, treasure, hunting, satellite, navigation, tracking, bugs, travel bugs" />
	<meta http-equiv="cache-control" content="no-cache" />
  <meta name="author" content="Opencaching.pl " />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/style_screen.css" />
  <link rel="stylesheet" type="text/css" media="print" href="tpl/stdstyle/css/style_print.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/style_{season}.css" />
	<link rel="SHORTCUT ICON" href="favicon.ico" />
<script type="text/javascript" src="lib/enlargeit/enlargeit.js"></script>
  <title><?php echo $tpl_subtitle; ?>{title}</title>
	{htmlheaders}
	{cachemap_header}	
	{viewcache_header}
</head>
<body{bodyMod}>


<script language="javascript" type="text/javascript">
function chname( newName )
{
	document.getElementById("search_input").name = newName;
	return false;
}
</script>

<div id="overall">


  <div class="page-container-1" style="position: relative;">
<div id="bg1">
&nbsp;
</div>
<div id="bg2">
&nbsp;
</div>


  	<!-- HEADER -->
		<!-- OC-Logo -->
		<div><img src="./images/oc_logo.png" alt="" style="margin-top:5px; margin-left:3px;" /></div>
    <!-- Sitename -->
		<div class="site-name">
      <p class="title"><a href="index.php">OPENCACHING.pl</a></p>
      <p class="subtitle"><a href="index.php">Geocaching w Polsce</a></p>

    </div>
		
		<!-- Flag navigations -->
    <div class="navflag-container">
      <div class="navflag">
			  <ul>
          {language_flags}
        </ul>
		  </div>
    </div>			
    <!-- Site slogan -->
		<div class="site-slogan-container">
			<form method="get" action="search.php" name="search_form">
			<div class="site-slogan">
				<div style="width:100%; text-align:left;">
				<p class="search">
				  <input type="radio" onclick="chname('cachename');" name="searchto" id="st_1" value="searchbyname" class="radio" checked="checked"/> <label for="st_1">{{cache_label}}</label>&nbsp;&nbsp;
					<input type="radio" onclick="chname('owner');" name="searchto" id="st_2" value="searchbyowner" class="radio" /> <label for="st_2">{{owner_label}}</label>&nbsp;&nbsp;
					<input type="radio" onclick="chname('finder');" name="searchto" id="st_3" value="searchbyfinder" class="radio" /> <label for="st_3">{{finder_label}}</label>&nbsp;&nbsp;
					<input type="radio" onclick="chname('waypoint');" name="searchto" id="st_4" value="searchbywaypoint" class="radio"/> <label for="st_4">waypoint</label>&nbsp;&nbsp;
					<input type="hidden" name="showresult" value="1"/>
					<input type="hidden" name="expert" value="0"/>
					<input type="hidden" name="output" value="HTML"/>
					<input type="hidden" name="sort" value="bydistance"/>
					<input type="hidden" name="f_inactive" value="0"/>
					<input type="hidden" name="f_ignored" value="0"/>
					<input type="hidden" name="f_userfound" value="0"/>
					<input type="hidden" name="f_userowner" value="0"/>
					<input type="hidden" name="f_watched" value="0"/>
					<input type="hidden" name="f_geokret" value="0"/>
				</p>
				</div>
				<div style="float:right;  margin-top:3px;"><input id="search_input" type="text" name="cachename" class="input100;" style="color:gray;" />&nbsp;&nbsp;<input type="submit" name="submit" value="{{search}}" class="formbuttons" /></div>
      </div>
			</form>
		</div>	
    <!-- Navigation Level 1 -->
    <div class="nav1-container">
      <div class="nav1" style="text-align:right;margin-right:20px;">
			{loginbox}
		  </div>
    </div>
    <!-- Header banner	 -->	 						    		 						
		<div class="header">
<!--		<div style="width:970px; padding-top:1px;"><img src="./images/head/rotator.php" alt="" style="border:0px;" /></div> --> 
		</div>		
   	
    <!-- Navigation Level 2 -->												
	  <div class="nav2">			
			<ul>
				<?php 
				$dowydrukuidx = mnu_MainMenuIndexFromPageId($menu, "dowydruku");			
				if( count($_SESSION['print_list']) > 0 )
				{
					$menu[$dowydrukuidx]['visible'] = true;
					$menu[$dowydrukuidx]['menustring'] .= " (".count($_SESSION['print_list']).")"; 
				}
				//user is admin
				if( $usr['admin'] )
				{
					$sql = "SELECT count(status) FROM reports WHERE status = 0";
					$new_reports = mysql_result(mysql_query($sql),0);
					$sql = "SELECT count(status) FROM reports WHERE status = 3";
					$lookhere_reports = mysql_result(mysql_query($sql),0);
					$sql = "SELECT count(status) FROM reports WHERE status <> 2";
					$active_reports = mysql_result(mysql_query($sql),0);
					$sql = "SELECT value FROM sysconfig WHERE name = 'hidden_for_approval'";
					$new_pendings = mysql_result(mysql_query($sql),0);
				}

				mnu_EchoMainMenu($menu[$pageidx]['siteid']);
				?>
      </ul>
		</div>
    <!-- Buffer after header -->    
		<div class="buffer" style="height:30px;"></div>
		<!-- NAVIGATION -->				
  	<!-- Navigation Level 3 -->
		<div class="nav3">
			<?php
				//Main menu
				$mainmenuidx = mnu_MainMenuIndexFromPageId($menu, "start");
				if (isset($menu[$mainmenuidx]['submenu']))
				{
					$registeridx = mnu_MainMenuIndexFromPageId($menu[$mainmenuidx]["submenu"], "register");
					if( $usr )
					{
						$menu[$mainmenuidx]['submenu'][$registeridx]['visible'] = false;
					}
					else
						$menu[$mainmenuidx]['submenu'][$registeridx]['visible'] = true;
					echo '<ul>';
					echo '<li class="title">'.tr('main_menu').'</li>';
					mnu_EchoSubMenu($menu[$mainmenuidx]['submenu'], $tplname, 1, false);
					echo '</ul>';
				}
			?>
			<?php
				if( $usr && isset($_SESSION['user_id']))
				{				
					$myhomeidx = mnu_MainMenuIndexFromPageId($menu, "myhome");
					$myprofileidx = mnu_MainMenuIndexFromPageId($menu[$myhomeidx]["submenu"], "myprofile");
					
					// [fixme] Have to do the menu unrolling... in not such a crappy way
					if( $tplname == "myprofile" || $tplname == "myprofile_change" || $tplname == "newemail" || $tplname == "newpw" || $tplname == "change_statpic")
					{
						for( $i = 0; $i < count($menu[$myhomeidx]["submenu"][$myprofileidx]['submenu']); $i++ )
						{
						
							$menu[$myhomeidx]["submenu"][$myprofileidx]['submenu'][$i]['visible'] = true;
						}
					}

					echo '<ul>';
					echo '<li class="title">'.$menu[$myhomeidx]["title"].'</li>';
					mnu_EchoSubMenu($menu[$myhomeidx]['submenu'], $tplname, 1, false);
					echo '</ul>';
				}
			?>
			<?php 
				if( $usr['admin'] )
				{
					echo '<ul>';
					$adminidx = mnu_MainMenuIndexFromPageId($menu, "viewreports");
					$menu[$adminidx]['visible'] = false;
					echo '<li class="title">'.$menu[$adminidx]["title"].'</li>';
					$zgloszeniaidx = mnu_MainMenuIndexFromPageId($menu[$adminidx]["submenu"], "viewreports");
					if( $active_reports > 0)
						$menu[$adminidx]["submenu"][$zgloszeniaidx]['menustring'] .= " (".$new_reports."/".$active_reports.")"; 
					$zgloszeniaidx = mnu_MainMenuIndexFromPageId($menu[$adminidx]["submenu"], "viewpendings");
					if( $new_pendings > 0)
						$menu[$adminidx]["submenu"][$zgloszeniaidx]['menustring'] .= " (".$new_pendings.")"; 
					mnu_EchoSubMenu($menu[$adminidx]['submenu'], $tplname, 1, false);
					echo '</ul>';
				}
			?>
      <!-- Main title -->
    </div>				
  	<!-- 	CONTENT -->
		<div class="content2">
			{template}
		</div>

		<!-- FOOTER -->
			<div class="footer">
			<span class="txt-black">&nbsp;&nbsp;<b>{{online_users}} (
                       </span> <span class="txt-yellow10">
                      <?php $onlusers=online_user();
                            $nuser=count($onlusers);
                        echo $nuser;
                            ?>
                        </span><span class="txt-black">) - {{online_users_info}}:&nbsp;</b></span>  
                         <span class="txt-yellow10">
                        <?php    foreach($onlusers as $onluser){
                        $userid=sqlValue("SELECT user_id FROM `user` WHERE username='$onluser'", 0);
                        echo "<a class=\"links_onlusers\" href=\"viewprofile.php?userid=".$userid."\">".$onluser."</a>,&nbsp;";
                                }

                            ?>
                           </span><p>&nbsp;</p>

				 <p>
					<a href="articles.php?page=impressum">{{impressum}}</a> | 
					<a href="articles.php?page=contact">{{contact}}</a> |
					<a href="/index.php?page=sitemap">{{main_page}}</a><br/>
					{runtime}
				</p>
				 <p><a href="http://validator.w3.org/check?uri=referer" title="Validate code as W3C XHTML 1.0 Compliant">W3C XHTML 1.0</a> | <a href="http://jigsaw.w3.org/css-validator/" title="Validate Style Sheet as W3C CSS 2.0 Compliant">W3C CSS 2.0</a></p>
			</div>
		</div>
</div>
</body>
</html>
