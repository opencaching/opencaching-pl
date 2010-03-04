<?
require("common.inc.php");

if (isset($_COOKIE['ocplm'])) 
{
    foreach ($_COOKIE['ocplm'] as $name => $value)
      {
       $login[$name]=base64_decode($value);
       }
//print_r($_COOKIE);
$logino = $login['USER'];
$loginh = $login['HASLO'];

setcookie("ocplm[USER]", base64_encode($logino), time()+36000);

setcookie("ocplm[HASLO]", base64_encode($loginh), time()+36000);


  mysql_connect("localhost","ocpl","n1uch@cz");
  mysql_select_db("ocpl");

  $result1 = mysql_query("SELECT * FROM `user` WHERE `username` = '$logino' LIMIT 1; ");

  $result = mysql_fetch_array($result1);
  
  echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>LOG</title></head><body>');

  if (($result['password'] == hash('sha512', md5($login['HASLO']))) && ($result['username'] == $login['USER']) && ($result['is_active_flag'] == '1') && ($result['rules_confirmed'] == '1'))
    { 
     //zalogowany



if (!empty($_POST['userinput'])  ) 
       {
        // aaa nie jest puste wyszukiwanie, wiec sprawdzamy
            
            echo('Zalogowany, <b>'. $logino . '</b><br>');
            echo('<font size="+2">Szukanie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr />');
   
            
                  $userinput= $_POST['userinput'];
         
                $rs=  mysql_query("SELECT * FROM `caches` WHERE `wp_oc` = '$userinput' AND `status`=1; ");
                $count = mysql_num_rows($rs);
                      
                     if ($count == 1)
						{
							$record = mysql_fetch_array($rs);
							


                                                           
                                                         $cachid= $record['cache_id'];

                                                         $nazwa= $record['name'];

                                                         //echo $cachid;
                                                         
                                                         echo $userinput;
                                                         echo ('<b><br>');
                                                         echo ((htmlspecialchars($nazwa, ENT_COMPAT, 'UTF-8')));
                                                         echo ('</b><br>');
                                                          echo ('<br>Czy do tej skrzynki dodac log?');
                                                           echo ('<br>');
                                                           echo ('<a href="dodaj_log_ok.php?cacheid='.$cachid.'">TAK </a><br><br><a href="dodaj_log.php">NIE</a><br><br>');

							unset($record);
                                                

                                                         
                                                    


						}
						else if ($count == 0)
						{
						echo('<br>Nie znalazlem waypointow<br><br><a href="dodaj_log.php">Powrot</a>');
							exit;
						}
						else if ($count > 1)
						{
							
							echo('<br>Bledne wyszukiwanie<br><br><a href="dodaj_log.php">Powrot</a>');
							
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
