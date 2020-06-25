/*
 **********************************************
   Service of GoogleCharts  >> Table - JG (triPPer)

                               author: tripper1971@wp.pl


  Simple exapmle:
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="js/GCT.js"></script>
    <script src="js/GCT.lang.php"></script>
    <script src="js/wz_tooltip.js"></script>

    <script>

    gctLoadTable( 'pl' ); ->> load library

    var gct = new GCT();
    gctSetCallback( nameOfCallbackFunction  ); ->> set callback function


    function nameOfCallbackFunction(){

        gct.setDataTable();

        gct.addColumn(gctData, 'string', 'Id'); //0
        gct.addColumn(gctData, 'string',  'Name', 'font-size: 12px; ' ); //1

        gct.hideColumns( [0] );

        gct.addOption('width', 410);
        gct.addOption('page', 'disable' );
        gct.addOption('sort', 'disable' );
        gct.addOption('pageSize', 0 );


        gct.addEmptyRow();
        gct.addToLastRow( gctData, 0, '1111');
        gct.addToLastRow( gctData, 1, 'Ala' );

        gct.addEmptyRow();
        gct.addToLastRow gctData, 0, '1112');
        gct.addToLastRow( gctData, 1, 'Ula' );

        gct.drawTable( 'table_div' );
    }

  </script>


    <body>
        <div id="table_div"></div>
    </body>


 **********************************************/

function gctLoadTable( lang, disableLoad )
{

    if (disableLoad == 1)
        return;

    google.charts.load('current', {packages: ['table'], 'language': lang });
}


function gctSetCallback( callbackName ){

	google.charts.setOnLoadCallback( callbackName );
};



////////////////////////////////////////////////////////////////



function GCT(){
//css: GCT.css
this.vcssClassNames = {
    'headerRow': 'GCT-background-color-white6 GCT-color-black11 GCTalign-left GCT-font-bold ',
    'tableRow': 'GCT-background-color-white GCT-color-black ',
    'oddTableRow': 'GCT-background-color-white6 GCT-color-black',
    'selectedTableRow': 'GCT-background-color-greyDE',
    'hoverTableRow': 'GCT-color-darkred',
    'headerCell': 'GCT-border-color-greyD9',
    'tableCell': 'GCT-border-color-greyD9',
    'rowNumberCell': 'GCT-color-none' };

this.tblOptions = {
    'allowHtml': true,
    'showRowNumber': false,
    'width': 730,
    'alternatingRowStyle': true,
    'sort': 'enable',
    'sortColumn': -1,
    'sortAscending': false,
    'cssClassNames': this.vcssClassNames,

    //Page settings
    'page': 'enable',
    'pageSize': 100,
    'pagingSymbols': {prev: GCT_lang.prev, next: GCT_lang.next},
    'startPage': 0
    };


this.colOption = [];
this.selectColor = 'color:#AA0000'; /*Automat Select*/

this.data; // gctSetDataTable();google.visualization.DataTable  <<- it should be the first line in callback function
this.table; //__setTable();     google.visualization.DataView   <<- it is called automatically
this.view; // __gctSetView();   google.visualization.Table      <<- it is called automatically

}


GCT.prototype.setDataTable = function(){
    this.data = new google.visualization.DataTable();
}


GCT.prototype.drawTable = function( divId ){

        if ( typeof this.table == 'undefined' ){
            this.__setTable( divId );
         }

        this.table.draw( this.__getViewOrData(), this.tblOptions);
};

GCT.prototype.__setTable = function(divId){
    this.table = new google.visualization.Table( document.getElementById( divId ) );
}



GCT.prototype.addColumn = function( ColType, ColName, ColProperty) {

    var nrCol = this.data.addColumn(ColType, ColName);


    if ( ColProperty != '' )
        this.colOption[ nrCol ] = ColProperty;

}

////////////////////////////////////////////////////////////////
// Values

GCT.prototype.addEmptyRow = function(){
    this.data.addRow();
}

GCT.prototype.addToLastRow  = function( nrCol, value, formatValue )
{
    var nrRow = this.data.getNumberOfRows()-1;

    this.__modifyValue( nrRow, nrCol, value, formatValue );
}

GCT.prototype.removeAllRows = function (){
    this.data.removeRows(0, this.data.getNumberOfRows() );
}

GCT.prototype.getValue = function( nrRow, nrCol ){
    return this.data.getValue( nrRow, nrCol );
}

GCT.prototype.__modifyValue = function( nrRow, nrCol, value, formatValue )
{
    this.data.setCell( nrRow , nrCol, value, formatValue );

    if ( this.colOption[ nrCol ] != '' )
            this.data.setProperty( nrRow , nrCol, 'style', this.colOption[ nrCol ] );
}


// Values
////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////
// View

GCT.prototype.hideColumns = function( colArray ){

    if ( this.__viewUndefined() ){
       this.__setView();
    }

    this.view.hideColumns( colArray );
};


GCT.prototype.__setView = function(){

    this.view = new google.visualization.DataView( this.data );
};


GCT.prototype.__viewUndefined = function(){
    return ( typeof this.view == 'undefined');
}

GCT.prototype.__getViewOrData = function(){

    if ( !this.__viewUndefined() ) {
        return this.view;
    }

    return this.data;
};

// View
////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////
// Operation

GCT.prototype.addOption = function(key, value){
    this.tblOptions[ key ] = value;
};

GCT.prototype.addVisualOptionVC = function(key, value){
    this.vcssClassNames[ key ] = value;
};

GCT.prototype.setColorForSelected = function( nrRow )
{
    var style;

    for (var i = 0; i < this.data.getNumberOfColumns(); i++)
    {
        if ( typeof this.colOption[ i ] != 'undefined' )
            style = this.colOption[ i ] + this.selectColor;
        else
            style = this.selectColor;

        this.data.setProperty( nrRow, i, 'style', style );
    }

};


GCT.prototype.setSelection = function( nrRow ){
    this.table.setSelection( nrRow );
}

GCT.prototype.getSelection = function(){
    return this.table.getSelection();
}

GCT.prototype.addSelectEvent = function( eventFunction ){
    google.visualization.events.addListener(this.table, 'select', eventFunction );
}

GCT.prototype.addPageEvent = function( eventFunction ){
    google.visualization.events.addListener(this.table, 'page', eventFunction );
};

// Operation
////////////////////////////////////////////////////////////////

