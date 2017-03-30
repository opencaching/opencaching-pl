<link href="tpl/stdstyle/js/jquery.1.10.3/css/myCupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script type="text/javascript" src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-ui-1.10.3.custom.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/Badge.css" />
<link rel="stylesheet" href="tpl/stdstyle/js/PieProgress/dist/css/asPieProgress.css">
<script type="text/javascript" src="tpl/stdstyle/js/PieProgress/js/jquery.js"></script>
<script type="text/javascript" src="tpl/stdstyle/js/PieProgress/dist/jquery-asPieProgress.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCTStats.css" />
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript' src="lib/js/GCT.js"></script>
<script type='text/javascript' src="lib/js/GCTStats.js"></script>
<script type='text/javascript' src="lib/js/wz_tooltip.js"></script>

  <script type="text/javascript">
    jQuery(function($) {
      $('.Badge-pie-progress').asPieProgress({
        namespace: 'pie_progress'
      });
  
      $('.pie_progress').asPieProgress('start');
    
    });

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

<div class="content2-pagetitle">
<img src="tpl/stdstyle/images/blue/merit_badge.png" class="icon32" alt="" title="" align="middle" />&nbsp;
{{merit_badge}}
</div>

<br>
<br>
<div >

    <div class="Badge-pie-progress" role="progressbar" data-goal="{progresbar_curr_val}" data-trackcolor="#d9d9d9" data-barcolor="{progresbar_color}" data-barsize="{progresbar_size}" aria-valuemin="0" aria-valuemax="{progresbar_next_val}">
        <div class="pie_progress__content"><img src="{picture}" /><br></div>
    </div>
    
    <span class="Badge-name">{badge_name}</span><br>
    <span class="Badge-short_desc">{badge_short_desc}</span>

<p class="Badge-other">
<br><br>
{{merit_badge_level_name}}: <b>{userLevelName}</b><br>
{{merit_badge_number}}: <b>{userCurrValue}</b><br>
{{merit_badge_next_level_threshold}}: <b>{userThreshold}</b><br>
<br><br>
</p>

</div>


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
</script>

<script type='text/javascript'>
{contentLvl} 

gct.setAsSelected({userLevel});
gct.drawChart();
gct.setSelection([{'row': {userLevel}}]);
gct.addSelectEvent( GCTEventBadgeLevel );

</script>

<script type='text/javascript'>
    var gctU = new GCT('idGCTUser');
    gctU.addColumn('string', "<?php echo tr('user') ?>", 'font-size: 12px;'); //0
    gctU.addColumn('number', "<?php echo tr('merit_badge_number') ?>", 'font-size: 12px;text-align:right;'); //1
    gctU.addColumn('string', '<?php echo tr("merit_badge_gain_date") ?>', 'font-size: 12px;' ); //2
    
    gctU.addChartOption('width', 300);
    gctU.addChartOption('pageSize', 20 );
    
    gctUShowUsers({userLevel});
</script>
<br>


