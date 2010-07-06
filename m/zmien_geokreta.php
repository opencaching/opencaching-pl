<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Zmiana Geokreta</title>
</head>

<body>
<font size="+2">
Zmień Mobilnie GK
</font /><br />
<a href="index.php">&raquo; Strona Główna</a><br /><hr />
<br />




<form action="http://geokrety.org/ruchy.php" method="post" /><br />
1. Typ logu <br />

<select size="1" name="logtype">
	<option value="0"  >Wrzuciłem GeoKreta</option>
	<option value="1"  >Zabrałem GeoKreta</option>
	<option value="3"  >Spotkałem GeoKreta</option>
	<option value="2"  >Komentarz</option>
	
</select><br /><br />

2.  Identyfikacja GeoKreta<br />

Tracking Code <input type="text" name="nr" id="nr" size="11" value="" /> <br /><br />

3.  Nowa lokalizacja

Waypoint:
<br /><input type="text" name="wpt" value="" id="wpt" size="9" /> OPXXXX<br /><br />
 

Użytkownik:<br />
<input type="text" name="username" id="username" value="" maxlength="20" /><br><br />

Data operacji:<br />
<input type="text" name="data" id="data" value="" /> YYYY-MM-DD<br /><br />
Czas:<br />
<input id="godzina" name="godzina" size="2" maxlength="2" value="12" /> HH (0-23)<br />
<input id="minuta" name="minuta" size="2" maxlength="2" value="00" /> MM<br /><br />

Komentarz:<br />
<textarea style="width: 90%;" id="poledoliczenia" name="comment" rows="3" cols="25" maxlength="512"></textarea><br /><br />


<input type="submit" value=" Pisz! " /></form>



</body>
</html>
