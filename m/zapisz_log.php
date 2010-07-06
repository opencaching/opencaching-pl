<?
require("common.inc.php");

if (isset($_COOKIE['ocplm'])) 
{
    foreach ($_COOKIE['ocplm'] as $name => $value)
      {
       $login[$name]=base64_decode($value);
       }

 $logino = $login['USER'];
$loginh = $login['HASLO'];
setcookie("ocplm[USER]", base64_encode($logino), time()+36000);

setcookie("ocplm[HASLO]", base64_encode($loginh), time()+36000);

  mysql_connect("localhost","ocpl","n1uch@cz");
  mysql_select_db("ocpl");

  $result1 = mysql_query("SELECT * FROM `user` WHERE `username` = '$logino' LIMIT 1; ");

  $result = mysql_fetch_array($result1);
   $record_user = $result;
   
   echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>LOG</title></head><body>');

  if (($result['password'] == hash('sha512', md5($login['HASLO']))) && ($result['username'] == $login['USER']) && ($result['is_active_flag'] == '1') && ($result['rules_confirmed'] == '1'))
    { 
     //zalogowany





if (!empty($_REQUEST['cacheid'])  ) 
       {
        // aaa nie jest puste wyszukiwanie, wiec sprawdzamy
            
            echo('Zalogowany, <b>'. $logino . '</b><br>');
            echo('<font size="+2">Dodawanie logu</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr />');
   
            
                  $cache_id= $_REQUEST['cacheid'];
         
                $rs=  mysql_query("SELECT * FROM `caches` WHERE `cache_id` = '$cache_id' AND `status`=1; ");
                
				$count = mysql_num_rows($rs);
                      
                     if ($count == 1)
						{
							$record_cache = mysql_fetch_array($rs);
							
                                      
                                                           
                                                        $nazwa= $record_cache['name'];

                                                         
														 
														 
														 //if
														 
												if( mb_strtolower($_POST['logpw']) != mb_strtolower($record_cache['logpw']))
												{
													echo('Bledne haslo.');
												}
													else
												if (!empty($_POST['rodzaj_wpisu'])  ) {

 		                                                $rodzaj = $_POST['rodzaj_wpisu'];
		                                                 
														 echo ((htmlspecialchars($nazwa, ENT_COMPAT, 'UTF-8')));
                                                       //  echo ('<br> TA STRONA ZAPISZE DO BAZY DANYCH');	
														 	 
														 
														  $cache_id_ok = $_POST['cache_id'];
														
														 
														 $log_date_month = $_POST['logmonth'];
														 $log_date_day = $_POST['logday'];
														 $log_date_year = $_POST['logyear'];
														
														
														//sprawdz ocene
														//sprawdz rekomendacje
														$ocena=$_POST['ocena'];
														
														
														$rekomendacja= $_POST['rekomendacja'];
														
														
														$tresc=$_POST['tresc'];
														
														
														
														if ($ocena != 'nie')
														 {
														   $sql = "SELECT count(*) FROM scores WHERE user_id='".sql_escape($record_user['user_id'])."' AND cache_id='".sql_escape(intval($cache_id))."'";
						$is_scored_query = mysql_query($sql);
						if( mysql_result($is_scored_query,0) == 0 && $record_user['user_id'] != $record_cache['user_id'])
						{					
							$sql = "UPDATE caches SET score=(score*votes+".sql_escape(intval($_POST['ocena'])).")/(votes+1), votes=votes+1 WHERE cache_id=".sql_escape($cache_id)." ;";
							mysql_query($sql);
							$sql = "INSERT INTO scores (user_id, cache_id, score) VALUES('".sql_escape($record_user['user_id'])."', '".sql_escape(intval($cache_id))."', '".sql_escape(intval($_POST['ocena']))."') ;";
							mysql_query($sql);						
						}							 
														 
														 } 
														
														
														
														
								$log_date = date('Y-m-d', mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year));
								
								$usrid = $record_user['user_id'];
								
								$uuid = mb_strtoupper(md5(uniqid(rand(), true)));

		//split into XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX (type VARCHAR 36, case insensitiv)
		$uuid = mb_substr($uuid, 0, 8) . '-' . mb_substr($uuid, -24);
		$uuid = mb_substr($uuid, 0, 13) . '-' . mb_substr($uuid, -20);
		$uuid = mb_substr($uuid, 0, 18) . '-' . mb_substr($uuid, -16);
		$uuid = mb_substr($uuid, 0, 23) . '-' . mb_substr($uuid, -12);
								
								mysql_query("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`) VALUES ('', '".sql_escape($cache_id)."', '".sql_escape($usrid)."', '".sql_escape($rodzaj)."', '".sql_escape($log_date)."', '".sql_escape($tresc)."', '0', '0', NOW(), NOW(), '".sql_escape($uuid)."', '2');");
														
								mysql_query("INSERT INTO `sys_sessions` (`uuid`, `user_id`, `permanent`, `last_login`) VALUES ('".sql_escape($uuid)."', '".sql_escape($record_user['user_id'])."', '0', NOW());");					
													
													$rs = mysql_query("SELECT `founds`, `notfounds`, `notes`, `last_found` FROM `caches` WHERE `cache_id`='".sql_escape($cache_id)."';");
						$record = mysql_fetch_array($rs);

						$last_found = '';	
												
												if ($rodzaj == '1' || $rodzaj == '7')
						{
							$tmpset_var = '`founds`=\'' . ($record['founds'] + 1) . '\'';

							$dlog_date = mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year);
							if ($record['last_found'] == NULL)
							{
								$last_found = ', `last_found`=\'' . date('Y-m-d', $dlog_date) . '\'';
							}
							elseif (strtotime($record['last_found']) < $dlog_date)
							{
								$last_found = ', `last_found`=\'' . date('Y-m-d', $dlog_date) . '\'';
							}
						}
						elseif ($rodzaj == '2' || $rodzaj == '8') // fuer Events wird not found als will attend Zaehler missbraucht
						{
							$tmpset_var = '`notfounds`=\'' . ($record['notfounds'] + 1) . '\'';
						}
						elseif ($rodzaj == '3')
						{
							$tmpset_var = '`notes`=\'' . ($record['notes'] + 1) . '\'';
						}

						
							mysql_query("UPDATE `caches` SET " . sql_escape($tmpset_var) . sql_escape($last_found) . " WHERE `cache_id`='".sql_escape($cache_id)."';");
						

												
							$rs = mysql_query("SELECT `log_notes_count`, `founds_count`, `notfounds_count` FROM `user` WHERE `user_id`='".sql_escape($record_user['user_id'])."';");
						$record = mysql_fetch_array($rs);

						if ($rodzaj == '1' || $rodzaj == '7')
						{
							$tmpset_var = '`founds_count`=\'' . (sql_escape($record['founds_count']) + 1) . '\'';
						}
						elseif ($rodzaj == '2')
						{
							$tmpset_var = '`notfounds_count`=\'' . (sql_escape($record['notfounds_count']) + 1) . '\'';
						}
						elseif ($rodzaj == '3')
						{
							$tmpset_var = '`log_notes_count`=\'' . (sql_escape($record['log_notes_count']) + 1) . '\'';
						}
						if ($rodzaj == '1' || $rodzaj == '2' || $rodzaj == '3' || $rodzaj == '7')
						{
							mysql_query("UPDATE `user` SET " . $tmpset_var . " WHERE `user_id`='".sql_escape($record_user['user_id'])."';");
						}					
												
										
						
						$rs = mysql_query("SELECT `log_types`.`cache_status` FROM `log_types` WHERE `id`='".sql_escape($log_type)."';");
						$record = mysql_fetch_array($rs);
						
							$cache_status = $record['cache_status'];
							if($cache_status != 0)
							{
								mysql_query("UPDATE `caches` SET `status`='".sql_escape($cache_status)."' WHERE `cache_id`='".sql_escape($cache_id)."';");
							}
								
											
											
						
						if ($rekomendacja == 'tak')
						{
							mysql_query("INSERT IGNORE INTO `cache_rating` (`user_id`, `cache_id`) VALUES('".sql_escape($record_user['user_id'])."', '".sql_escape($cache_id)."');");	
												
						}						
									
							
									
									
							echo ('<br>Twoj wpis dodany do bazy<br>');
												
														
														
														// <option>BBB</option>
														}
														else
														
														{
														 
														 echo ('Cos nie tak!!!');
														}
														  
														//koniec_if  
														  
														  
                                                        //poprawne wsio wiec dodajemy log...

                                                        
                                                        
                                                

                                                         
                                                    


						}
						else if ($count == 0)
						{
						echo('<br>Nie ma takiej skrzynki<br><br><a href="dodaj_log.php">Powrot</a>');
							exit;
						}
						else if ($count > 1)
						{
							
							echo('<br>Bledna skrzynka<br><br><a href="dodaj_log.php">Powrot</a>');
							
							exit;
						}

                  
 mysql_free_result($rs);
						unset($count);
           
   

            



             
        // aaa koniec sprawdzania 







        } 
      else {

               echo('Zalogowany, <b>'. $logino . '</b><br>');
               echo('<font size="+2">Szukanie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr />');
               echo('<font size="1">Szukaj waypointa:</font><br/><form action="dodaj_log.php" method="post" style="display:inline;">OPXXXX<br><input type="text" name="userinput" size="5" style="height:14px;"/><br/><br/><input type="submit" value="Szukaj"/></form>');


           }  




    }
   else 
   {
     echo('Blad cookie<br>');
     echo('<font size="+2">Zaloguj sie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr /><form action=login.php method=post>Uzytkownik: <br><input name="UZYTKOWNIK" type=text size="10"><br><br>Haslo:<br> <input name="HASELO" type=password size="10"><br><br><input type=submit value="Wejdz"></form>');
   }
	
} 
else
{
echo('Nie zalogowany. Jesli nie mozesz sie zalogowac, sprawdz obsluge coockies w telefonie.<br>');
echo('<font size="+2">Zaloguj sie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr /><form action=login.php method=post>Uzytkownik: <br><input name="UZYTKOWNIK" type=text size="10"><br><br>Haslo:<br> <input name="HASELO" type=password size="10"><br><br><input type=submit value="Wejdz"></form>');


}



?>


<hr /><center>
</center>
</body>
</html>
