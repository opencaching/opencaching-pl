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
														 
														 
												if (!empty($_POST['rodzaj_wpisu'])  ) {

 		                                                $rodzaj = $_POST['rodzaj_wpisu'];
		                                                 
														 echo ((htmlspecialchars($nazwa, ENT_COMPAT, 'UTF-8')));
                                                         echo ('<br>');	
														 	 
														 
														 echo('<form action="zapisz_log.php?cacheid='.$cache_id.'" method="post">' );                                                        
														 echo('<input type="hidden" name="cache_id" value="'.$cache_id.'">');
								                        echo('<input type="hidden" name="rodzaj_wpisu" value="'.$rodzaj.'">');
														if ($rodzaj =='1') 
																{
																 echo('Rodzaj: Znaleziona<br> ');
															 	}
														if ($rodzaj =='2') 
																{
																 echo('Rodzaj: Nie znaleziona<br> ');
															 	}
													    if ($rodzaj =='3') 
																{
																 echo('Rodzaj: Komentarz<br><br> ');
															 	}
														if ($rodzaj =='7') 
																{
																 echo('Rodzaj: Uczestniczyl<br><br> ');
															 	}
														if ($rodzaj =='8') 
																{
																 echo('Rodzaj: Zamierza<br><br> ');
															 	}   
														 
														 $log_date_month = $_POST['logmonth'];
														 $log_date_day = $_POST['logday'];
														 $log_date_year = $_POST['logyear'];
														if (is_numeric($log_date_month) && is_numeric($log_date_day) && is_numeric($log_date_year))
				{
					$date_not_ok = (checkdate($log_date_month, $log_date_day, $log_date_year) == false);
					if($date_not_ok == false)
					{
						if(mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year)>=mktime())
							{
								$date_not_ok = true;
							}
							else
							{
								$date_not_ok = false;
							}
						
					}
				}
				else
				{
					$date_not_ok = true;
				}
														
														
													if ($date_not_ok == false)
	                                                     {
														 //data ok
														 echo ('Data: '.$_POST["logday"].'.'.$_POST["logmonth"].'.'.$_POST["logyear"].'<br>');
														 echo('<input type="hidden" name="logday" value="'.$_POST["logday"].'">');
														 echo('<input type="hidden" name="logmonth" value="'.$_POST["logmonth"].'">');
														 echo('<input type="hidden" name="logyear" value="'.$_POST["logyear"].'">');
														 
														 }
														 else
														 {
														 echo('<input type="hidden" name="logday" value="'.date("d").'">');
														 echo('<input type="hidden" name="logmonth" value="'.date("m").'">');
														 echo('<input type="hidden" name="logyear" value="'.date("Y").'">');
														 echo ('Data: '.date("d").'.'.date("m").'.'.date("Y").'<br>');
														 //data zla
														 
														 }												
													    
														
														//sprawdz ocene
														//sprawdz rekomendacje
														if (!empty($_POST['ocena'])) 
														{ 
														echo('<input type="hidden" name="ocena" value="'.$_POST['ocena'].'">'); 
														echo('Ocena: '.$_POST['ocena']);
														}
														else
														{
														$ocena = 'nie';
														echo('<input type="hidden" name="ocena" value="'.$ocena.'">'); 
														echo('Ocena: '.$ocena);
														}
														
														
														if ($_POST['rekomendacja']==true)
														{
														  echo('<br>Rekondacja: TAK');
														  echo('<input type="hidden" name="rekomendacja" value="tak">');
														  
														}
														else
														{
														echo('<br>Rekondacja: Nie');
														  echo('<input type="hidden" name="rekomendacja" value="nie">');
														
														}
														
														if ($_POST['text'])
														{
														  echo('<br>Tresc: '.$_POST['text']);
														  echo('<input type="hidden" name="tresc" value="'.$_POST['text'].'">');
														  
														}
														else
														 {
														  echo('<br>Tresc: ');
														  echo('<input type="hidden" name="tresc" value="">');
														  
														}
														echo('<br>Haslo do logu:<br><input type="hidden" name="logpw" value="'.$_POST['logpw'].'">');

														 
														 
														 
														 echo('<br><br>UWAGA! Kikniecie dalej doda log.<br><input type="submit" value="Dalej ->"/><br> </form>');
														 
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
