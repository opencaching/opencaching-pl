<?php ?>

<script type="text/javascript">
function clearForms() {
    var i;
    for (i = 0; (i < document.forms.length); i++) {
        document.forms[i].reset();
    }
}

function toggle() {
    var ele = document.getElementById("toggleText");
    var text = document.getElementById("displayText1");
    var text2 = document.getElementById("displayText2");
    var text3 = document.getElementById("displayText3");
    var help_link1 = document.getElementById("help_link1");
    var help_link2 = document.getElementById("help_link2");
    var help_link3 = document.getElementById("help_link3");
    var help_block = document.getElementById("help_block");

    if (ele.style.display == "block") {
        ele.style.display = "none";
        text.innerHTML = "{{openchecker_info}}";
        text2.innerHTML = "{{openchecker_info}}";
        text3.innerHTML = "{{openchecker_info}}";
        help_link1.style.display = "block";
        help_link2.style.display = "none";
        help_link3.style.display = "none";
        help_block.style.display = "block";
    } else {
        ele.style.display = "block";
        text.innerHTML = "{{openchecker_back}}";
        text2.innerHTML = "{{openchecker_back}}";
        text3.innerHTML = "{{openchecker_back}}";
        help_link1.style.display = "none";
        help_link2.style.display = "block";
        help_link3.style.display = "block";
        help_block.style.display = "none";
    }
}
</script>

<body onLoad="clearForms()" onUnload="clearForms()">

    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/openchecker_32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" />
        {{openchecker_name}}
    </div>

    {section_1_start}

    <div id="help_link3" style="height:35px; display: none;">
        <a id="displayText3" href="javascript:toggle();" class=links href="{openchecker_script}">{{openchecker_info}}</a>
        <br /><br />
    </div>

    <div id="toggleText" style="display: none;" >
        <p>{{openchecker_help_01}} <br /><br />
            {{openchecker_help_02}} <br /><br />
            <b>{{openchecker_help_03}}</b> <br /><br />
            {{openchecker_help_04}}<br />
            {{openchecker_help_05}} <br />
            {{openchecker_help_06}} <br />
            {{openchecker_help_07}} <br />
            {{openchecker_help_08}} <br />
            {{openchecker_help_09}} <br /><br />
            {{openchecker_help_10}} <br /><br />
            {{openchecker_help_11}}<br />
            {{openchecker_help_12}} <br /><br />
            <b>{{openchecker_help_13}}</b>
            {{openchecker_help_14}} <br /><br />
            {{openchecker_help_15}} <br />
            {{openchecker_help_16}} <br />
            {{openchecker_help_17}} <br /><br />
            {{openchecker_help_18}}
        </p>
    </div>

    <div id="help_link1" class="notice" style="height:35px; display: block;">
        {{openchecker_help}}:
        <a id="displayText1" href="javascript:toggle();" class=links href="{openchecker_script}">{{openchecker_info}}</a>
    </div>

    <div id="help_link2" style="height:35px; display: none;"><br /><br />
        <a id="displayText2" href="javascript:toggle();" class=links href="{openchecker_script}">{{openchecker_info}}</a>
    </div>
    {section_1_stop}

    <div id="help_block" style="display: block;">

        {section_1_start}

        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{openchecker_title_01}}
            </p>
        </div>

        <div style="clear:both">{openchecker_form}</div>
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{openchecker_title_02}}
            </p>
        </div>

        <br/><br/><br/>

        <div class="searchdiv">
            <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
                <tr>
                    <td><a href="{openchecker_script}?sort=wpt">{{waypoint}}</a></td>
                    <td>{{openchecker_type}}</td>
                    <td><a href="{openchecker_script}?sort=name">{{cache_name}}</a></td>
                    <td>{{status_label}}</td>
                    <td><a href="{openchecker_script}?sort=owner">{{owner_label}}</a></td>
                    <td><a href="{openchecker_script}?sort=attempts">{{openchecker_tries}}</a></td>
                    <td><a href="{openchecker_script}?sort=hits">{{openchecker_hits}}</a></td>
                </tr>

                <tr>
                    <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td>
                </tr>
                {caches_table}
                <tr>
                    <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="5" width="100%"/></td>
                </tr>
            </table>
        </div>

        <p>&nbsp;</p>
        {section_1_stop}

        {section_2_start}
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{openchecker_form}}
            </p>
        </div>
        <div class="searchdiv">
            <br/>
          
            <table width="99%">
                <tr>
                    <td width="40">{cache_icon}</td>
                    <td valign="top">
                        {wp_oc} <br /><b><a href="viewcache.php?wp={wp_oc}">  {cachename}</a></b>
                    </td>
                    <td> </td>
                    <td align="right">
                        {{openchecker_owner}} <br/><i><a href="viewprofile.php?userid={user_id}">{user_name}</a></i>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td></tr>
            </table>

            <p class="errormsg">{openchecker_not_enabled}</p>
            {section_openchecker_form_start}
            <form name="openchecker" action="{openchecker_script}" method="post" class="form-group-sm">
                <input type="hidden" name="cacheid" value="{cacheid}">
                <input type="hidden" name="wp" value="{wp_oc}">
                {{openchecker_coords}}:<br/><br/>
                {{openchecker_degrees}} N: <input type="text" name="degrees_N" maxlength="2" size="2" class="form-control input40" />°
                {{openchecker_minutes}} N:  <input type="text" name="minutes_N"  maxlength="6" size="5" class="form-control input70" onkeyup="this.value = this.value.replace(/,/g, '.'); this.selectionStart = this.selectionEnd = this.value.length;" /><br/><br/>
                {{openchecker_degrees}} E: <input type="text" name="degrees_E" maxlength="3" size="2" class="form-control input40" />°
                {{openchecker_minutes}} E:  <input type="text" name="minutes_E"  maxlength="6" size="5" class="form-control input70" onkeyup="this.value = this.value.replace(/,/g, '.'); this.selectionStart = this.selectionEnd = this.value.length;" /><br/><br/><br/>
                <button type="submit" name="submit" value="{{openchecker_check}}" class="btn btn-primary">{{openchecker_check}}</button>
            </form>
            {section_openchecker_form_stop}
        </div>
        {section_2_stop}

        {section_3_start}
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{openchecker_result}}:
            </p>
        </div>

        <br/>

        <div>
            <table class="content">
                <tr>
                    <td>{image_yesno}</td>
                    <td class="content">
                        <h2 class="errormsg">{result_title}</h2>
                        <p>&nbsp;</p>
                        {score}
                        <p>{result_text}</p>

                        {waypoint_desc}

                        <p>&nbsp;</p>
                        {save_mod_coord}
                    </td>
                </tr>
            </table>

            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>
                {{openchecker_attempts_01}} {attempts_counter} {{openchecker_attempts_02}} {count_limit}
                {{openchecker_attempts_03}} {time_limit} {{openchecker_attempts_04}}
            </p>
        </div>
        {section_3_stop}

        {section_4_start}
        <hr />
        <p class="errormsg">
            {{openchecker_attempts_overflow}}
        </p>
        {section_4_stop}

        {section_5_start}
        <hr />
        <p class="errormsg">
            {openchecker_wrong_cache}
        </p>
        {section_5_stop}

    </div>
