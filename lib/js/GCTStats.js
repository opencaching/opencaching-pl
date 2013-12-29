/*
		GoogleTableChart for Stats - 	JG (triPPer) 
										tripper1971@wp.pl
							
		2013.12				
*/


function GCTStatsGotoProfil( link )
{
	window.location.href = link;
}


function GCTStatsSetRadio(name )
{	
	var radio;
		
	if ( name == "Rok" ) 
		radio = document.getElementById("rR");
	else
		radio = document.getElementById("rD");
	
	radio.checked= true;
}


function GCTStatsGotoPosition( Position )
{
	
	
	var myPos = parseInt( Position );
	
	alert( myPos );
	
	if ( myPos != 0 )
	{
		gct.setAsSelected( myPos );	
		gct.goToPosition( myPos, 1 );
	}
};


function GCTStatsFindUser( User )
{
	var user = User;
	user = user.toUpperCase();	
	var userMax = user + 'z';
	
	var nrRowsArray = gct.getFilteredRows( [{column: 4, minValue: user, maxValue: userMax}] );


	if ( nrRowsArray.length == 1 )
	{		
		gct.setAsSelected( nrRowsArray[0] );
		gct.goToPosition( nrRowsArray[0], 1  );
	} 
	else
		gct.showRows( nrRowsArray, 1 );
	
}



// DatePicker
$(function() {	
    $('#datepicker').datepicker({
		dateFormat: 'yy-mm-dd',		
	});
	
    $('#datepicker1').datepicker({
		dateFormat: 'yy-mm-dd',		
	});	
});	