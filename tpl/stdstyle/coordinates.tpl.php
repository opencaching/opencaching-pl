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
    {lat_dir} {lat_float}&deg;&nbsp;&nbsp;{lon_dir} {lon_float}&deg;<br/>
</div>
<div style="margin-top:4px;">
    <font size="2" color="#000080"><b>DD&deg; MM,mmm&#39;</b></font> <font size="1">(WGS84)</font><br/>
    {lat_dir} {lat_deg_int}&deg; {lat_min_float}&#39;&nbsp;&nbsp;{lon_dir} {lon_deg_int}&deg; {lon_min_float}&#39;<br/>
</div>
<div style="margin-top:4px;">
    <font size="2" color="#000080"><b>DD&deg; MM&#39; SS&#39;&#39;</b></font> <font size="1">(WGS84)</font><br/>
    {lat_dir} {lat_deg_int}&deg; {lat_min_int}&#39; {lat_sec_float}&#39;&#39;&nbsp;&nbsp;{lon_dir} {lon_deg_int}&deg; {lon_min_int}&#39; {lon_sec_float}&#39;&#39;<br/>
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
