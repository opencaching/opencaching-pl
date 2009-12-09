<?php
  if ($_SERVER["QUERY_STRING"] == "source") {
    header("Content-Type: text/plain");
    readfile($_SERVER["SCRIPT_FILENAME"]);
  } elseif ($_POST["Rating"] == "TRUE") {
    $Equipment = $_POST["Equipment"];
    $Night = $_POST["Night"];
    $Length = $_POST["Length"];
    $Trail = $_POST["Trail"];
    $Overgrowth = $_POST["Overgrowth"];
    $Elevation = $_POST["Elevation"];
    $Difficulty = $_POST["Difficulty"];
    $maximum = max($Equipment, $Night, $Length, $Trail, $Overgrowth, $Elevation);
    if ($maximum > 0) {
      $terrain = $maximum + 0.25 * ($Equipment == $maximum)  + 0.25 * ($Night == $maximum)
                          + 0.25 * ($Length == $maximum)     + 0.25 * ($Trail == $maximum)
                          + 0.25 * ($Overgrowth == $maximum) + 0.25 * ($Elevation == $maximum) - 0.25;
    };
    $intTerr = floor($terrain);

?>
<html><head>
  <title>Results - Geocache Rating System</title>
  <style type="text/css"><!--
    .top  { background: #eeeeee; }
    .head { background: #eef6e5; }
    .odd  { background: #ddeeff; }
    .even { background: #ffffcc; }
  //--></style>
</head>

<body>
<table width=600 class="content">
	<colgroup>
		<col width="100">
	</colgroup>
	<tr><td width=600 class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" border="0" width="32" height="32" alt="ABC" title="ABC" align="middle"><font size="4">  <b>System oceny skrzynki</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>

<table>
<tr>
  <td class="top" colspan=2><br><font color=blue>Twoja skrzynka została oceniona na: <strong><?php echo ($Difficulty + 1) . '/' . ($terrain + 1); ?></font></strong>
  <br><br>Uwaga:  Jeśli stopień trudności jest pomiędzy np 1 a 2 to w ustawieniach skrzynki możesz podawać poziomy trudności z dokładnością 0.5 np 1.5
  </td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <th class="head" colspan=2 align="left">Poziom trudności zadania: <em><?php echo ($Difficulty + 1); ?></em></th>
</tr>
<tr>
  <td class="odd" align="right" nowrap>*</td>
  <td class="odd"><?php if ($Difficulty == 0) echo '<strong><em>'; ?>Łatwy. Łatwo widoczna i może być znaleziona w przeciągu paru minut poszukiwań.
      <?php if ($Difficulty == 0) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap>**</td>
  <td class="even"><?php if ($Difficulty == 1) echo '<strong><em>'; ?>Średni. Przeciętny poszukiwacz skrzynek znajdzie ją w czasie mniejszym niż 30 min poszukiwań.
      <?php if ($Difficulty == 1) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap>***</td>
  <td class="odd"><?php if ($Difficulty == 2) echo '<strong><em>'; ?>Wyzwanie. Doświadczonemu poszukiwaczowi skrzynek może zająć dużą część dnia na odszukanie skrzynki.
      <?php if ($Difficulty == 2) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap>****</td>
  <td class="even"><?php if ($Difficulty == 3) echo '<strong><em>'; ?>Trudny. Prawdziwe wyzwanie dla doświadczonego poszukiwacza skrzynek. Może wymagać specjalnych umiejętności lub wiedzy lub gruntownego przygotowania do poszukiwań. Może wymagać wielu dni/wycieczek aby znaleźć skrzynkę.
      <?php if ($Difficulty == 3) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap>*****</td>
  <td class="odd"><?php if ($Difficulty == 4) echo '<strong><em>'; ?>Ekstremalne. Poważne psychczne lub fizyczne wyzwanie. Wymagające wyspecjalizowanej wiedzy i umiejętności lub sprzętu aby odnaleźć skrzynkę.
      <?php if ($Difficulty == 4) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <th class="head" colspan=2 align="left">Poziom trudności terenu: <em><?php echo ($terrain + 1); ?></em></th>
</tr>
<tr>
  <td class="odd" align="right" nowrap>*</td>
  <td class="odd"><?php if ($intTerr == 0) echo '<strong><em>'; ?>Dostępny dla niepełnosprawnych. Teren wybetonowany, utwardzony i stosunkowo płaski. Mniej niż 0.5 km wymagana jest wycieczka.
      <?php if ($intTerr == 0) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap>**</td>
  <td class="even"><?php if ($intTerr == 1) echo '<strong><em>'; ?>Dostępny dla małych dzieci. Teren jest ogólnie wzdłuż oznakowanych szlaków. Nie ma stromych zmian elewacji terenu. Mniej niż 3 km wymagana wędrówka
      <?php if ($intTerr == 1) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap>***</td>
  <td class="odd"><?php if ($intTerr == 2) echo '<strong><em>'; ?>Niedostępne dla małych dzieci. Przeciętny dorosły lub starsze dziecko powinno byc OK w zależności od kondycji. Teren możliwe jest że leży poza szlakami, drogami, mogą być strome zmiany elewacji terenu i więcej niż 3 km wędrówka wymagana.
      <?php if ($intTerr == 2) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap>****</td>
  <td class="even"><?php if ($intTerr == 3) echo '<strong><em>'; ?>Tylko dla zaawasnowanych entuzajstów wycieczek. Teren jest pradopodobnie poza szlakami i drogami. Będzie posiadał jedną lub więcej nastepujących cech: bardzo nachylone elelwacje terenu (wymagające użycia rąk) i więcej niż 16 km wędrówka wymagana.       
  <?php if ($intTerr == 3) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap>*****</td>
  <td class="odd"><?php if ($intTerr == 4) echo '<strong><em>'; ?>Wymaga wyspecjalizowanego sprzętu i wiedzy lub doświadczenia (łódki, samochodu 4x4, wspinaczka na skałach, nurkowanie, itp) lub ogólnie jest ekstremalnie cięzki.
      <?php if ($intTerr == 4) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td class="top" colspan=2><a href='?'>Jeszcze raz chcesz ocenić?</a></td>
</tr>
</table>
</body>
</html>
<?php
  } else {
?>
<html><head>
  <title>Geocache Rating System</title>
  <style type="text/css">
    .head { background: #eef6e5; }
    .odd  { background: #ddeeff; }
    .even { background: #ffffcc; }
    .foot { background: #eeeeee; }
  </style>
</head>

<body>
<table width=600 class="content">
	<colgroup>
		<col width="100">
	</colgroup>
	<tr><td width=600 class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" border="0" width="32" height="32" alt="ABC" title="ABC" align="middle"><font size="4">  <b>System oceny skrzynki</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>


<p>Nie wiesz jaki jest poziom trudnosci Twojej skrzynki? Odpowiedz na poniższe pytania:</p>

<form action='?' method="post">
<table>
</tr>
  <th class="head" colspan=2 align="left">Czy wymagany jest specjalistyczny sprzęt?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Equipment" value="0" checked></td>
  <td class="odd">Nie</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Equipment" value="4"></td>
  <td class="even">Tak</td>
</tr>
<tr>
  <td class="foot" colspan=2>Potrzebna łódka, samochód terenowy, sprzęt do wspinaczki, nurkowania...?</td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>

</tr>
  <th class="head" colspan=2 align="left">Czy dotarcie do skrzynki i powrót zajmie więcej niż jeden dzień ?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Night" value="0" checked></td>
  <td class="odd">Nie</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Night" value="3"></td>
  <td class="even">Tak</td>
</tr>
<tr>
  <td class="foot" colspan=2></td>
</tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">Jaka odległosć trzeba pokonać pieszo?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Length" value="0" checked></td>
  <td class="odd">mniej niż 1 km<br></td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Length" value="1"></td>
  <td class="even">między 1 a 3 km</td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Length" value="2"></td>
  <td class="odd">między 3 a 16 km</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Length" value="3"></td>
  <td class="even">powyżej 16 km</td>
</tr>
<tr>
  <td class="foot" colspan=2>Wędróka pisze liczona od parkingu najblizszego do skrzynki.</td>
</tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">Jak wyglada szlak?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Trail" value="0" checked></td>
  <td class="odd">Droga utwardzona 
      <br><em>asfalt, beton lub kostka brukowa.</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Trail" value="1"></td>
  <td class="even">Droga gruntowa
      <br><em> 	Droga gruntowa
      można wjechać zwykłym rowerem, pchać wózek.</em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Trail" value="2"></td>
  <td class="odd">Inny rodzaj drogi 
      <br><em>szuter, piasek, błoto..., jesli rower to tylko górski.</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Trail" value="3"></td>
  <td class="even">Szlak? Jaki szlak? 
      <br><em>Nie ma żadnej scieżki, dojazd nie wchodzi w grę.</em>
  </td>
</tr>
<tr>
  <td class="foot" colspan=2></td>
</tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">Czy szlak prowadzi przez zarosla?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Overgrowth" value="0" checked></td>
  <td class="odd">Nie 
      <br><em></em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Overgrowth" value="1"></td>
  <td class="even">Niewielkie zarosla 
      <br><em></em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Overgrowth" value="2"></td>
  <td class="odd">Tak, wysokie zarosla, kolce 
      <br><em></em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Overgrowth" value="3"></td>
  <td class="even">Totalne haszcze 
      <br><em>Nie widać drugiej strony, potrzebna maczeta lub inny przyrzad. Najczęsciej kolce lub trujace rosliny.</em>
  </td>
</tr>
</tr>
  <td class="foot" colspan=2></td>
<tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">Jakie jest ukształtowanie terenu?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Elevation" value="0" checked></td>
  <td class="odd">Przeważnie płaskie 
      <br><em>Niewielkie różnice wysokosci. Można pokonać rowerem, wózkiem dziecięcym, inwalidzkim....</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Elevation" value="1"></td>
  <td class="even">Niewielkie wzniesienia 
      <br><em>Można pokonać rowerem.</em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Elevation" value="2"></td>
  <td class="odd">Teren stromy 
      <br><em>Duża stromizna, rower trzeba pchać pod górę</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Elevation" value="3"></td>
  <td class="even">Urwisko 
      <br><em> 	Urwisko
      Do wejscia potrzebne cztery kończyny.</em>
  </td>
</tr>
</tr>
  <td class="foot" colspan=2></td>
<tr>
</tr>
  <td colspan=2><hr></td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">Czy łatwo znależć skrzynkę?</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Difficulty" value="0" checked></td>
  <td class="odd">Miejsce ukrycia jest oczywiste..</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Difficulty" value="1"></td>
  <td class="even">Może być w kilku miejscach, znalezienie zajmie chwilę..</td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Difficulty" value="2"></td>
  <td class="odd">Dobrze schowany, może to być multicache lub do znalezienia potrzebne sa wskazówki..</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Difficulty" value="3"></td>
  <td class="even">Znalezienie wymaga specjalnych umiejętnosci, wiedzy lub przygotowania. Może zajać kilka dni</td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Difficulty" value="4"></td>
  <td class="odd">Znalezienie wymaga umiejętnosci i specjalistycznego sprzętu. Poważne wyzwanie..</td>
</tr>
</tr>
  <td class="foot" colspan=2>Proszę wziąc pod uwagę widzialność, dostepność lub inne przeszkody odpowiadając na pytania.</td>
<tr>
</tr>
  <td colspan=2><hr></td>
<tr>
</tr>
  <td colspan=2><input type="hidden" name="Rating" value="TRUE">
     <input type="submit" value="Wylicz">
     <input type="reset" value="Wyszczysc formularz"></td>
<tr>
</table>
</form>
</body>
</html>
<?php
  };
?>

