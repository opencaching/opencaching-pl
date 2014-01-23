<table class="content" width="97%">
    <tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}}" align="middle" /><font size="4">  <b>{{statistics}}</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
</table>
<script type="text/javascript">
TimeTrack( "START" );
</script>

<?php
global $debug_page;
if ( $debug_page )
    echo "<script type='text/javascript'>TimeTrack( 'DEBUG' );</script>";
?>

<table class="table" width="760" style="line-height: 1.6em; font-size: 10px;">
<tr>
<td><?php include ("t1.php");?>
</td></tr>
</table>

<script type="text/javascript">
TimeTrack( "END", "S1" );
</script>
