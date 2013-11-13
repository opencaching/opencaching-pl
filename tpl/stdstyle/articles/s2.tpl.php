 
<script>
//JG - włączenie śledzenia na potrzeby oszacowania użycia zasobów serwera 

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45656189-1', 'opencaching.pl');
  ga('send', 'pageview');

/*//////////////////////////////////////////////////////////////
 TrackTiming

 call: 	TimeTrack
 params: mode = START, END, DEBUG
 
/////////////////////////////////////////////////////////////////*/
  function trackTiming()
  {
  	this.startTime;
  	this.endTime;
  	
  	this.debug = false;
  };

  trackTiming.prototype.startTime = function() {
    this.startTime = new Date().getTime();  
    return this;
  };

  trackTiming.prototype.endTime = function() {
    this.endTime = new Date().getTime();
    return this;
  };

  trackTiming.prototype.sendTime = function() 
  {
  	var elapsedTime = this.endTime - this.startTime;
  	
  	if (!this.debug) 
  	{		
  		_gaq.push(['_trackTiming', 'DB', 'Run Query', elapsedTime, 'OCPL-Statystka: Seeker', 100]);
  	}
  	else
  	{
  		alert( elapsedTime/1000 );
  	}
  	
    return this;
  };

  trackTiming.prototype.setDebug = function() 
  {
  	this.debug = true;
    	return this;
  };

  function TimeTrack( mode )
  {		
  	if (mode == "START")
  	{
  		t = new trackTiming();
  		t.startTime();		
  	} 

  	if (mode == "END")
  	{
  		t.endTime();
  		t.sendTime();
  	} 

  	if (mode == "DEBUG")
  	{
  		t.setDebug()
  	}		
  }
  

</script>


<table class="content">
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

<table width="760" class="table" style="line-height: 1.6em; font-size: 10px;">
<tr>
<td><?php include ("t2.php");?>
</td></tr>
</table>

<script type="text/javascript">
TimeTrack( "END" );
</script>
