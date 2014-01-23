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
  <th class="head" colspan=2 align="left">{{rating-c_23}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Length" value="0" checked></td>
  <td class="odd">{{rating-c_24}}<br /></td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Length" value="1"></td>
  <td class="even">{{rating-c_25}}</td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Length" value="2"></td>
  <td class="odd">{{rating-c_26}}</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Length" value="3"></td>
  <td class="even">{{rating-c_27}}</td>
</tr>
<tr>
  <td class="foot" colspan=2>{{rating-c_28}}</td>
</tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">{{rating-c_29}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Trail" value="0" checked></td>
  <td class="odd">{{rating-c_30}}</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Trail" value="1"></td>
  <td class="even">{{rating-c_31}}</em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Trail" value="2"></td>
  <td class="odd">{{rating-c_32}}</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Trail" value="3"></td>
  <td class="even">{{rating-c_33}}</em>
  </td>
</tr>
<tr>
  <td class="foot" colspan=2></td>
</tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">{{rating-c_34}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Overgrowth" value="0" checked></td>
  <td class="odd">{{rating-c_35}}
      <br /><em></em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Overgrowth" value="1"></td>
  <td class="even">{{rating-c_36}}
      <br /><em></em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Overgrowth" value="2"></td>
  <td class="odd">{{rating-c_37}}
      <br /><em></em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Overgrowth" value="3"></td>
  <td class="even">{{rating-c_38}}</em>
  </td>
</tr>
</tr>
  <td class="foot" colspan=2></td>
<tr>
</tr>
  <td colspan=2>&nbsp;</td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">{{rating-c_39}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Elevation" value="0" checked></td>
  <td class="odd">{{rating-c_40}}</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Elevation" value="1"></td>
  <td class="even">{{rating-c_41}}</em>
  </td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Elevation" value="2"></td>
  <td class="odd">{{rating-c_42}}</em>
  </td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Elevation" value="3"></td>
  <td class="even">{{rating-c_43}}</em>
  </td>
</tr>
</tr>
  <td class="foot" colspan=2></td>
<tr>
</tr>
  <td colspan=2><hr></td>
<tr>

</tr>
  <th class="head" colspan=2 align="left">{{rating-c_44}}</th>
<tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Difficulty" value="0" checked></td>
  <td class="odd">{{rating-c_45}}</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Difficulty" value="1"></td>
  <td class="even">{{rating-c_46}}</td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Difficulty" value="2"></td>
  <td class="odd">{{rating-c_47}}</td>
</tr>
<tr>
  <td class="even" valign="top"><input type="radio" name="Difficulty" value="3"></td>
  <td class="even">{{rating-c_48}}</td>
</tr>
<tr>
  <td class="odd" valign="top"><input type="radio" name="Difficulty" value="4"></td>
  <td class="odd">{{rating-c_49}}</td>
</tr>
</tr>
  <td class="foot" colspan=2>{{rating-c_50}}</td>
<tr>
</tr>
  <td colspan=2><hr></td>
<tr>
</tr>
  <td colspan=2><input type="hidden" name="Rating" value="TRUE">
     <input type="submit" value="{{rating-c_51}}">
     <input type="reset" value="{{rating-c_52}}"></td>
<tr>
</table>
</form>
{{rating-c_53}}
</body>
</html>
<?php
  };
?>
