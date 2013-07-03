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
	<tr><td width=600 class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="ABC" title="ABC" align="middle" /><font size="4">  <b>{{rating-c_01}}</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>

<table>
<tr>
  <td class="top" colspan=2><br /><font color=blue>{{rating-c_02}} <strong><?php echo ($Difficulty + 1) . '/' . ($terrain + 1); ?></font></strong>
  <br /><br />{{rating-c_03}}
  </td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <th class="head" colspan=2 align="left">{{rating-c_04}} <em><?php echo ($Difficulty + 1); ?></em></th>
</tr>
<tr>
  <td class="odd" align="right" nowrap="nowrap">*</td>
  <td class="odd"><?php if ($Difficulty == 0) echo '<strong><em>'; ?>{{rating-c_05}}
      <?php if ($Difficulty == 0) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap="nowrap">**</td>
  <td class="even"><?php if ($Difficulty == 1) echo '<strong><em>'; ?>{{rating-c_06}}
      <?php if ($Difficulty == 1) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap="nowrap">***</td>
  <td class="odd"><?php if ($Difficulty == 2) echo '<strong><em>'; ?>{{rating-c_07}}
      <?php if ($Difficulty == 2) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap="nowrap">****</td>
  <td class="even"><?php if ($Difficulty == 3) echo '<strong><em>'; ?>{{rating-c_08}}
      <?php if ($Difficulty == 3) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap="nowrap">*****</td>
  <td class="odd"><?php if ($Difficulty == 4) echo '<strong><em>'; ?>{{rating-c_09}}
      <?php if ($Difficulty == 4) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <th class="head" colspan=2 align="left">{{rating-c_10}} <em><?php echo ($terrain + 1); ?></em></th>
</tr>
<tr>
  <td class="odd" align="right" nowrap="nowrap">*</td>
  <td class="odd"><?php if ($intTerr == 0) echo '<strong><em>'; ?>{{rating-c_11}}
      <?php if ($intTerr == 0) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap="nowrap">**</td>
  <td class="even"><?php if ($intTerr == 1) echo '<strong><em>'; ?>{{rating-c_12}}
      <?php if ($intTerr == 1) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap="nowrap">***</td>
  <td class="odd"><?php if ($intTerr == 2) echo '<strong><em>'; ?>{{rating-c_13}}
      <?php if ($intTerr == 2) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="even" align="right" nowrap="nowrap">****</td>
  <td class="even"><?php if ($intTerr == 3) echo '<strong><em>'; ?>{{rating-c_14}}       
  <?php if ($intTerr == 3) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td class="odd" align="right" nowrap="nowrap">*****</td>
  <td class="odd"><?php if ($intTerr == 4) echo '<strong><em>'; ?>{{rating-c_15}}
      <?php if ($intTerr == 4) echo '</em></strong>'; ?></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td class="top" colspan=2><a href='?'>{{rating-c_16}}</a></td>
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
	<tr><td width=600 class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="ABC" title="ABC" align="middle" /><font size="4">  <b>{{rating-c_01}}</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>


<p>{{rating-c_17}}</p>

<form action='?' method="post">
<table>
</tr>
  <th class="head" colspan=2 align="left">{{rating-c_18}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Equipment" value="0" checked></td>
  <td class="odd">{{rating-c_19}}</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Equipment" value="4"></td>
  <td class="even">{{rating-c_20}}</td>
</tr>
<tr>
  <td class="foot" colspan=2>{{rating-c_21}}</td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>

</tr>
  <th class="head" colspan=2 align="left">{{rating-c_22}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Night" value="0" checked></td>
  <td class="odd">{{rating-c_19}}</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Night" value="3"></td>
  <td class="even">{{rating-c_20}}</td>
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
  <td class="odd">mniej niż 1 km<br /></td>
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
      <br /><em>asfalt, beton lub kostka brukowa.</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Trail" value="1"></td>
  <td class="even">Droga gruntowa
      <br /><em> 	Droga gruntowa
      można wjechać zwykłym rowerem, pchać wózek.</em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Trail" value="2"></td>
  <td class="odd">Inny rodzaj drogi 
      <br /><em>szuter, piasek, błoto..., jesli rower to tylko górski.</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Trail" value="3"></td>
  <td class="even">Szlak? Jaki szlak? 
      <br /><em>Nie ma żadnej scieżki, dojazd nie wchodzi w grę.</em>
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
      <br /><em></em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Overgrowth" value="1"></td>
  <td class="even">Niewielkie zarosla 
      <br /><em></em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Overgrowth" value="2"></td>
  <td class="odd">Tak, wysokie zarosla, kolce 
      <br /><em></em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Overgrowth" value="3"></td>
  <td class="even">Totalne haszcze 
      <br /><em>Nie widać drugiej strony, potrzebna maczeta lub inny przyrzad. Najczęsciej kolce lub trujace rosliny.</em>
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
      <br /><em>Niewielkie różnice wysokosci. Można pokonać rowerem, wózkiem dziecięcym, inwalidzkim....</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Elevation" value="1"></td>
  <td class="even">Niewielkie wzniesienia 
      <br /><em>Można pokonać rowerem.</em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Elevation" value="2"></td>
  <td class="odd">Teren stromy 
      <br /><em>Duża stromizna, rower trzeba pchać pod górę</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Elevation" value="3"></td>
  <td class="even">Urwisko 
      <br /><em> 	Urwisko
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
     <input type="reset" value="Wyczyść formularz"></td>
<tr>
</table>
</form>
System oceny dostępny dzięki stronie http://www.clayjar.com/gcrs/
</body>
</html>
<?php
  };
?>
