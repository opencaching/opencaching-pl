<?php

?>

<p>
    {nocacheid_start}
<div style="margin-top:4px;">
    <font size="2" color="#000080"><b><a href="viewcache.php?wp={wp}" target="_blank">{cachename}</a></b></font>
    <font size="2" color="#000080"> {owner}</font>
</div>
{nocacheid_end}

<div style="margin-top:16px;">
    <font size="2" color="#000080"><b>DD,dddd&deg;</b></font> <font size="1">(WGS84)</font><br/>
    {coords_f1}<br/>
</div>
<div style="margin-top:4px;">
    <font size="2" color="#000080"><b>DD&deg; MM,mmm&#39;</b></font> <font size="1">(WGS84)</font><br/>
    {coords_f2}<br/>
</div>
<div style="margin-top:4px;">
    <font size="2" color="#000080"><b>DD&deg; MM&#39; SS&#39;&#39;</b></font> <font size="1">(WGS84)</font><br/>
    {coords_f3}<br/>
</div>
<div style="margin-top:4px;">
    <font size="2" color="#000080"><b>UTM</b></font> <font size="1">(WGS84)</font><br/>
    <?php /*
      {utm_zone}{utm_letter} E {utm_east}&nbsp;&nbsp;N {utm_north} <br/>
     */ ?>
    {utm2_zone}{utm2_letter} {utm2_NS} {utm2_north}&nbsp;&nbsp;{utm2_EW} {utm2_east} <br/>
</div>
<?php /*
  <div style="margin-top:4px;">
  <font size="2" color="#000080"><b>Gau&szlig;-Kr&uuml;ger</b></font> <font size="1">(Potsdam-Datum)</font><br/>
  R {gk_rechts}&nbsp;&nbsp;H {gk_hoch} <br/>
  </div>
  <div style="margin-top:4px;">
  <font size="2" color="#000080"><b>PUWG 1992</b></font><br/>
  {x1992} {y1992}
  </div>
  <div style="margin-top:4px;">
  <font size="2" color="#000080"><b>QTH Locator</b></font><br/>
  {qthlocator}
  </div>
 */ ?>
</p>
