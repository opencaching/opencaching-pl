<?php 
## do zrobienia:
## - walidacja czy z posta są przekazywane wartości numeryczne (malo istotne, najwyzej zwroci ze wynik zly)
## - lista keszy w opensprawdzaczu - przewijanie do dalszych (na razie obsluguje 100 keszynek)
## layout jak mycache.php


//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false)
{
 // czy user zalogowany ?
 if ($usr == false)
 {
  // nie zalogowany wiec przekierowanie na strone z logowaniem
  $target = urlencode(tpl_get_current_page());
  tpl_redirect('login.php?target='.$target);
 }
else
{
 // wskazanie pliku z kodem html ktory jest w tpl/stdstyle/ 
 $tplname = 'opensprawdzacz';

  tpl_set_var("sekcja_5_start",'<!--');
  tpl_set_var("sekcja_5_stop",'-->');
 
// jeśli istnieje $_POST['stopnie_N'] znaczy że użytkownik wpisał współrzędne
// sekcja 3 - sprawdzająca poprawność wpisanych współrzędnych 

if (isset($_POST['stopnie_N']))
{
 tpl_set_var("sekcja_3_start",'');
 tpl_set_var("sekcja_3_stop",'');
 tpl_set_var("sekcja_2_start",'<!--');
 tpl_set_var("sekcja_2_stop",'-->');
 tpl_set_var("sekcja_1_start",'<!--');
 tpl_set_var("sekcja_1_stop",'-->'); 
  
 //check how many times user tried to guess answer 
 // sprawdzamy czy user nie używa brutal force
 
 
 $ile_prob = 15; // declaration how many times user can try his answer per hour/session
 tpl_set_var("ile_prob", $ile_prob);
 
  if (isset ($_SESSION['opensprawdzacz_licznik']))
  {
   if ($_SESSION['opensprawdzacz_licznik'] >= $ile_prob)
    {
	 $czas_ostatniej_proby = $_SESSION['opensprawdzacz_czas'];
	 $czas_teraz = date('U');
	 $czas_jaki_uplynal = $czas_teraz - $czas_ostatniej_proby;
	 tpl_set_var("czasss1", $czas_jaki_uplynal);
	 if ($czas_jaki_uplynal > 3600)
	  {
	   $_SESSION['opensprawdzacz_licznik'] = 1;
	   $_SESSION['opensprawdzacz_czas'] = $czas_teraz;
	  }
     else
	  {
	   // $_SESSION['opensprawdzacz_czas'] = date('U');
	   $czas_jaki_uplynal = round (60 - ($czas_jaki_uplynal / 60));
       tpl_set_var("licznik_zgadywan", $_SESSION["opensprawdzacz_licznik"]);
	   tpl_set_var("test1", tr(os_zgad));
	   tpl_set_var("wynik", '');
	   tpl_set_var("ikonka_yesno", '<image src="tpl/stdstyle/images/blue/opensprawdzacz_stop.png" />');
	   tpl_set_var("sekcja_4_start", '');
	   tpl_set_var("sekcja_4_stop", '');
	   tpl_set_var("twoje_ws", 'Masz maksymalnie '.$ile_prob.' prób / godzinę <br> Musisz odczekać jeszcze ' . $czas_jaki_uplynal .' minut'); 
	   goto endzik;
	  } 
	}
   else
    {
	 tpl_set_var("sekcja_4_start", '<!--');
	 tpl_set_var("sekcja_4_stop", '-->');
	 $czasss = $_SESSION['opensprawdzacz_czas'];
	 // tpl_set_var("czasss1", $czasss);
     $_SESSION['opensprawdzacz_licznik'] = $_SESSION['opensprawdzacz_licznik'] + 1;
	 $_SESSION['opensprawdzacz_czas'] = date('U');
	 $czasss = ($_SESSION['opensprawdzacz_czas'] - $czasss);
	 tpl_set_var("licznik_zgadywan", $_SESSION["opensprawdzacz_licznik"]);
	 tpl_set_var("czasss1", $czasss);
	 // tpl_set_var("czasss2", $_SESSION['opensprawdzacz_czas']);
	}
  }
  else
  {
   $_SESSION['opensprawdzacz_licznik'] = 1;
   tpl_set_var("licznik_zgadywan", $_SESSION["opensprawdzacz_licznik"]);
   tpl_set_var("sekcja_4_start",'<!--');
   tpl_set_var("sekcja_4_stop", '-->'); 
  }
  //koniec sekcji kontrolującej brutal force
  
  // get data from post.
  $stopnie_N = mysql_real_escape_string($_POST['stopnie_N']);
  $minuty_N  = mysql_real_escape_string($_POST['minuty_N']);
  $stopnie_E = mysql_real_escape_string($_POST['stopnie_E']);
  $minuty_E  = mysql_real_escape_string($_POST['minuty_E']);
  $cache_id  = mysql_real_escape_string($_POST['cacheid']);
  
  // converting from HH MM.MMM to DD.DDDDDD
  $wspolrzedneN = $stopnie_N + $minuty_N / 60;
  $wspolrzedneE = $stopnie_E + $minuty_E / 60;
  
  // geting data from database
  $result = sql("SELECT `wp_id`, 
                   `waypoints`.`type`, 
				   `waypoints`.`longitude`, 
				   `waypoints`.`latitude`,  
				   `waypoints`.`status`, 
				   `waypoints`.`type`,
				   `waypoints`.`opensprawdzacz`,
				   `opensprawdzacz`.`proby`,
				   `opensprawdzacz`.`sukcesy`
			  FROM `waypoints`, `opensprawdzacz` 
			  WHERE `waypoints`.`cache_id`='&1' 
			  AND `waypoints`.`opensprawdzacz` = 1
			  AND `waypoints`.`type` = 3 
			  AND `waypoints`.`cache_id`= `opensprawdzacz`.`cache_id`
			  ", 
			  $cache_id);
			  
  $dane = mysql_fetch_array($result);
  $licznik_prob = $dane['proby']+1;
  
  $wspolrzedneN_wzorcowe = $dane['latitude'];
  $wspolrzedneE_wzorcowe = $dane['longitude'];

  //comparing data from post with data from database	
  if (
	    (($wspolrzedneN_wzorcowe - $wspolrzedneN) < 0.00001) && 
		(($wspolrzedneN_wzorcowe - $wspolrzedneN) > -0.00001)
	    &&
		(($wspolrzedneE_wzorcowe - $wspolrzedneE) < 0.00001) && 
		(($wspolrzedneE_wzorcowe - $wspolrzedneE) > -0.00001)
	  )
	  {
	     //puzzle solved - resukt ok
		 $licznik_sukcesow = $dane['sukcesy']+1;
		 sql("UPDATE `opensprawdzacz` SET `proby`=$licznik_prob,`sukcesy`=$licznik_sukcesow  WHERE `cache_id` = $cache_id");
		 tpl_set_var("test1", tr('os_sukces'));
		 tpl_set_var("ikonka_yesno", '<image src="tpl/stdstyle/images/blue/opensprawdzacz_tak.png" />');
	  }
    else 
	  {
	     //puzzle not solved - restult wrong
		 sql("UPDATE `opensprawdzacz` SET `proby`='$licznik_prob'  WHERE `cache_id` = $cache_id");
		 tpl_set_var("test1", tr('os_fail'));
		 tpl_set_var("ikonka_yesno", '<image src="tpl/stdstyle/images/blue/opensprawdzacz_nie.png" />');
	  }
  //tpl_set_var("wynik", $wspolrzedneN.'/'.$wspolrzedneN_wzorcowe.'<br>'.$wspolrzedneE.'/'. $wspolrzedneE_wzorcowe);
  tpl_set_var("wynik",'');
  
  
  
 // tpl_set_var("wsp_NS", );
 // tpl_set_var("wsp_EW", );
  tpl_set_var("twoje_ws", tr('os_twojews') . '<b> N '. $stopnie_N.'°'.$minuty_N . '</b>/<b> E '. $stopnie_E.'°'.$minuty_E .'</b>');
  tpl_set_var("cache_id",  $cache_id); 
  
  
  goto endzik;
}  
 
 
 // get cache waypoint from url
 if (isset ($_GET['op_keszynki'])) 
   {
     $cache_wp = mysql_real_escape_string($_GET['op_keszynki']);
	 $cache_wp = strtoupper($cache_wp);
   } 
 else 
 {
  $formularz = '
  <form action="opensprawdzacz.php" method="get">
  '.tr(os_podaj_waypoint).': 
  <input type="text" name="op_keszynki" maxlength="6"/>
  <button type="submit" name="przeslanie_waypointa" value="'.tr(submit).'" style="font-size:14px;width:160px"><b>'.tr(submit).'</b></button>
  </form>
  ';
  
  $zapytajka = "SELECT `waypoints`.`cache_id`, 
                       `waypoints`.`type`, 
					   `waypoints`.`status`, 
					   `waypoints`.`stage`, 
					   `waypoints`.`desc`,
					   `caches`.`name`,
					   `caches`.`wp_oc`,
					   `caches`.`user_id`,
					   `caches`.`type`,
					   `caches`.`status`,
					   `user`.`username`,
					   `cache_type`.`sort`,
					   `cache_type`.`icon_small`,
					   `opensprawdzacz`.`proby`,
					   `opensprawdzacz`.`sukcesy`
			    FROM   `waypoints` 
		   LEFT JOIN   `opensprawdzacz` 
		          ON   `waypoints`.`cache_id` = `opensprawdzacz`.`cache_id`,
				       `caches`, `user`, `cache_type`
			   WHERE   `waypoints`.`opensprawdzacz` = 1
				 AND   `waypoints`.`type` = 3
				 AND   `caches`.`type` = `cache_type`.`id`
				 AND   `caches`.`user_id` = `user`.`user_id`
		         AND   `waypoints`.`cache_id` = `caches`.`cache_id`
			ORDER BY   `caches`.`name`
			   LIMIT   0, 100
				 ";
				 
$status = array (
'1' => '<img src="tpl/stdstyle/images/log/16x16-found.png" border="0" alt="Gotowa do szukania">',
'2' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="Tymczasowo niedostępna">',
'3' => '<img src="tpl/stdstyle/images/log/16x16-dnf.png" border="0" alt="zarchiwizowana">',
'4' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="Ukryta do czasu weryfikacji">',
'5' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="jeszcze niedostępna">',
'6' => '<img src="tpl/stdstyle/images/log/16x16-dnf.png" border="0" alt="Zablokowana przez COG">'
);

			 
  $keszynki_opensprawdzacza = sql($zapytajka);
  $ile_keszynek = mysql_num_rows($keszynki_opensprawdzacza);
  
  $tabelka_keszynek = '';
  for ($i=0; $i < $ile_keszynek; $i++)
   {
    $dane_keszynek = mysql_fetch_array($keszynki_opensprawdzacza);
    $tabelka_keszynek .= '<tr>
                           <td><a class="links" href="viewcache.php?wp='.$dane_keszynek['wp_oc'].'">'.$dane_keszynek['wp_oc'].'</a></td>
                           <td><a class="links" href="opensprawdzacz.php?op_keszynki='.$dane_keszynek['wp_oc'].'"> '. $dane_keszynek['name'] . '</a> </td>
                           <td><a href="viewcache.php?wp='.$dane_keszynek['wp_oc'].'"><img src="tpl/stdstyle/images/'.$dane_keszynek['icon_small'].'" /></a></td>
                           <td align="center">'.$status[$dane_keszynek['status']].'</td>
                           <td><a href="viewprofile.php?userid='.$dane_keszynek['user_id'].'">'.$dane_keszynek['username'] . '</td>
                           <td align="center">'.$dane_keszynek['proby'] . '</td>
                           <td align="center">'.$dane_keszynek['sukcesy'] . '</td>
						 </tr>';
   }
  
  tpl_set_var("sekcja_1_start",'');
  tpl_set_var("sekcja_1_stop", '');
  tpl_set_var("sekcja_2_start",'<!--');
  tpl_set_var("sekcja_2_stop", '-->');
  tpl_set_var("sekcja_3_start",'<!--');
  tpl_set_var("sekcja_3_stop", '-->'); 
  tpl_set_var("sekcja_4_start",'<!--');
  tpl_set_var("sekcja_4_stop", '-->');  
  tpl_set_var("sekcja_formularz_opensprawdzacza_start", '<!--');
  tpl_set_var("sekcja_formularz_opensprawdzacza_stop", '');
  tpl_set_var("formularz",$formularz);
  tpl_set_var("keszynki",$tabelka_keszynek);
  mysql_free_result($keszynki_opensprawdzacza);
  goto endzik;
 }


 // sekcja 2 (wyswietla dane kesza i formularz do wpisania współrzędnych)
 // pobieramy dane z bazy
 
 
 $rs = sql("SELECT `caches`.`name`,
                   `caches`.`cache_id`,
				   `caches`.`type`,
				   `cache_type`.`icon_large`,
                   `user`.`username`
			FROM   `caches`, `user`, `cache_type` 
            WHERE  `caches`.`user_id` = `user`.`user_id` 
			AND    `caches`.`type` = `cache_type`.`id`
			AND    `caches`.`wp_oc` = '&1'",$cache_wp);


 
 // przekaznie wynikow w postaci zmiennych do pliku z kodem html
 tpl_set_var("sekcja_1_start",'<!--');
 tpl_set_var("sekcja_1_stop",'-->');
 tpl_set_var("sekcja_2_start",'');
 tpl_set_var("sekcja_2_stop",'');
 tpl_set_var("sekcja_3_start",'<!--');
 tpl_set_var("sekcja_3_stop",'-->'); 
 tpl_set_var("sekcja_4_start",'<!--');
 tpl_set_var("sekcja_4_stop", '-->');  
 
 $czyjest = mysql_num_rows($rs);
 if ($czyjest == 0)
 {
  tpl_set_var("ni_ma_takiego_kesza", tr(ni_ma_takiego_kesza));
  tpl_set_var("sekcja_2_start",'<!--');
  tpl_set_var("sekcja_2_stop",'-->');
  tpl_set_var("sekcja_5_start",'');
  tpl_set_var("sekcja_5_stop",'');
  goto endzik;
 }
 
 $record = mysql_fetch_array($rs);
 $cache_id = $record['cache_id'];
 
 tpl_set_var("wp_oc",$cache_wp);
 tpl_set_var("ikonka_keszyny", '<img src="tpl/stdstyle/images/'.$record['icon_large'].'" />');
 tpl_set_var("cacheid",$record['cache_id']);
 tpl_set_var("ofner",$record['username']);
 tpl_set_var("cachename",$record['name']);  

 mysql_free_result($rs);
 

$wp_rs = sql("SELECT `waypoints`.`wp_id`, 
                     `waypoints`.`type`, 
					 `waypoints`.`longitude`, 
					 `waypoints`.`latitude`,  
					 `waypoints`.`status`, 
					 `waypoints`.`type`,
					 `waypoints`.`opensprawdzacz`
			  FROM `waypoints` 
			  WHERE `cache_id`='&1' AND `type` = 3 ",$cache_id);

	$wp_record = sql_fetch_array($wp_rs);
	if (($wp_record['status'] == 3) && ($wp_record['opensprawdzacz'] == 1))
	{
	 tpl_set_var("sekcja_formularz_opensprawdzacza_start", '');
     tpl_set_var("sekcja_formularz_opensprawdzacza_stop", '');
	 tpl_set_var("okienka",'');  
    }
	else
	{
	 tpl_set_var("okienka", tr(os_nie_ma_w_os));
     tpl_set_var("sekcja_formularz_opensprawdzacza_start", '<!--');
     tpl_set_var("sekcja_formularz_opensprawdzacza_stop", '-->');
    }
 }
mysql_free_result($wp_rs); 
}      

endzik:
// budujemy kod html ktory zostaje wsylany do przegladraki
tpl_BuildTemplate();
?>