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
	var userMax = user + 'ż';
	
	var nrRowsArray = gct.getFilteredRows( [{column: 4, minValue: user, maxValue: userMax}] );


	if ( nrRowsArray.length == 1 )
	{		
		gct.setAsSelected( nrRowsArray[0] );
		gct.goToPosition( nrRowsArray[0], 1  );		
	} 
	else
	{
		gct.showRows( nrRowsArray, 1 );
		
	}
}


function GCTEventSelectFunction( event )
{
	var RecArray =  gct.getSelection();
	var nrSel = RecArray.length;
	
	document.Details.SelectedUser.value = nrSel; 
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


$(document).ready(function() {
	$( "#HelpDialog" ).dialog({	
	autoOpen: false,
	width: 350,
	height: 200,
	show: {
	effect: "fade",
	duration: 1000
	},
	hide: {
	effect: "fade",
	duration: 1000
	}
	});
	
	$( "#bChartHelp" ).click(function() {
	$( "#HelpDialog" ).dialog( "open" );
	});
	
	$('#HelpDialog').addClass('GCT-div-dialog');
});

$(document).ready(function() { 
    var dlg=$('#dialog').dialog({		
		autoOpen: false,
		width: 800,
		height: 600,				 
		show: { effect: "explode", duration: 1000 },
		hide: { effect: "explode", duration: 1000 },		
		modal: true			

    });

    $('#bDetails').click(function(e) {
        var link = 'lib/th102.php';
       
       	var item;
    	var UserID = [];
    	var RecArray =  gct.getSelection();
    	
    	
		for (var i = 0; i < RecArray.length; i++) 
		{
		   item = RecArray[i];
			    
		   val = gct.getValue( item.row, 5 );
		   UserID[ i ] = val; 
		}
    	 
		link = link+"?UserID="+UserID;
		
		
		var DateFrom = document.getElementById("DateFrom");
		var DateTo = document.getElementById("DateTo");
		var NameOfStat = document.getElementById("stat");
		 		
		link = link + "&DF=" + DateFrom.value + "&DT=" + DateTo.value + "&stat=" + NameOfStat.value;  	
		//alert( link );
		
    	e.preventDefault();
    	dlg.load(link, function(){
    		
    	    dlg.dialog('open');
    	});
    });
    
    $('#dialog').addClass('GCT-div-dialog');
    
});

