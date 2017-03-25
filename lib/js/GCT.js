/*
 * *********************************************
 *  Service of GoogleCharts - JG (triPPer)
 *
 *                              author: tripper1971@wp.pl
 *
 *
 * Simple exapmle:
 *   <script type='text/javascript' src='https://www.google.com/jsapi'></script>
 *   <script type="text/javascript" src="lib/js/GCT.js"></script>
 *   <script type="text/javascript" src="lib/js/GCT.lang.php"></script>
 *   <script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
 *
 *  <script type="text/javascript">
 *      GCTLoad( 'TableChart' );
 *  </script>
 *
 *
 * <script type="text/javascript">
 *
 *              var gct = new GCT()
 *
 *              gct.addColumn('string', 'Names', 'width:500px; text-align: left; color: green; ');
 *              gct.addColumn('number', 'Salary');
 *              gct.addColumn('boolean', 'Full Time Employee');
 *
                gct.addChartOption('showRowNumber', true );

 *              gct.addRow( ['Bob',   {v: 7000,  f: '$7,000'},  true ] );
 *              gct.addRow( ['Jim',   8888,  false ] );
 *              gct.addRow( ['<span  style="color:red" >Alice</span>', {v: 12500, f: '$12,500'}, true] );
 *              gct.addRow( ['<span  style="onmouseover="Tip(\'Hello !!! \')" onmouseout="UnTip()" >Kate</span>', {v: 12500, f: '$12,500'}, true] );
 *
 *              //other way of adding
 *              t.addEmptyRow();
 *              t.addToLastRow( 0, 'Jacek');
 *              t.addToLastRow( 1, 999 );
 *              t.addToLastRow( 2, true);
 *
 *
 *              t.sortByColumns([{column: 1, desc:true}]);
 *              //or
 *              t.sortByColumnsView([{column: 1, desc:true}]);
 *
 *
 *              t.hideColumns( [3] );
 *              t.showRows( [0,1], 0 );
 *
 *              t.drawChart();
 *
 *              nrRowsArray = t.getFilteredRows([{column: 1, value: 42}, {column: 0, minValue: 'Bob', maxValue: 'Jim'}])
 *
 * </script>
 ************************************************
 */

var ChartType;

function GCTLoad( ct, lang, disableLoad )
{
    ChartType = ct;

    if (disableLoad == 1)
        return;

    if ( ct == 'ChartTable')
        google.load('visualization', '1', {packages: ['table'], 'language': lang });
    else if ( ct == 'ChartMotion')
        google.load('visualization', '1', {packages: ['motionchart'], 'language': lang });
    else if ( ct == 'ChartLine')
        google.load('visualization', '1', {packages: ['corechart'], 'language': lang});
    else if ( ct == 'ChartBar')
        google.load('visualization', '1', {packages: ['corechart'], 'language': lang});

}




function GCT( divId ){


    this.divId = divId;

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

    this.TblOptions = {
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

    this.MotionOptions = {
        'width':600,
        'height': 400
    };

    this.LineOptions = {
        'legend': { position: 'right'},
        'curveType': 'function',
        'interpolateNulls': true,
        'fontSize': 11,
        'chartArea': {left:60,top:65,width:"75%",height:"75%"},
        'width': 780,
        'height': 550,
        'pointSize': 3,

        'hAxis': {  gridlines: { color: '#EEEEEE', count: 4} },

        'vAxis': {  gridlines: { color: '#EEEEEE'},
                    format: '#',
                    viewWindowMode: 'max',
                    title: GCT_lang.number_of_caches,
                    titleTextStyle: { bold: true, italic: true, fontSize: 12 }
                },


        'backgroundColor': { strokeWidth: 4, stroke: '#DBE6F1' },
        'tooltip': { showColorCode: true, textStyle: { fontSize: 11 } }
    };



    this.BarOptions = {
        'legend': { position: 'right'},
        'chartArea': {left:130,top:65,width:"70%",height:"75%"},
        'width': 780,
        'height': 550,
        'fontSize': 11,
        'isStacked': true,
        //bar: {groupWidth: 10},

        'hAxis': {  gridlines: { color: '#EEEEEE'},
                    format: '#',
                    viewWindowMode: 'max',
                    title: 'Number of caches',
                    titleTextStyle: { bold: true, italic: true, fontSize: 12 }
                },

        'vAxis': {  gridlines: { color: '#EEEEEE', count: 4} },

        'backgroundColor': { strokeWidth: 4, stroke: '#DBE6F1' },
        'tooltip': { showColorCode: true, textStyle: { fontSize: 11 } }

     };


    //delete this.TblOptions.allowHtml;
    this.ColOption = [];

    this.GCTdata = new google.visualization.DataTable();
    this.GCTview;
    this.GCTdv;

    this.chart;


    this.GCTSelectColor = ' color:#AA0000 '; /*Automat Select*/


    this.addColumn; /*function( ColType, ColName, ColProperty)*/
                    //Data Type: 'string, 'number', 'boolean', 'date' as Date object, timeofday as [hour, minute, second, millisenconds]

    //Add Data, Get Data
    this.addRow; /*function ( row )*/
    this.addEmptyRow; /* function () */
    this.addToLastRow; /* function ( col, value )*/
    this.getValueFromLastRow; /* function( nrCol ) */
    this.getValue; /* function( nrRow, nrCol ) */
    this.modifyValue; /* function ( nrRow, nrCol, value, formatValue ) */
    this.getNumberOfRows; /* function () */
    this.removeAllRows;

    //View function - !!! Use they after adding all data !!!
    this.showRows; /* function(rowArray, dt) */
    this.hideColumns; /* function( rowColumns ) */
    this.sortByColumnsView; /*function( sortArray )*/
    this.getViewColumns;

    this.drawChart; /*function ()*/

    //Chart Option
    this.addChartOption; /*function(key, value)*/
    this.delChartOption; /*function(key)*/
    this.getChartOption; /*function() return: ChartOption*/

    this.addTblOptionVC; /*function(key, value)*/
    this.delTblOptionVC; /*function(key)*/

    //Tools
    this.sortByColumns; /*function( sortArray )*/
    this.getFilteredRows; /* function( filterArray ) return: nrRowsArray */
    this.setAsSelected; /* function( nrRow ) */
    this.getSelection; /* function() return: nrRowsArray */
    this.setSelection; /* function() return: nrRowsArray */

    //Page Navigation - TableChart
    this.goToPage; /* function( nrPage, dt ) */
    this.goToPosition; /* function( nrPos, dt ) */

    //Event
    this.addSelectEvent; /* function( eventFunction ) */
    this.addPageEvent; /* function( eventFunction ) */
}


GCT.prototype.getSelection = function(){
    return this.chart.getSelection();
};

//setSelection([{'row': 4}])
GCT.prototype.setSelection = function( nrRow ){
    this.chart.setSelection( nrRow );
};


GCT.prototype.setAsSelected = function( nrRow )
{
    var style;


    for (var i = 0; i < this.GCTdata.getNumberOfColumns(); i++)
    {
        if ( typeof this.ColOption[ i ] != 'undefined' )
            style = this.ColOption[ i ] + this.GCTSelectColor;
        else
            style = this.GCTSelectColor;

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

GCT.prototype.getViewColumns = function()
{
    if ( typeof this.GCTview == 'undefined')
        this.__setView();

    return this.GCTview.getViewColumns();
};


GCT.prototype.addColumn = function( ColType, ColName, ColProperty) {

    var nrCol = this.GCTdata.addColumn(ColType, ColName);

    if ( ColProperty != '' )
        this.ColOption[ nrCol ] = ColProperty;


};

GCT.prototype.removeAllRows = function (){
    this.GCTdata.removeRows(0, this.GCTdata.getNumberOfRows() );    
}




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

GCT.prototype.getNumberOfRows = function ()
{
    return this.GCTdata.getNumberOfRows()-1;
}

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


GCT.prototype.getValueFromLastRow = function( nrCol )
{
    var nrRow = this.GCTdata.getNumberOfRows()-1;
    return this.getValue(nrRow, nrCol );
};

GCT.prototype.getValue = function(nrRow, nrCol ){
    return this.GCTdata.getValue(nrRow, nrCol );
};


GCT.prototype.drawChart = function ( directDraw )
{
    if ( directDraw == 1 )
        this.__getFunDrawChart( this.__getDV(), this.__getChartOptions() );
    else
        google.setOnLoadCallback( this.__getFunDrawChart( this.__getDV(), this.__getChartOptions() ) );

};

GCT.prototype.getChartOption = function(){
    return this.__getChartOptions();
};


GCT.prototype.addChartOption = function(key, value){
    this.__getChartOptions()[ key ] = value;
};

GCT.prototype.addVisualOptionVC = function(key, value){
    this.vcssClassNames[ key ] = value;
};

GCT.prototype.delChartOption = function( key ){
    delete this.__getChartOptions[ key ];
};

GCT.prototype.delTblOptionVC = function(key, value){
    delete this.vcssClassNames[ key ];
};

GCT.prototype.sortByColumnsView = function( sortArray )
{
    if ( typeof this.GCTview == 'undefined')
        this.__setView();

    //this.GCTview.setRows( data.getSortedRows( sortArray );
    this.GCTview.setRows(this.GCTdata.getSortedRows(sortArray));
};


GCT.prototype.sortByColumns = function( sortArray )
{
    this.GCTdata.sort( sortArray );
};


GCT.prototype.getFilteredRows = function( filterArray )
{
    return this.GCTdata.getFilteredRows( filterArray );
};

GCT.prototype.goToPage = function( nrPage, dt )
{
    this.addChartOption( 'startPage', nrPage );

    if ( dt == 1)
        this.__drawTable( this.__getDV(), this.TblOptions );
};

GCT.prototype.goToPosition = function( nrPos, dt )
{
    var page = Math.floor( (nrPos)/this.TblOptions[ 'pageSize' ] );
    this.goToPage( page, dt );
};


GCT.prototype.addSelectEvent = function( eventFunction ){
    google.visualization.events.addListener(this.chart, 'select', eventFunction );
};

GCT.prototype.addPageEvent = function( eventFunction ){
    google.visualization.events.addListener(this.chart, 'page', eventFunction );
};

GCT.prototype.__getFunDrawChart = function ( dv, co )
{
    if (ChartType == 'ChartTable')
        return this.__drawTable( dv, co );
    else if ( ChartType == 'ChartMotion')
        return this.__drawMotion( dv, co );
    else if ( ChartType == 'ChartLine')
        return this.__drawLine( dv, co );
    else if ( ChartType == 'ChartBar')
        return this.__drawBar( dv, co );
};


GCT.prototype.__getChartOptions = function ()
{
    if (ChartType == 'ChartTable')
        return this.TblOptions;
    else if ( ChartType == 'ChartMotion')
        return this.MotionOptions;
    else if ( ChartType == 'ChartLine')
        return this.LineOptions;
    else if ( ChartType == 'ChartBar')
        return this.BarOptions;
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

    if ( typeof this.chart == 'undefined' )
        this.chart = new google.visualization.Table(document.getElementById(this.divId));

    this.chart.draw( data, options );
};


GCT.prototype.__drawMotion = function(data, options){
    if ( typeof this.chart == 'undefined' )
        this.chart = new google.visualization.MotionChart(document.getElementById(this.divId));

    this.chart.draw(data, options);
};


GCT.prototype.__drawLine = function(data, options){
    if ( typeof this.chart == 'undefined' )
        this.chart = new google.visualization.LineChart(document.getElementById(this.divId));

    this.chart.draw(data, options);
};


GCT.prototype.__drawBar = function(data, options){
    if ( typeof this.chart == 'undefined' )
        this.chart = new google.visualization.BarChart(document.getElementById(this.divId));

    this.chart.draw(data, options);
};


GCT.prototype.__setView = function()
{
    this.GCTview = new google.visualization.DataView( this.GCTdata );
    return this.GCTview;
};








