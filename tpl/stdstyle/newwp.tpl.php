<?php

?>
<script type="text/javascript">
    function _chkType()
    {
        var nextstage = document.forms['waypoints_form'].nextstage.value;
        if (document.waypoints_form.type.value == "4" || document.waypoints_form.type.value == "5")
        {
            document.waypoints_form.stage.value = "0";
            document.waypoints_form.stage.disabled = true;

        }
        else {
            document.waypoints_form.stage.value = nextstage;
            document.waypoints_form.stage.disabled = false;

        }
        // ======= openchecker checkbox start ==============================
        // this part of script display or hide section witch checkbox
        // which allow final waypoint to be used into OpenChecker
        //---------------------------------------------------------------------
        if (document.waypoints_form.type.value == "3")
        {
            document.getElementById('openchecker_block').style.display = 'block';
        }
        else
        {
            document.getElementById('openchecker_block').style.display = 'none';
            document.getElementById('openchecker').checked = false;
        }
        // ====== openchecker checkbox stop ================================


        return false;
    }
</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />&nbsp;{{add_new_waypoint}} {{for_cache}}: <font color="black">{cache_name}</color></div>
{general_message}
<form action="newwp.php" method="post" enctype="application/x-www-form-urlencoded" name="waypoints_form" dir="ltr">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="cachetype" value="{cachetype}"/>
    <input type="hidden" name="nextstage" value="{nextstage}"/>
    <div class="searchdiv">



        <table width="90%" class="table" border="0">
            <tr><td class="buffer" colspan="2"></td></tr>

            <tr>
                <td class="content-title-noshade">{{type_wp2}}:</td>
                <td>
                    <select name="type" class="form-control input200" onChange="return _chkType()">
                        {typeoptions}
                    </select>{type_message}
                </td>
            </tr>
            <tr><td>&nbsp;</td>
                <td><div class="notice" style="width:500px;min-height:24px;height:auto;">{{waypoints_about_info}}</div></td>
            </tr>
            {start_stage}
            <tr>
                <td class="content-title-noshade">{{number_stage_wp}}:</td>
                <td>
                    <input type="text"     name="stage" maxlength="2" value="{stage}" class="form-control input40" />{stage_message}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><div class="notice" style="width:350px;height:44px;">{{show_info_for_value}}</div>
                </td>
            </tr>
            {end_stage}
            <tr>
                <td valign="top" class="content-title-noshade">{{coordinates}}:</td>
                <td class="content-title-noshade">
                    <fieldset style="border: 1px solid black; width: 250px; height: 32%; background-color: #FAFBDF;"  class="form-group-sm">
                        <legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
                        <select name="latNS" class="form-control input50">
                            <option value="N"{latNsel}>N</option>
                            <option value="S"{latSsel}>S</option>
                        </select>
                        &nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="form-control input40" />
                        &deg;&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="form-control input70" onkeyup="this.value = this.value.replace(/,/g, '.'); this.selectionStart = this.selectionEnd = this.value.length;" />&nbsp;'&nbsp;
                        {lat_message}<br />
                        &nbsp;&nbsp;&nbsp;
                        <select name="lonEW" class="form-control input50">
                            <option value="E"{lonEsel}>E</option>
                            <option value="W"{lonWsel}>W</option>
                        </select>
                        &nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="form-control input40" />
                        &deg;&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="form-control input70" onkeyup="this.value = this.value.replace(/,/g, '.'); this.selectionStart = this.selectionEnd = this.value.length;" />&nbsp;'&nbsp;
                        {lon_message}
                    </fieldset>
                </td>
            </tr>
            <!-- === openchecker section checkbox start ================================== -->
            {openchecker_start}
            <tr>
                <td></td>
                <td>
                    <div name="openchecker_block" id="openchecker_block" style="display: {openchecker_display};">
                        <br />
                        <input type="checkbox"  id="openchecker" name="openchecker" {openchecker_checked}/>
                        <label for="openchecker">{{openchecker_name}}</label>
                        <br/>
                        <div class="notice" style="width:350px;">{{openchecker_enable_checkbox}} </div>
                    </div>
                </td>
            </tr>
            {openchecker_end}
            <!-- === openchecker section checkbox stop ==================================== -->
            <tr><td colspan="2"><div class="buffer"></div></td></tr>
            <tr>
                <td valign="top" class="content-title-noshade">{{describe_wp}}:</td>
                <td class="content-title-noshade">
                    <textarea name="desc" rows="10" cols="60">{desc}</textarea>{desc_message}
                </td>

            </tr>
            <tr>
                <td valign="top" class="content-title-noshade">{{status_wp}}:</td>
            </tr>
            <tr>
                <td valign="top" align="left" colspan="2">
                    <table border="0" style="width:600px;font-size: 12px; line-height: 1.6em;">
                        <tr>
                            <td>
                                <input type="radio" name="status" id="status_1" value="1" {checked1} />
                                <label for="status_1" style="font-size: 12px; line-height: 1.6em;">{{wp_status1}}</label>
                            </td>
                        </tr>
                        <tr><td>
                                <input type="radio" name="status" id="status_2" value="2" {checked2} />
                                <label for="status_2" style="font-size: 12px; line-height: 1.6em;">{{wp_status2}}</label>
                            </td>
                        </tr>
                        <tr><td>
                                <input type="radio" name="status" id="status_3" value="3" {checked3} />
                                <label for="status_3" style="font-size: 12px; line-height: 1.6em;">{{wp_status3}}</label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td valign="top" align="left" colspan="2">
                <button type="button" name="back" value="back" class="btn btn-default">{{cancel}}</button>&nbsp;&nbsp;
                <button type="submit" name="submitform" value="submit" class="btn btn-primary">{{write_wp}}</button>
                <br /><br /></td>
        </tr>

        </table>
</form>
</div>
