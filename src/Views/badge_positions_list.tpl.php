<?php
use src\Utils\I18n\I18n;
?>

<br>
{head}

<br>
<div>
<table class="Badge-div-table Tables">
    <tr style="vertical-align: top;">
    <td class="Badge-div-oneTable"><div id='idGCTPosition' align = "left"></div></td>
    </tr>
</table>
</div>

<br>
<br>


<script>
<?php echo "gctLoadTable( '" . I18n::getCurrentLang() . "' );"?>

var gct = new GCT();
gctSetCallback( positionsCB );

function positionsCB(){

    gct.setDataTable();

    gct.addColumn('string', '' ); //
    gct.addColumn('string',  '<?php echo tr("geocache") ?>', 'font-size: 12px; ' ); //1
    gct.addColumn('string', '<?php echo tr("owner") ?>', 'font-size: 12px; ' ); //2
    gct.addColumn('string', '<?php echo tr("merit_badge_gain_date") ?>', 'font-size: 12px; ' ); //3

    gct.addOption('sortColumn', 3 ); //Date
    gct.addOption('sortAscending', false );
    gct.addOption('pageSize', 30);

    {content}

    gct.drawTable( 'idGCTPosition' );
}
</script>

<br>
