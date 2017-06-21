  <script type="text/javascript">
    function gctUShowUsers( level ){
        gctU.removeAllRows();
        {contentUsr}
        gctU.drawChart();
    }


    function GCTEventBadgeLevel( event )
    {
        var recArray =  gct.getSelection();
        var item = recArray[0];
        var val = gct.getValue( item.row, 0 );

        gctU.removeAllRows();
        gctUShowUsers( val );
    }

  </script>

{head}


<div>
<table class="Badge-div-table Info">
    <tr><td>{desc_cont}</td></tr>
</table>
</div>

<br>
<div>
<table class="Badge-div-table Tables">
    <tr style="vertical-align: top;">
    <td class="Badge-div-oneTable"><div id='idGCTLevel' align = "left"></div></td>
    <td class="Badge-div-spaceBetweenTables"></td>
    <td class="Badge-div-oneTable"><div id='idGCTUser' align = "left"> </div></td>
    </tr>
</table>
</div>

<br>

{showPositions}

<br>

<div>
<table class="Badge-div-table Info" style="text-align:center;">
    <tr><td>{who_prepared}</td></tr>
</table>
</div>


<script type='text/javascript'>
<?php echo "GCTLoad( 'ChartTable', '" . $lang . "' );"?>
</script>

<script type='text/javascript'>
var gct = new GCT('idGCTLevel');
gct.addColumn('string', ''); //0
gct.addColumn('string',  '', 'font-size: 12px; ' ); //1
gct.addColumn('string',  '<?php echo tr("merit_badge_level") ?>', 'font-size: 12px; ' ); //2
gct.addColumn('string', '<?php echo tr("merit_badge_number_threshold"); ?>', 'width: 90px; font-size: 12px; text-align:left;' ); //3
gct.addColumn('string', '<?php echo tr("merit_badge_gain_count") ?>', 'width: 50px; font-size: 12px; text-align:right;' ); //4
gct.addColumn('string', '<?php echo tr("merit_badge_gain_last_date") ?>', 'width: 100px;font-size: 12px; ' ); //5

gct.hideColumns( [0] );

gct.addChartOption('width', 410);
gct.addChartOption('page', 'disable' );
gct.addChartOption('sort', 'disable' );
gct.addChartOption('pageSize', 0 );

{contentLvl}

gct.setAsSelected({userLevel});
gct.drawChart();
gct.setSelection([{'row': {userLevel}}]);
gct.addSelectEvent( GCTEventBadgeLevel );


var gctU = new GCT('idGCTUser');
gctU.addColumn('string', "<?php echo tr('user') ?>", 'font-size: 12px;'); //0
gctU.addColumn('number', "<?php echo tr('merit_badge_number') ?>", 'font-size: 12px;text-align:right;'); //1
gctU.addColumn('string', '<?php echo tr("merit_badge_gain_date") ?>', 'font-size: 12px;' ); //2

gctU.addChartOption('width', 300);
gctU.addChartOption('pageSize', 20 );

gctUShowUsers({userLevel});

</script>
<br>