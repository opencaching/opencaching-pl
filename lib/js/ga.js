  /*//////////////////////////////////////////////////////////////
  TrackTiming - JG (triPPer)

  call: 	TimeTrack
  params: mode = START, END, DEBUG
  
 /////////////////////////////////////////////////////////////////*/


(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					
					  ga('create', 'UA-45656189-1', 'opencaching.pl');
					  ga('send', 'pageview');
					  
					  
					  

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

   trackTiming.prototype.sendTime = function( whichStat ) 
   {
	   	var elapsedTime = this.endTime - this.startTime;
	   	
	   	if (!this.debug) 
	   	{	
	   		ga('send', 'timing', 'STATS', whichStat, elapsedTime );
	   	}
	   	else
	   	{
	   		var msg = "Stat: " + whichStat + " Time: " + elapsedTime/1000; 	   		
	   		alert( msg );
	   	}
	   	
	     return this;
   };

   trackTiming.prototype.setDebug = function() 
   {
	   	this.debug = true;
	     	return this;
   };

   function TimeTrack( mode, whichStat )
   {		
		if (mode == "START")
		{
			t = new trackTiming();
			t.startTime();		
		} 
		
		if (mode == "END")
		{
			t.endTime();
			t.sendTime( whichStat );
		} 
		
		if (mode == "DEBUG")
		{
			t.setDebug();
		}		
   }