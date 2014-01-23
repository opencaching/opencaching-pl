<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" align="middle"/>&nbsp;{{cache_reports_01}}</div>
    <div class="buffer"></div>
    <p>[{{cache_reports_02}} <a href="viewreports.php?archiwum={archiwum}">{arch_curr}</a>]</p>
        <input type="hidden" name="cacheid" value="{cacheid}"/>
        <table border='1' class="table" width="90%">
            <tr>
                <th >ID</th>
                <th >{{cache_reports_03}}</th>
                <th >Cache</th>
                <th >{{cache_reports_04}}</th>
                <th >{{cache_reports_05}}</th>
                <th >{{cache_reports_06}}</th>
                <th >{{cache_reports_07}}</th>
                <th >Status</th>
                <th >{{cache_reports_08}}</th>
            </tr>
            {content}
        </table>

