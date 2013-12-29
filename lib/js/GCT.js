/*
 * *********************************************
 * 	Service of GoogleTableChart - JG (triPPer)
 * 						tripper1971@wp.pl
 * 
 * 
 * Simple exapmle:
 *	 <script type='text/javascript' src='https://www.google.com/jsapi'></script>
 *	 <script type="text/javascript" src="lib/js/GCT.js"></script>
 *	 <script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
 * 
 * 				var gct = new GCT()
 * 
 *		      	gct.addColumn('string', 'Names', 'width:500px; text-align: left; color: green');    	
 *   			gct.addColumn('number', 'Salary');
 *				gct.addColumn('boolean', 'Full Time Employee');
 * 
 *     			gct.addRow( ['Bob',   {v: 7000,  f: '$7,000'},  true ] );		
 *   			gct.addRow( ['Jim',   8888,  false ] );
 *   			gct.addRow( ['<span  style="color:red" >Alice</span>', {v: 12500, f: '$12,500'}, true] );
 * 				gct.addRow( ['<span  style="onmouseover="Tip(\'Hello !!! \')" onmouseout="UnTip()" >Kate</span>', {v: 12500, f: '$12,500'}, true] );
 * 
 * 				//other way of adding
 *				t.addEmptyRow();  
 *		    	t.addToLastRow( 0, 'Jacek');
 *		    	t.addToLastRow( 1, 999 );
 *		    	t.addToLastRow( 2, true);
 *
 *				
 *				t.sort([{column: 1, desc:true}]);
 *
 *				
 *				t.hideColumns( [3] );
 *				t.showRows( [0,1], 0 ); 	
 *
 *				t.drawTable();
 *
 *				nrRowsArray = t.getFilteredRows([{column: 1, value: 42}, {column: 0, minValue: 'Bob', maxValue: 'Jim'}])
 * 
 ************************************************ 
 */



 
google.load('visualization', '1', {packages:['table']});

function GCT( divId ){
	
	this.divId = divId;
	
	//css: GCT.css
	this.vcssClassNames = {
	    'headerRow': 'GCT-background-color-white6 GCT-color-black11 GCTalign-left GCT-font-bold',
	    'tableRow': 'GCT-background-color-white GCT-color-black ',
	    'oddTableRow': 'GCT-background-color-white6 GCT-color-black',
	    'selectedTableRow': 'GCT-background-color-grey25',
	    'hoverTableRow': 'GCT-color-darkred',
	    'headerCell': 'GCT-border-color-greyD9',
	    'tableCell': 'GCT-border-color-greyD9',
	    'rowNumberCell': 'GCT-color-none' }; 
	
	
	
	this.TblOptions = {		
	    'allowHtml': true,
	    'showRowNumber': false,
	    'width': 730,
	    'alternatingRowStyle': true,
		'sortColumn': -1,
		'sortAscending': false,
	    'cssClassNames': this.vcssClassNames,
		
	    //Page settings                     
	    'page': 'enable',
	    'pageSize': 100,
	    'pagingSymbols': {prev: 'poprzednia', next: 'następna'},
		'pagingButtonsConfiguration': 'auto',
		'startPage': 0 
		};


	//delete this.TblOptions.allowHtml;				
	this.ColOption = [];
	
	this.GCTdata = new google.visualization.DataTable();
	this.GCTview;	
	this.GCTdv;
	
	this.GCTSelectBgColor = ' ;background: #DDDDDD; '; /*Automat Select*/ 
	
					
	this.addColumn; /*function( ColType, ColName, ColProperty)*/
					//Data Type: 'string, 'number', 'boolean', 'date' as Date object, timeofday as [hour, minute, second, millisenconds]
	
	//Add Data, Get Data
	this.addRow; /*function ( row )*/
	this.addEmptyRow; /* function () */
	this.addToLastRow; /* function ( col, value )*/
	this.getValue; /* function( nrRow, nrCol ) */	
	this.modifyValue; /* function ( nrRow, nrCol, value, formatValue ) */	

	//View function - !!! Use they after adding all data !!!
	this.showRows; /* function(rowArray, dt) */ 
	this.hideColumns; /* function( rowColumns ) */	
			
	
	this.drawTable; /*function ()*/
	
	//Table and Visualisation Option  
	this.addTblOption; /*function(key, value)*/	
	this.delTblOption; /*function(key)*/	
	this.addTblOptionVC; /*function(key, value)*/
	this.delTblOptionVC; /*function(key)*/
	
	//Tools
	this.sortByColumn; /*function( sortArray )*/
	this.getFilteredRows; /* function( filterArray ) return: nrRowsArray */
	this.setAsSelected; /* function( nrRow ) */
	
	//Page Navigation
	this.goToPage; /* function( nrPage, dt ) */
	this.goToPosition; /* function( nrPos, dt ) */ 
}

GCT.prototype.setAsSelected = function( nrRow )
{
	var style;
	
	
	for (var i = 0; i < this.GCTdata.getNumberOfColumns(); i++) 
	{		
		if ( typeof this.ColOption[ i ] != 'undefined' )		
			style = this.ColOption[ i ] + this.GCTSelectBgColor;
		else
			style = this.GCTSelectBgColor;
				 
		this.GCTdata.setProperty( nrRow, i, 'style', style );
	}
};

GCT.prototype.showRows = function( rowArray, dt )
{	
	if ( typeof this.GCTview == 'undefined')
		this.__setView();
																			
	this.GCTview.setRows( rowArray );
	
	if ( dt == 1)	
		this.__drawTable( this.GCTview, this.TblOptions );		
};


GCT.prototype.hideColumns = function( colArray )
{	
	if ( typeof this.GCTview == 'undefined')
		this.__setView();

	this.GCTview.hideColumns( colArray );	
};

  
GCT.prototype.addColumn = function( ColType, ColName, ColProperty) {
	
	var nrCol = this.GCTdata.addColumn(ColType, ColName);	
	
	if ( ColProperty != '' )
		this.ColOption[ nrCol ] = ColProperty;
		
	
};

GCT.prototype.addRow = function ( row )
{
	if (!isNaN(this.GCTview)) {
		alert("You try to add records after settings view !!!");
		return;
	}
	
	var nrRow = this.GCTdata.addRow( row );
		
	for( var i = 0; i < this.ColOption.length; i++ )
	{
		if ( this.ColOption[ i ] != '' )
			this.GCTdata.setProperty( nrRow ,i, 'style', this.ColOption[ i ] );
	}
};

GCT.prototype.addEmptyRow = function ()
{
	if (!isNaN(this.GCTview)) {
		alert("You try to add records after settings view !!!");
		return;
	}
	
	this.GCTdata.addRow();
};


GCT.prototype.addToLastRow = function ( nrCol, value, formatValue )
{
	var nrRow = this.GCTdata.getNumberOfRows()-1;
	
	this.modifyValue( nrRow, nrCol, value, formatValue );
};


GCT.prototype.modifyValue = function ( nrRow, nrCol, value, formatValue )
{	
	this.GCTdata.setCell( nrRow , nrCol, value, formatValue );
	
	if ( this.ColOption[ nrCol ] != '' )
			this.GCTdata.setProperty( nrRow , nrCol, 'style', this.ColOption[ nrCol ] );
};


GCT.prototype.getValue = function(nrRow, nrCol ){
	return this.GCTdata.getValue(nrRow, nrCol )
};

GCT.prototype.drawTable = function ()
{				
	google.setOnLoadCallback( this.__drawTable( this.__getDV(), this.TblOptions ) );		
};


GCT.prototype.addTblOption = function(key, value){
	this.TblOptions[ key ] = value;
};

GCT.prototype.addVisualOptionVC = function(key, value){
	this.vcssClassNames[ key ] = value;
};

GCT.prototype.delTblOption = function( key ){
	delete this.TblOptions[ key ];
};

GCT.prototype.delTblOptionVC = function(key, value){
	delete this.vcssClassNames[ key ];
};

GCT.prototype.sortByColumn = function( sortArray )
{	
	this.GCTdata.sort( sortArray );	
};


GCT.prototype.getFilteredRows = function( filterArray )
{	
	return this.GCTdata.getFilteredRows( filterArray );	
};

GCT.prototype.goToPage = function( nrPage, dt )
{	
	this.addTblOption( 'startPage', nrPage );
	
	if ( dt == 1)
		this.__drawTable( this.__getDV(), this.TblOptions );	
};

GCT.prototype.goToPosition = function( nrPos, dt )
{	
	var page = Math.floor( (nrPos-1)/this.TblOptions[ 'pageSize' ] );	
	this.goToPage( page, dt );
};



GCT.prototype.__getDV = function ()
{
	if ( typeof this.GCTview != 'undefined') {
		this.GCTdv = this.GCTview;		
	}
	else {		
		this.GCTdv = this.GCTdata;
	}	
	
	return this.GCTdv;
};


GCT.prototype.__drawTable = function( data, options ){	
	
	var table = new google.visualization.Table(document.getElementById(this.divId));
	table.draw( data, options );
};


GCT.prototype.__setView = function() 
{	
	this.GCTview = new google.visualization.DataView( this.GCTdata );
	return this.GCTview;	
};





 

