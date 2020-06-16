/*
 * *********************************************
 *  Service of GoogleCharts - JG (triPPer)
 *
 *                              author: tripper1971@wp.pl
 *
 *
 * Simple exapmle:
 *   <script src='https://www.google.com/jsapi'></script>
 *   <script src="js/GCT.js"></script>
 *   <script src="js/GCT.lang.php"></script>
 *   <script src="js/wz_tooltip.js"></script>
 *
 *  <script>
 *      GCTLoad( 'TableChart' );
 *  </script>
 *
 *
 * <script>
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

    if ( ct == 'ChartTable'){
        //google.load('visualization', '1', {packages: ['table'], 'language': lang });
        google.charts.load('current', {packages: ['table'], 'language': lang });
    }
    else if ( ct == 'ChartMotion'){
        //google.load('visualization', '1', {packages: ['motionchart'], 'language': lang });
        google.charts.load('current', {packages: ['motionchart'], 'language': lang });
    }
    else if ( ct == 'ChartLine'){
        //google.load('visualization', '1', {packages: ['corechart'], 'language': lang});
        google.charts.load('current', {packages: ['corechart'], 'language': lang});
    }
    else if ( ct == 'ChartBar'){
        //google.load('visualization', '1', {packages: ['corechart'], 'language': lang});
        google.charts.load('current', {packages: ['corechart'], 'language': lang});
    }

}




function GCT(divId, disableLoad) {


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

    this.GCTdata = {
        cols: [],
        rows: []
    };
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

    this.onAPILoaded; /* function() */

    this.setViewCalled = false;
    this.drawChartCalled = false;
    this.columnsToHide = [];
    this.listeners = [];

    if (typeof disableLoad == 'undefined' || !disableLoad) {
        var self = this;
        google.setOnLoadCallback(function() {
            self.onAPILoaded();
        });
    }
}

GCT.prototype.onAPILoaded = function() {
    this.GCTdata = new google.visualization.DataTable(this.GCTdata);
    this.__setView = function() {
        this.GCTview = new google.visualization.DataView( this.GCTdata );
        return this.GCTview;
    };
    this.addColumn = function(colType, colName, colProperty) {
        var nrCol = this.GCTdata.addColumn(colType, colName);
        if (colProperty != '') {
            this.ColOption[nrCol] = colProperty;
        }
    };
    this.removeAllRows = function() {
        this.GCTdata.removeRows(0, this.GCTdata.getNumberOfRows());
    };
    this.__addRow = function(row) {
        var nrRow = this.GCTdata.addRow(row);
        for (var i = 0; i < this.ColOption.length; i++) {
            if (this.ColOption[i] != '') {
                this.GCTdata.setProperty(nrRow ,i, 'style', this.ColOption[i]);
            }
        }
    };
    this.modifyValue = function(nrRow, nrCol, value, formatValue) {
        this.GCTdata.setCell(nrRow , nrCol, value, formatValue);

        if (this.ColOption[ nrCol ] != '') {
            this.GCTdata.setProperty(
                nrRow, nrCol, 'style', this.ColOption[nrCol]
            );
        }
    };
    this.addSelectEvent = function(eventFunction) {
        google.visualization.events.addListener(
            this.chart, 'select', eventFunction
        );
    };
    this.addPageEvent = function(eventFunction) {
        google.visualization.events.addListener(
            this.chart, 'page', eventFunction
        );
    };
    if (this.setViewCalled) {
        this.__setView();
        this.setViewCalled = false;
    }
    if (this.columnsToHide.length > 0) {
        this.hideColumns(this.columnsToHide);
        this.columnsToHide = [];
    }
    if (this.drawChartCalled) {
        this.__getFunDrawChart(this.__getDV(), this.__getChartOptions());
        this.setDrawChartCalled = false;
    }
    for (var i = 0 ; i < this.listeners.length; i++) {
        google.visualization.events.addListener(
            this.chart, this.listeners[i].type, this.listeners[i].listener
        );
    }
    this.listeners = [];
};

GCT.prototype.getSelection = function() {
    var result = null;
    if (typeof this.chart != 'undefined') {
        result = this.chart.getSelection();
    }
    return result;
};

//setSelection([{'row': 4}])
GCT.prototype.setSelection = function(nrRow) {
    if (typeof this.chart != 'undefined') {
        this.chart.setSelection(nrRow);
    }
};

GCT.prototype.setAsSelected = function(nrRow) {
    if (typeof this.GCTdata.getNumberOfColumns == 'function') {
        for (var i = 0; i < this.GCTdata.getNumberOfColumns(); i++) {
            var style;
            if (typeof this.ColOption[i] != 'undefined') {
                style = this.ColOption[i] + this.GCTSelectColor;
            } else {
                style = this.GCTSelectColor;
            }
            this.GCTdata.setProperty(nrRow, i, 'style', style);
        }
    }
};

GCT.prototype.showRows = function(rowArray, dt) {
    if (typeof this.GCTview != 'undefined' || this.__setView()) {
        this.GCTview.setRows(rowArray);
        if (dt == 1) {
            this.__drawTable(this.GCTview, this.TblOptions);
        }
    } else {
        this.setViewCalled = true;
    }
};

GCT.prototype.hideColumns = function(colArray) {
    if (typeof this.GCTview != 'undefined' || this.__setView()) {
        this.GCTview.hideColumns(colArray);
    } else {
        this.setViewCalled = true;
        this.columnsToHide = this.columnsToHide.concat(colArray);
    }
};

GCT.prototype.getViewColumns = function() {
    var result = null;
    if (typeof this.GCTview != 'undefined' || this.__setView()) {
        result = this.GCTview.getViewColumns();
    } else {
        this.setViewCalled = true;
    }
};

GCT.prototype.addColumn = function(colType, colName, colProperty) {
    this.GCTdata.cols.push({
        type: colType,
        label: colName
    });

    if (colProperty != '') {
        this.ColOption[this.GCTdata.length - 1] = colProperty;
    }
};

GCT.prototype.removeAllRows = function () {
    this.GCTData.rows = [];
}

GCT.prototype.__addRow = function(row) {
    for (var i = 0; i < row.length; i++) {
        if (typeof row[i].v == 'undefined') {
            row[i] = {
                v: row[i]
            };
        }
        if (i < this.ColOption.length && this.ColOption[i] != '') {
            row[i].p = {
                style: this.ColOption[i]
            };
        }
    }
    this.GCTdata.rows.push({
        c: row
    });
}

GCT.prototype.addRow = function(row) {
    if (!isNaN(this.GCTview)) {
        alert("You try to add records after settings view !!!");
        return;
    }

    this.__addRow(row);
};

GCT.prototype.addEmptyRow = function () {
    if (!isNaN(this.GCTview)) {
        alert("You try to add records after settings view !!!");
        return;
    }

    this.__addRow([]);
};

GCT.prototype.getNumberOfRows = function () {
    return (
        typeof this.GCTdata.getNumberOfRows == 'function'
        ? this.GCTdata.getNumberOfRows() - 1
        : this.GCTdata.rows.length - 1
    );
}

GCT.prototype.addToLastRow = function(nrCol, value, formatValue) {
    this.modifyValue(this.getNumberOfRows(), nrCol, value, formatValue);
};

GCT.prototype.modifyValue = function(nrRow, nrCol, value, formatValue) {
    if (typeof this.GCTdata.rows[nrRow] == 'undefined') {
        this.GCTdata.rows[nrRow] = {
            c: []
        };
    }
    if (typeof this.GCTdata.rows[nrRow].c[nrCol] == 'undefined') {
        this.GCTdata.rows[nrRow].c[nrCol] = {};
    }
    if (typeof this.GCTdata.rows[nrRow].c[nrCol].v == 'undefined') {
        this.GCTdata.rows[nrRow].c[nrCol] = {
            v: ''
        };
    }
    this.GCTdata.rows[nrRow].c[nrCol].v = value;
    if (typeof formatValue != 'undefined') {
        this.GCTdata.rows[nrRow].c[nrCol].f = formatValue;
    }
    if (this.ColOption[nrCol] != '' ) {
        this.GCTdata.rows[nrRow].c[nrCol].p = {
            style: this.ColOption[nrCol]
        };
    }
};

GCT.prototype.getValueFromLastRow = function(nrCol) {
    return this.getValue(this.getNumberOfRows(), nrCol);
};

GCT.prototype.getValue = function(nrRow, nrCol) {
    var result = null;
    if (typeof this.GCTdata.getValue == 'function') {
        result = this.GCTdata.getValue(nrRow, nrCol);
    } else {
        result =
            typeof this.GCTdata.rows[nrRow].c[nrCol].v != 'undefined'
            ? this.GCTdata.rows[nrRow].c[nrCol].v
            : null;
    }
    return result;
};


GCT.prototype.drawChart = function(directDraw) {
    if (typeof directDraw != 'undefined' && directDraw) {
        this.__getFunDrawChart(this.__getDV(), this.__getChartOptions());
    } else {
        this.drawChartCalled = true;
    }
};

GCT.prototype.getChartOption = function() {
    return this.__getChartOptions();
};


GCT.prototype.addChartOption = function(key, value) {
    this.__getChartOptions()[key] = value;
};

GCT.prototype.addVisualOptionVC = function(key, value) {
    this.vcssClassNames[key] = value;
};

GCT.prototype.delChartOption = function(key) {
    delete this.__getChartOptions[key];
};

GCT.prototype.delTblOptionVC = function(key, value) {
    delete this.vcssClassNames[key];
};

GCT.prototype.__getSortedRows = function(sortColumns, indices) {
    var result = [];
    var sortArray = [];
    if (typeof sortColumns != 'undefined') {
        if (typeof sortColumns == 'number') {
            sortArray.push({
                column: sortColumns
            });
        } else if (Array.isArray(sortColumns)) {
            for (var i = 0; i < sortColumns.length; i++) {
                if (typeof sortColumns[i] == 'number') {
                    sortArray.push({
                        column: sortColumns[i]
                    });
                } else {
                    sortArray.push(sortColumns[i]);
                }
            }
        }
        for (var i = 0; i < this.GCTdata.rows.length; i++) {
            data[i] = [this.GCTdata.rows[i], i];
        }
        data.sort(function(a, b) {
            var cmpResult = 0;
            for (var i = 0; i < sortArray.length && cmpResult == 0; i++) {
                var vA = a[0].c[sortArray[i].column].v;
                var vB = b[0].c[sortArray[i].column].v;
                cmpResult = (vA < vB) ? -1 : (vA === vB ? 0 : 1);
                if (cmpResult != 0
                    && typeof sortArray[i].desc != 'undefined'
                    && typeof sortArray[i].desc
                ) {
                    cmpResult = -cmpResult;
                }
            }
            return cmpResult;
        });
        var resultTypeInd = (typeof indices != 'undefined' && indices) ? 1 : 0;
        for (var i = 0; i < data.length; i++) {
            result.push(data[i][resultTypeDesc]);
        }
    }
    return result;
};

GCT.prototype.sortByColumnsView = function(sortArray) {
    if (typeof this.GCTview != 'undefined' || this.__setView()) {
        // probably unnecessary check below, GCTdata should be from google by now
        this.GCTview.setRows(
            typeof this.GCTdata.getSortedRows == 'function'
            ? this.GCTdata.getSortedRows(sortArray)
            : this.__getSortedRows(sortArray, true)
        );
    } else {
        this.setViewCalled = true;
    }
};

GCT.prototype.sortByColumns = function(sortArray) {
    if (typeof this.GCTdata.getNumberOfRows == 'function') {
        this.GCTdata.sort(sortArray);
    } else {
        this.GCTdata.rows = this.__getSortedRows(sortArray);
    }
};

/* == TODO for offline == */
GCT.prototype.getFilteredRows = function(filterArray) {
    var result = null;
    if (typeof this.GCTdata.getFilteredRows == 'function') {
        result = this.GCTdata.getFilteredRows(filterArray);
    }
    return result;
};

GCT.prototype.goToPage = function(nrPage, dt) {
    this.addChartOption('startPage', nrPage);

    if (dt == 1) {
        this.__drawTable(this.__getDV(), this.TblOptions);
    }
};

GCT.prototype.goToPosition = function(nrPos, dt) {
    var page = Math.floor((nrPos)/this.TblOptions['pageSize']);
    this.goToPage(page, dt);
};

GCT.prototype.addSelectEvent = function(eventFunction) {
    this.listeners.push({
        type: 'select',
        listener: eventFunction
    });
};

GCT.prototype.addPageEvent = function(eventFunction) {
    this.listeners.push({
        type: 'page',
        listener: eventFunction
    });
};


GCT.prototype.__getFunDrawChart = function(dv, co) {
    var result = null;
    if (ChartType == 'ChartTable') {
        result = this.__drawTable(dv, co);
    } else if (ChartType == 'ChartMotion') {
        result = this.__drawMotion(dv, co);
    } else if (ChartType == 'ChartLine') {
        result = this.__drawLine(dv, co);
    } else if (ChartType == 'ChartBar') {
        result = this.__drawBar(dv, co);
    }
    return result;
};

GCT.prototype.__getChartOptions = function () {
    var result = null;
    if (ChartType == 'ChartTable') {
        result = this.TblOptions;
    } else if (ChartType == 'ChartMotion') {
        result = this.MotionOptions;
    } else if (ChartType == 'ChartLine') {
        result = this.LineOptions;
    } else if (ChartType == 'ChartBar') {
        result = this.BarOptions;
    }
    return result;
};

GCT.prototype.__getDV = function () {
    if ( typeof this.GCTview != 'undefined') {
        this.GCTdv = this.GCTview;
    }
    else {
        this.GCTdv = this.GCTdata;
    }

    return this.GCTdv;
};

GCT.prototype.__drawTable = function(data, options) {
    if (typeof this.chart == 'undefined') {
        this.chart = new google.visualization.Table(
            document.getElementById(this.divId)
        );
    }
    this.chart.draw(data, options);
};

GCT.prototype.__drawMotion = function(data, options) {
    if (typeof this.chart == 'undefined') {
        this.chart = new google.visualization.MotionChart(
            document.getElementById(this.divId)
        );
    }
    this.chart.draw(data, options);
};

GCT.prototype.__drawLine = function(data, options) {
    if (typeof this.chart == 'undefined' ) {
        this.chart = new google.visualization.LineChart(
            document.getElementById(this.divId)
        );
    }
    this.chart.draw(data, options);
};

GCT.prototype.__drawBar = function(data, options) {
    if (typeof this.chart == 'undefined') {
        this.chart = new google.visualization.BarChart(
            document.getElementById(this.divId)
        );
    }
    this.chart.draw(data, options);
};

GCT.prototype.__setView = function() {
    return null;
};
