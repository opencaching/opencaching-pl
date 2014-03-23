<table width="100%" >
<tr>
    <td>
<!--    <table  class = "GCT-div-table" >
            <tr>
                <td width = "150px" align = "center" >
                <form name="ChartHelp" style="display:inline;" action="" onsubmit="return false;" >
                    <button name="bChartHelp" id="bChartHelp"  />Help</button>
                </form>
                </td>
            </tr>
        </table>-->

    </td>

    <td>
        <form name="Details" style="display:inline;" action="" onsubmit="return false;" >
            <table class="GCT-div-table" >
                <tr>
                    <td rowspan = 2>
                        <?php echo tr('Selected')?>:&nbsp&nbsp<input type="text" name="SelectedUser" id="SelectedUser" class="GCT-div-readOnly" style="width:20px" readonly >&nbsp&nbsp<?php echo tr('positions')?>
                    </td>

                    <td>
                        &nbsp&nbsp&nbsp&nbsp<button name="bLineChart" id="bLineChart"  /><?php echo tr('LineChart')?></button>
                    </td>
                    <td rowspan = 2>
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span><button name="bChartHelp" id="bChartHelp"  /><?php echo tr('Help')?></button>
                    </td>

                </tr>
                <tr>

                    <td>
                        <br>
                        &nbsp&nbsp&nbsp&nbsp<button name="bBarChart" id="bBarChart"   /><?php echo tr('BarChart')?></button>
                    </td>
                </tr>
            </table>
        </form>
    </td>


<!-- Begin of Position   width = "200px" align = "center"-->
    <td align="right">
        <table  class = "GCT-div-table" >
            <tr>
                <td >
                <form name="Position" style="display:inline;" action="" onsubmit="return false;" >
                    <input type="hidden" value="0" name="RealPosOfTable" >
                    <?php echo tr('my_position')?>:&nbsp&nbsp<input type="text" name="Ranking" id="Ranking" class="GCT-div-readOnly" style="width:70px" readonly >
                    &nbsp&nbsp&nbsp&nbsp<button name="bGo" onClick ="GCTStatsGotoPosition(document.Position.RealPosOfTable.value)"  /><?php echo tr('go')?></button>
                </form>
                </td>
            </tr>
        </table>
    </td>
<!-- End of Position -->
</tr>
</table>

