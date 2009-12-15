<?php
  // get form data
  if(count($_REQUEST)) foreach($_REQUEST as $name => $val) eval('$' . $name . ' = "' . $val . '";');

  // initialize values
  if(!$graphCreate) {
    $graphType = 'hBar';
    $graphShowValues = 1;
    $graphValues = '123,456,789,987,654,321';
    $graphLabels = 'Horses,Dogs,Cats,Birds,Pigs,Cows';
    $graphBarWidth = 20;
    $graphBarLength = '1.0';
    $graphLabelSize = 12;
    $graphValuesSize = 12;
    $graphPercSize = 12;
    $graphPadding = 10;
    $graphBGColor = '#ABCDEF';
    $graphBorder = '1px solid blue';
    $graphBarColor = '#A0C0F0';
    $graphBarBGColor = '#E0F0FF';
    $graphBarBorder = '2px outset white';
    $graphLabelColor = '#000000';
    $graphLabelBGColor = '#C0E0FF';
    $graphLabelBorder = '2px groove white';
    $graphValuesColor = '#000000';
    $graphValuesBGColor = '#FFFFFF';
    $graphValuesBorder = '2px groove white';
  }
  else {
    if($graphBarWidth == '') $graphBarWidth = 0;
    if($graphBarLength == '') $graphBarLength = 0;
    if($graphLabelSize == '') $graphLabelSize = 0;
    if($graphValuesSize == '') $graphValuesSize = 0;
    if($graphPercSize == '') $graphPercSize = 0;
    if($graphPadding == '') $graphPadding = 0;
  }
?>
<html>
<head>
<title>HTML-Graphs Example</title>
<style> <!--
BODY, P, SPAN, DIV, TABLE, TD, TH, UL, OL, LI {
  font-family: Arial, Helvetica;
  font-size: 12px;
  color: black;
}
H4 {
  font-family: Arial, Helvetica;
  font-size: 14px;
  margin-bottom: 5px;
}
INPUT, SELECT, TEXTAREA {
  font-family: Arial, Helvetica;
  font-size: 12px;
}
--> </style>
</head>
<body marginwidth="10" marginheight="10" topmargin="10" leftmargin="10">
<form name="f1" method="post">
<input type="hidden" name="graphCreate" value="1">
<table border="0" cellspacing="0" cellpadding="2" width="95%"><tr>
<td colspan="2">This is an example page for HTML-Graphs. Click "Create Graph" and see how the graph
looks different each time you change the form fields:</td>
</tr><tr valign="top" bgcolor="#E0F0FF">
<td align="left">
  <table border="0" cellspacing="0" cellpadding="1"><tr>
  <td align="right">Graph Type:</td>
  <td><select name="graphType" style="width:100px">
  <option value="hBar"<?php if($graphType == 'hBar') echo ' selected'; ?>>horizontal
  <option value="vBar"<?php if($graphType == 'vBar') echo ' selected'; ?>>vertical
  </select></td>
  </tr><tr>
  <td align="right">Show Values:</td>
  <td><select name="graphShowValues" style="width:100px">
  <option value="0"<?php if($graphShowValues == 0) echo ' selected'; ?>>% only
  <option value="1"<?php if($graphShowValues == 1) echo ' selected'; ?>>abs. and %
  <option value="2"<?php if($graphShowValues == 2) echo ' selected'; ?>>abs. only
  <option value="3"<?php if($graphShowValues == 3) echo ' selected'; ?>>none
  </select></td>
  </tr><tr>
  <td align="right">Values (comma-separated):</td>
  <td><input type="text" name="graphValues" maxlength="200" style="width:220px" value="<?php echo $graphValues; ?>"></td>
  </tr><tr>
  <td align="right">Labels (comma-separated):</td>
  <td><input type="text" name="graphLabels" maxlength="200" style="width:220px" value="<?php echo $graphLabels; ?>"></td>
  </tr><tr>
  <td align="right">Bars Width:</td>
  <td><input type="text" name="graphBarWidth" maxlength="3" style="width:30px" value="<?php echo $graphBarWidth; ?>"></td>
  </tr><tr>
  <td align="right">Bars Length Ratio (0.1 - 2.9):</td>
  <td><input type="text" name="graphBarLength" maxlength="3" style="width:30px" value="<?php echo $graphBarLength; ?>"></td>
  </tr><tr>
  <td align="right">Labels Font Size:</td>
  <td><input type="text" name="graphLabelSize" maxlength="2" style="width:30px" value="<?php echo $graphLabelSize; ?>"></td>
  </tr><tr>
  <td align="right">Values Font Size:</td>
  <td><input type="text" name="graphValuesSize" maxlength="2" style="width:30px" value="<?php echo $graphValuesSize; ?>"></td>
  </tr><tr>
  <td align="right">Percentage Font Size:</td>
  <td><input type="text" name="graphPercSize" maxlength="2" style="width:30px" value="<?php echo $graphPercSize; ?>"></td>
  </tr><tr>
  <td align="right">Graph Padding:</td>
  <td><input type="text" name="graphPadding" maxlength="2" style="width:30px" value="<?php echo $graphPadding; ?>"></td>
  </tr><tr>
  <td colspan="2">&nbsp;</td>
  </tr><tr>
  <td colspan="2" align="left">
  <input type="button" value="Reset" style="width:120px; font-weight:bold" onClick="document.f1.graphCreate.value=''; document.f1.submit()">
  &nbsp; <input type="submit" value="Create Graph" style="width:120px; font-weight:bold"></td>
  </tr></table>
</td>
<td align="right">
  <table border="0" cellspacing="0" cellpadding="1"><tr>
  <td align="right">Graph BG Color:</td>
  <td><input type="text" name="graphBGColor" size="14" maxlength="14" value="<?php echo $graphBGColor; ?>"></td>
  </tr><tr>
  <td align="right">Graph Border:</td>
  <td><input type="text" name="graphBorder" size="14" maxlength="30" value="<?php echo $graphBorder; ?>"></td>
  </tr><tr>
  <td align="right">Bars Color:</td>
  <td><input type="text" name="graphBarColor" size="14" maxlength="14" value="<?php echo $graphBarColor; ?>"></td>
  </tr><tr>
  <td align="right">Bars BG Color:</td>
  <td><input type="text" name="graphBarBGColor" size="14" maxlength="14" value="<?php echo $graphBarBGColor; ?>"></td>
  </tr><tr>
  <td align="right">Bars Border:</td>
  <td><input type="text" name="graphBarBorder" size="14" maxlength="30" value="<?php echo $graphBarBorder; ?>"></td>
  </tr><tr>
  <td align="right">Labels Color:</td>
  <td><input type="text" name="graphLabelColor" size="14" maxlength="14" value="<?php echo $graphLabelColor; ?>"></td>
  </tr><tr>
  <td align="right">Labels BG Color:</td>
  <td><input type="text" name="graphLabelBGColor" size="14" maxlength="14" value="<?php echo $graphLabelBGColor; ?>"></td>
  </tr><tr>
  <td align="right">Labels Border:</td>
  <td><input type="text" name="graphLabelBorder" size="14" maxlength="30" value="<?php echo $graphLabelBorder; ?>"></td>
  </tr><tr>
  <td align="right">Values Color:</td>
  <td><input type="text" name="graphValuesColor" size="14" maxlength="14" value="<?php echo $graphValuesColor; ?>"></td>
  </tr><tr>
  <td align="right">Values BG Color:</td>
  <td><input type="text" name="graphValuesBGColor" size="14" maxlength="14" value="<?php echo $graphValuesBGColor; ?>"></td>
  </tr><tr>
  <td align="right">Values Border:</td>
  <td><input type="text" name="graphValuesBorder" size="14" maxlength="30" value="<?php echo $graphValuesBorder; ?>"></td>
  </tr></table>
</td>
</tr></table><br>
<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr valign="top">
<td>
<h4>Graph</h4>
<?php
  if($graphValues) {
    include('graphs.inc.php');
    $graph = new BAR_GRAPH($graphType);
    $graph->values = $graphValues;
    $graph->labels = $graphLabels;
    $graph->showValues = $graphShowValues;
    $graph->barWidth = $graphBarWidth;
    $graph->barLength = $graphBarLength;
    $graph->labelSize = $graphLabelSize;
    $graph->absValuesSize = $graphValuesSize;
    $graph->percValuesSize = $graphPercSize;
    $graph->graphPadding = $graphPadding;
    $graph->graphBGColor = $graphBGColor;
    $graph->graphBorder = $graphBorder;
    $graph->barColors = $graphBarColor;
    $graph->barBGColor = $graphBarBGColor;
    $graph->barBorder = $graphBarBorder;
    $graph->labelColor = $graphLabelColor;
    $graph->labelBGColor = $graphLabelBGColor;
    $graph->labelBorder = $graphLabelBorder;
    $graph->absValuesColor = $graphValuesColor;
    $graph->absValuesBGColor = $graphValuesBGColor;
    $graph->absValuesBorder = $graphValuesBorder;
    echo $graph->create();
  }
  else echo '<h4>No values!</h4>';
?>
</td>
<td width="10">&nbsp;</td>
<td>
<h4>Code</h4>
<textarea wrap="off" style="width:330px; height:180px;">
<?php
  echo '$graph = new BAR_GRAPH("' . $graphType . '");' . "\n";
  echo '$graph-&gt;values = "' . $graphValues . '";' . "\n";
  echo '$graph-&gt;labels = "' . $graphLabels . '";' . "\n";
  echo '$graph-&gt;showValues = ' . $graphShowValues . ';' . "\n";
  echo '$graph-&gt;barWidth = ' . $graphBarWidth . ';' . "\n";
  echo '$graph-&gt;barLength = ' . $graphBarLength . ';' . "\n";
  echo '$graph-&gt;labelSize = ' . $graphLabelSize . ';' . "\n";
  echo '$graph-&gt;absValuesSize = ' . $graphValuesSize . ';' . "\n";
  echo '$graph-&gt;percValuesSize = ' . $graphPercSize . ';' . "\n";
  echo '$graph-&gt;graphPadding = ' . $graphPadding . ';' . "\n";
  echo '$graph-&gt;graphBGColor = "' . $graphBGColor . '";' . "\n";
  echo '$graph-&gt;graphBorder = "' . $graphBorder . '";' . "\n";
  echo '$graph-&gt;barColors = "' . $graphBarColor . '";' . "\n";
  echo '$graph-&gt;barBGColor = "' . $graphBarBGColor . '";' . "\n";
  echo '$graph-&gt;barBorder = "' . $graphBarBorder . '";' . "\n";
  echo '$graph-&gt;labelColor = "' . $graphLabelColor . '";' . "\n";
  echo '$graph-&gt;labelBGColor = "' . $graphLabelBGColor . '";' . "\n";
  echo '$graph-&gt;labelBorder = "' . $graphLabelBorder . '";' . "\n";
  echo '$graph-&gt;absValuesColor = "' . $graphValuesColor . '";' . "\n";
  echo '$graph-&gt;absValuesBGColor = "' . $graphValuesBGColor . '";' . "\n";
  echo '$graph-&gt;absValuesBorder = "' . $graphValuesBorder . '";' . "\n";
  echo 'echo $graph-&gt;create();';
?>
</textarea>
</td>
</tr></table>
</form>
</body>
</html>
