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
														 
														 echo('<form action="dodaj_log_ok_2.php?cacheid='.$cache_id.'" method="post">' );
														echo('<input type="hidden" name="cache_id" value="'.$cache_id.'">');
								                        echo('<input type="hidden" name="rodzaj_wpisu" value="'.$rodzaj.'">');
														
														   
														    if ($rodzaj == '1' or $rodzaj=='7') 
															 {
															  // znaleziona
															    
																
															  echo('<br>Rodzaj: Znaleziona<br><br>Ocena:<br> ');
															  echo('<label><input type="radio" name="ocena" value="-3" id="radio_0" />
-3 </label><label><input type="radio" name="ocena" value="-2" id="radio_1" />-2 </label><br><label><input type="radio" name="ocena" value="-1" id="radio_2" />-1 </label><label><input type="radio" name="ocena" value="0" id="radio_3" />0 </label><br><label><input type="radio" name="ocena" value="1" id="radio_4" />1 </label><label><input type="radio" name="ocena" value="2" id="radio_5" />2   </label><br><label><input type="radio" name="ocena" value="3" id="radio_6" />3 </label><label><input type="radio" name="ocena" value="nie" id="radio_7" />Brak </label><br /><br></p>');
															
															
															
															
															echo ('<label><input type="checkbox" name="rekomendacja" />Rekomendacja</label> ');
															
															echo ('<br><br>Data: <br><input name="logday" size="2" maxlength="2" value="'.date("d").'" type="text">. <input name="logmonth" size="2" maxlength="2" value="'.date("m").'" type="text">. <input name="logyear" maxlength="4" size="4" value="'.date("Y").'" type="text"><br><br>'); 
															  
															
															echo(' <textarea name="text" id="text" cols="30" rows="5"></textarea><br>');
															if( $record_cache['logpw'] != "" )
															{
																echo(' <input type="text" size="10" name="logpw" value=""/>');
															}
															
															 
															 }
															 
															 else
															 
															 {
															    
																if ($rodzaj == '2') 
																{
																 echo('<br>Rodzaj: Nie znaleziona<br><br> ');
															 	}
																if ($rodzaj == '3') 
																{
																echo('<br>Rodzaj: Komentarz<br><br> ');
															    }
																if ($rodzaj =='8') 
																{
																echo('<br>Rodzaj: Zamierza<br><br> ');
															    }
																
																
																echo ('<br><br>Data: <br><input name="logday" size="2" maxlength="2" value="'.date("d").'" type="text">. <input name="logmonth" size="2" maxlength="2" value="'.date("m").'" type="text">. <input name="logyear" maxlength="4" size="4" value="'.date("Y").'" type="text"><br><br>'); 
															  
															
															echo(' <textarea name="text" id="text" size="4" cols="30" rows="5"></textarea><br>');
															
															 
																 
															 // nie znaleziona lub koment
															 
															 }
														   
														   
														
														 
														 echo('<br><br><input type="submit" value="Dalej ->"/><br> </form>');
														 
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
