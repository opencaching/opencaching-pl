<table class="content" width="97%">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="Statystyki" title="Statystyki" align="middle" /><font size="4">  <b>{{statistics}}</b></font></td></tr>
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

<?php include ("top.php");?>

<script type="text/javascript">
TimeTrack( "END", "S5" );
</script>