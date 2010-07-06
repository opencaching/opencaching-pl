<?

function logowanie()
{

//funkcja logowania czyli caly HTML

echo('<font size="+2">Zaloguj sie</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr /><form action=login.php method=post>Uzytkownik: <br><input name="UZYTKOWNIK" type=text size="10"><br><br>Haslo:<br> <input name="HASELO" type=password size="10"><br><br><input type=submit value="Wejdz"></form>');





}




if ((!empty($_POST['UZYTKOWNIK'])) &&  (!empty($_POST['HASELO']))  ) {
$login['USER']=$_POST['UZYTKOWNIK'];
$login['HASLO']=$_POST['HASELO'];
} 
else {



if (isset($_COOKIE['ocplm'])) {
    foreach ($_COOKIE['ocplm'] as $name => $value) {
       $login[$name]=base64_decode($value);

 // echo "$name : $value <br />\n";

//print_r($_COOKIE);

    }
	
}
else {

$login['USER']='0';
$login['HASLO']='0';

}

}

       //print_r($_COOKIE);
       
 $logino = $login['USER'];
$loginh = $login['HASLO'];
setcookie("ocplm[USER]", base64_encode($logino), time()+36000);

setcookie("ocplm[HASLO]", base64_encode($loginh), time()+36000);

echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>LOG</title></head><body>');



if (($login['USER']!='0') && ($login['HASLO']!='0')) 
{ 

//Sprawdzamy poprawnosc logowania




  mysql_connect("localhost","ocpl","n1uch@cz");
mysql_select_db("ocpl");
$logino = $login['USER'];
$loginh = $login['HASLO'];

$result1 = mysql_query("SELECT * FROM `user` WHERE `username` = '$logino' LIMIT 1; ");

$result = mysql_fetch_array($result1);

if (($result['password'] == hash('sha512', md5($login['HASLO']))) && ($result['username'] == $login['USER']) && ($result['is_active_flag'] == '1') && ($result['rules_confirmed'] == '1'))
{ 
echo ('<font size="+2">Panel OC</font><br /><a href="index.php">&raquo; Strona Glowna</a><br /><hr /><br />Zalogowany, <b>');
echo $result['username'];
echo ('</b><br><a href="logout.php">Wyloguj</a><br><br>');
echo ('<a href="dodaj_log.php">Dodaj Log</a><br>');
echo ('<a href="ostatnie_logi.php">Ostatnie Logi</a><br>');



//DODAC SET_COOCKIE


}
else

{ 
//echo $result->password;

//if ($result['password'] != hash('sha512', md5($login['HASLO']))) echo('Bledne Haslo');
//if ($result['username'] != $login['USER']) echo('Bledna nazwa uzytkownika');
//if ($result['is_active_flag'] != '1')  echo('User nie aktywny');
//if ($result['rules_confirmed'] != '1') echo('User nie zaakceptowal regulaminu');
echo('Blad - Zaloguj sie ponownie<br><br>');


logowanie();


}



}
else
{
//Odwolanie do formularza logowania

logowanie();



}


?>

<hr /><center>
</center>
</body>
</html>
