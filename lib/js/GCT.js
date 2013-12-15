/*
 * *********************************************
 * 	Service of Table - JG (triPPer)
 * 
 * 
 * Sipmple exapmle:
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
 * 				gct.addRow( ['<span  style="onmouseover="Tip(\'ala ma kota\')" onmouseout="UnTip()" >Kate</span>', {v: 12500, f: '$12,500'}, true] );
 * 
 *				t.addEmptyRow();
 *		    	t.addToLastRow( 0, 'Jacek');
 *		    	t.addToLastRow( 1, 999 );
 *		    	t.addToLastRow( 2, true);
 *		    	t.addToLastRow( 3, 'aaa');
 *
 *				t.drawTable();
 * 
 ************************************************ 
 */

 
google.load('visualization', '1', {packages:['table']});

function GCT( divId ){
	
	this.divId = divId;
	
	//css: GCT.css
	this.vcssClassNames = {
	    'headerRow': '',
	    'tableRow': 'GCTrow-color GCTtext-color',
	    'oddTableRow': 'GCToddRow-color GCTtext-color',
	    'selectedTableRow': 'GCTselectedRow-color',
	    'hoverTableRow': 'GCThoverText-color',
	    'headerCell': 'GCTheader-color GCTalign-left',
	    'tableCell': 'GCTborder-color',
	    'rowNumberCell': 'GCTrowNumber-color' }; 
	
	
	
	this.TblOptions = {		
	    'allowHtml': true,
	    'showRowNumber': false,
	    'width': 730,
	    'alternatingRowStyle': true,
	    'cssClassNames': this.vcssClassNames,
		
	    //Page settings                     
	    'page': 'enable',
	    'pageSize': 100,
	    'pagingSymbols': {prev: 'poprzednia - todo english', next: 'następna - todo english'},
		'pagingButtonsConfiguration': 'auto' 
		};


	//delete this.TblOptions.allowHtml;				
	this.ColOption = [];
	
	this.GCTdata = new google.visualization.DataTable();	
				
	this.addColumn; /*function( ColType, ColName, ColProperty)*/
					//Data Type: 'string, 'number', 'boolean', 'date' as Date object, timeofday as [hour, minute, second, millisenconds]
	
	this.addRow; /*function ( row )*/
	this.addEmptyRow; /* function () */
	this.addToLastRow; /* function ( col, value )*/
	
	this.drawTable; /*function ()*/
	
	//Table and Visualisation Option  
	this.addTblOption; /*function(key, value)*/	
	this.delTblOption; /*function(key)*/	
	this.addTblOptionVC; /*function(key, value)*/
	this.delTblOptionVC; /*function(key)*/
	
}
 
GCT.prototype.addColumn = function( ColType, ColName, ColProperty) {
	
	var nrCol = this.GCTdata.addColumn(ColType, ColName);	
	
	if ( ColProperty != '' )
		this.ColOption[ nrCol ] = ColProperty;
};

GCT.prototype.addRow = function ( row )
{
	var nrRow = this.GCTdata.addRow( row );
		
	for( var i = 0; i < this.ColOption.length; i++ )
	{
		if ( this.ColOption[ i ] != '' )
			this.GCTdata.setProperty( nrRow ,i, 'style', this.ColOption[ i ] );
	}
};

GCT.prototype.addEmptyRow = function ()
{
	this.GCTdata.addRow();
};


GCT.prototype.addToLastRow = function ( nrCol, value )
{
	var nrRow = this.GCTdata.getNumberOfRows();
	
	this.GCTdata.setCell( nrRow-1 , nrCol, value);
	
	if ( this.ColOption[ nrCol ] != '' )
			this.GCTdata.setProperty( nrRow-1 , nrCol, 'style', this.ColOption[ nrCol ] );
};


GCT.prototype.drawTable = function ()
{	
	google.setOnLoadCallback( this.__drawTable( this.GCTdata, this.TblOptions ) );
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


GCT.prototype.__drawTable = function( data, options ){	
	var table = new google.visualization.Table(document.getElementById(this.divId));
	table.draw( data, options );
};
