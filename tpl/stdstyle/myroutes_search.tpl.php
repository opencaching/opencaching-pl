<?php

?>
<script type="text/javascript">
    var mnAttributesShowCat2 = 1;
    var maAttributes = new Array({attributes_jsarray});

    function check_recommendations() {
        if (document.optionsform.cache_rec[1].checked == true) {
            if (isNaN(document.optionsform.cache_min_rec.value)) {
                alert("Minimalna ilość rekomendacji musi być cyfrą!");
                return false;
            } else if (document.optionsform.cache_min_rec.value <= 0 || document.optionsform.cache_min_rec.value > 999) {
                alert("Dozwolona wartość minimalnej ilości rekomendacji musi być z zakresu: 0 - 999");
                return false;
            }
        }
        return true;
    }

    function sync_options(element)
    {

        var tmpattrib = "";
        for (i = 0; i < maAttributes.length; i++)
            if (maAttributes[i][1] == 1)
                tmpattrib = '' + tmpattrib + maAttributes[i][0] + ';';
        if (tmpattrib.length > 0)
            tmpattrib = tmpattrib.substr(0, tmpattrib.length - 1);

        var tmpattrib_not = "";
        for (i = 0; i < maAttributes.length; i++)
            if (maAttributes[i][1] == 2)
                tmpattrib_not = '' + tmpattrib_not + maAttributes[i][0] + ';';
        if (tmpattrib_not.length > 0)
            tmpattrib_not = tmpattrib_not.substr(0, tmpattrib_not.length - 1);

        var recommendations = 0;
        if (document.forms['optionsform'].cache_rec[0].checked == true) {
            document.forms['optionsform'].cache_min_rec.disabled = 'disabled';
            recommendations = 0;
        }
        else if (document.forms['optionsform'].cache_rec[1].checked == true) {
            document.forms['optionsform'].cache_min_rec.disabled = false;
            recommendations = document.forms['optionsform'].cache_min_rec.value;
        }
        document.optionsform.cacherating.value = recommendations;
        document.forms['optionsform'].f_inactive.value = document.optionsform.f_inactive.checked ? 1 : 0;
        document.forms['optionsform'].f_ignored.value = document.optionsform.f_ignored.checked ? 1 : 0;
        document.forms['optionsform'].f_userfound.value = document.optionsform.f_userfound.checked ? 1 : 0;
        document.forms['optionsform'].f_userowner.value = document.optionsform.f_userowner.checked ? 1 : 0;

        document.forms['optionsform'].cachetype1.value = document.optionsform.cachetype1.checked ? 1 : 0;
        document.forms['optionsform'].cachetype2.value = document.optionsform.cachetype2.checked ? 1 : 0;
        document.forms['optionsform'].cachetype3.value = document.optionsform.cachetype3.checked ? 1 : 0;
        document.forms['optionsform'].cachetype4.value = document.optionsform.cachetype4.checked ? 1 : 0;
        document.forms['optionsform'].cachetype5.value = document.optionsform.cachetype5.checked ? 1 : 0;
        document.forms['optionsform'].cachetype6.value = document.optionsform.cachetype6.checked ? 1 : 0;
        document.forms['optionsform'].cachetype7.value = document.optionsform.cachetype7.checked ? 1 : 0;
        document.forms['optionsform'].cachetype8.value = document.optionsform.cachetype8.checked ? 1 : 0;
        document.forms['optionsform'].cachetype9.value = document.optionsform.cachetype9.checked ? 1 : 0;
        document.forms['optionsform'].cachetype10.value = document.optionsform.cachetype10.checked ? 1 : 0;

        document.forms['optionsform'].cachesize_2.value = document.optionsform.cachesize_2.checked ? 1 : 0;
        document.forms['optionsform'].cachesize_3.value = document.optionsform.cachesize_3.checked ? 1 : 0;
        document.forms['optionsform'].cachesize_4.value = document.optionsform.cachesize_4.checked ? 1 : 0;
        document.forms['optionsform'].cachesize_5.value = document.optionsform.cachesize_5.checked ? 1 : 0;
        document.forms['optionsform'].cachesize_6.value = document.optionsform.cachesize_6.checked ? 1 : 0;
        document.forms['optionsform'].cachesize_7.value = document.optionsform.cachesize_7.checked ? 1 : 0;
        document.forms['optionsform'].cachevote_1.value = document.optionsform.cachevote_1.value;
        document.forms['optionsform'].cachevote_2.value = document.optionsform.cachevote_2.value;
        document.forms['optionsform'].cachenovote.value = document.optionsform.cachenovote.checked ? 1 : 0;
        document.forms['optionsform'].cachedifficulty_1.value = document.optionsform.cachedifficulty_1.value;
        document.forms['optionsform'].cachedifficulty_2.value = document.optionsform.cachedifficulty_2.value;
        document.forms['optionsform'].cacheterrain_1.value = document.optionsform.cacheterrain_1.value;
        document.forms['optionsform'].cacheterrain_2.value = document.optionsform.cacheterrain_2.value;
        document.forms['optionsform'].cache_attribs.value = tmpattrib;
        document.forms['optionsform'].cache_attribs_not.value = tmpattrib_not;

    }

    function switchAttribute(id)
    {
        var attrImg = document.getElementById("attrimg" + id);
        var nArrayIndex = 0;

        for (nArrayIndex = 0; nArrayIndex < maAttributes.length; nArrayIndex++)
        {
            if (maAttributes[nArrayIndex][0] == id)
                break;
        }

        if (maAttributes[nArrayIndex][1] == 0)
        {
            attrImg.src = maAttributes[nArrayIndex][3];
            maAttributes[nArrayIndex][1] = 1;
        }
        else if (maAttributes[nArrayIndex][1] == 1)
        {
            attrImg.src = maAttributes[nArrayIndex][4];
            maAttributes[nArrayIndex][1] = 2;
        }
        else if (maAttributes[nArrayIndex][1] == 2)
        {
            attrImg.src = maAttributes[nArrayIndex][5];
            maAttributes[nArrayIndex][1] = 0;
        }

        sync_options(null);
    }
    function hideAttributesCat2()
    {
        mnAttributesShowCat2 = 0;
        document.getElementById('attributesCat2').style.display = "none";
    }

    function showAttributesCat2()
    {
        mnAttributesShowCat2 = 1;
        document.getElementById('attributesCat2').style.display = "block";
    }

    function switchCat2()
    {
        if (mnAttributesShowCat2 != 0)
            hideAttributesCat2();
        else
            showAttributesCat2();
    }
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{search_caches_along_route}}: <span style="color: black;font-size:13px;">{routes_name} ({{radius}} {distance} km)</span></div>
<form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="optionsform" dir="ltr">
    <input type="hidden" name="routeid" value="{routeid}"/>
    <input type="hidden" name="distance" value="{distance}"/>
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />
    <div class="searchdiv">

        <p class="content-title-noshade-size3">{{search_options}}</p>
        <div class="searchdiv">
            <table class="table">
                <tr>
                    <td class="content-title-noshade">{{omit_caches}}:</td>
                    <td colspan="2">
                        <input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="javascript:sync_options(this)" {f_inactive_checked} /> <label for="l_inactive">{{not_active}}</label>
                        <input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="javascript:sync_options(this)" {f_ignored_disabled} /> <label for="l_ignored">{{ignored}}</label>
                        <input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="javascript:sync_options(this)" {f_userfound_disabled} /> <label for="l_userfound">{{founds}}</label>&nbsp;&nbsp;
                        <input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="javascript:sync_options(this)" {f_userowner_disabled} /> <label for="l_userowner">{{of_owner}}</label>&nbsp;&nbsp;
                    </td>
                </tr>
            </table>
        </div>
        <div class="searchdiv">
            <table class="table">
                <tr>
                    <td  class="content-title-noshade">{{cache_type}}:</td>
                    <td>

                        <table class="table">
                            <tr>
                                <td><input type="checkbox" id="cachetype2" name="cachetype2" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype2} /> <label for="cachetype2">{{traditional}}</label></td>
                                <td><input type="checkbox" id="cachetype3" name="cachetype3" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype3} /> <label for="cachetype3">{{multicache}}</label></td>
                                <td><input type="checkbox" id="cachetype5" name="cachetype5" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype5} /> <label for="cachetype5">{{webcam}}</label></td>
                                <td><input type="checkbox" id="cachetype6" name="cachetype6" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype6} /> <label for="cachetype6">{{event}}</label></td>

                            </tr>
                            <tr>
                                <td><input type="checkbox" id="cachetype7" name="cachetype7" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype7} /> <label for="cachetype7">{{quiz}}</label></td>
                                <td><input type="checkbox" id="cachetype8" name="cachetype8" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype8} /> <label for="cachetype8">{{moving}}</label></td>
                                <td><input type="checkbox" id="cachetype9" name="cachetype9" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype9} /> <label for="cachetype9">{{podcast}}</label></td>
                                <td><input type="checkbox" id="cachetype10" name="cachetype10" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype10} /> <label for="cachetype10">{{owncache}}</label></td>

                            </tr>
                            <tr>
                                <td><input type="checkbox" id="cachetype4" name="cachetype4" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype4} /> <label for="cachetype4">{{virtual}}</label></td>
                                <td><input type="checkbox" id="cachetype1" name="cachetype1" value="1" onclick="javascript:sync_options(this)" class="checkbox"  {cachetype1} /> <label for="cachetype1">{{unknown_type}}</label></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="searchdiv">
            <table class="table">
                <tr>
                    <td  class="content-title-noshade">{{cache_size}}:</td>

                    <td>
                        <table class="table">
                            <tr>
                                <td>
                                    <input type="checkbox" name="cachesize_1" value="1" id="l_cachesize_1" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_1} /><label for="l_cachesize_1">{{cacheSize_1}}</label>
                                    <input type="checkbox" name="cachesize_2" value="1" id="l_cachesize_2" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_2} /><label for="l_cachesize_2">{{cacheSize_2}}</label>
                                    <input type="checkbox" name="cachesize_3" value="1" id="l_cachesize_3" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_3} /><label for="l_cachesize_3">{{cacheSize_3}}</label>
                                    <input type="checkbox" name="cachesize_4" value="1" id="l_cachesize_4" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_4} /><label for="l_cachesize_4">{{cacheSize_4}}</label>
                                    <input type="checkbox" name="cachesize_5" value="1" id="l_cachesize_5" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_5} /><label for="l_cachesize_5">{{cacheSize_5}}</label>
                                    <input type="checkbox" name="cachesize_6" value="1" id="l_cachesize_6" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_6} /><label for="l_cachesize_6">{{cacheSize_6}}</label>
                                    <input type="checkbox" name="cachesize_7" value="1" id="l_cachesize_7" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_7} /><label for="l_cachesize_7">{{cacheSize_7}}</label>
                                    <input type="checkbox" name="cachesize_8" value="1" id="l_cachesize_8" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_8} /><label for="l_cachesize_8">{{cacheSize_8}}</label>
                                </td>

                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="searchdiv">
            <table class="table">
                <tr>
                    <td valign="middle" class="content-title-noshade">{{cache_attributes}}:</td>
                    <td class="content-title-noshade">
                        <div style="width:500px;">{cache_attrib_list}</div>
                        <div id="attributesCat2">{cache_attribCat2_list}</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="searchdiv">
            <table class="table">
                <tr class="form-group-sm">
                    <td valign="top" class="content-title-noshade">{{task_difficulty}}:</td>
                    <td class="content-title-noshade">
                        {{from}} <select name="cachedifficulty_1" class="form-control input50" onchange="javascript:sync_options(this)">
                            <option value="1" {cdf2}>1</option>
                            <option value="1.5" {cdf3}>1.5</option>
                            <option value="2" {cdf4}>2</option>
                            <option value="2.5" {cdf5}>2.5</option>
                            <option value="3" {cdf6}>3</option>
                            <option value="3.5" {cdf7}>3.5</option>
                            <option value="4" {cadf8}>4</option>
                            <option value="4.5" {cdf9}>4.5</option>
                            <option value="5" {cdf10}>5</option>
                        </select>
                        {{to}} <select name="cachedifficulty_2" class="form-control input50" onchange="javascript:sync_options(this)">
                            <option value="1" {cdt2}>1</option>
                            <option value="1.5" {cdt3}>1.5</option>
                            <option value="2" {cdt4}>2</option>
                            <option value="2.5" {cdt5}>2.5</option>
                            <option value="3" {cdt6}>3</option>
                            <option value="3.5" {cdt7}>3.5</option>
                            <option value="4" {cdt8}>4</option>
                            <option value="4.5"{cdt9}>4.5</option>
                            <option value="5" {cdt10}>5</option>
                        </select>
                    </td>
                </tr>
                <tr class="form-group-sm"><td class="buffer" colspan="3"></td></tr>
                <tr class="form-group-sm">
                    <td valign="top" class="content-title-noshade">{{terrain_difficulty}}:</td>
                    <td class="content-title-noshade">
                        {{from}} <select name="cacheterrain_1" class="form-control input50" onchange="javascript:sync_options(this)">
                            <option value="1" {ctf2}>1</option>
                            <option value="1.5" {ctf3}>1.5</option>
                            <option value="2" {ctf4}>2</option>
                            <option value="2.5" {ctf5}>2.5</option>
                            <option value="3" {ctf6}>3</option>
                            <option value="3.5" {ctf7}>3.5</option>
                            <option value="4" {ctf8}>4</option>
                            <option value="4.5" {ctf9}>4.5</option>
                            <option value="5" {ctf10}>5</option>
                        </select>
                        {{to}} <select name="cacheterrain_2" class="form-control input50" onchange="javascript:sync_options(this)">
                            <option value="1" {ctt2}>1</option>
                            <option value="1.5" {ctt3}>1.5</option>
                            <option value="2" {ctt4}>2</option>
                            <option value="2.5" {ctt5}>2.5</option>
                            <option value="3" {ctt6}>3</option>
                            <option value="3.5" {ctt7}>3.5</option>
                            <option value="4" {ctt8}>4</option>
                            <option value="4.5" {ctt9}>4.5</option>
                            <option value="5" {ctt10}>5</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <div class="searchdiv">
            <table class="table">
                <tr class="form-group-sm">
                    <td valign="top" class="content-title-noshade">{{scores}}:</td>
                    <td class="content-title-noshade">
                        {{from}} <select name="cachevote_1" class="form-control input200" onchange="javascript:sync_options(this)">
                            <option value="-3" {cvf6}>{{rating_poor}}</option>
                            <option value="-1" {cvf1}>{{rating_mediocre}}</option>
                            <option value="0.1" {cvf2}>{{rating_avarage}}</option>
                            <option value="1.4" {cvf4}>{{rating_good}}</option>
                            <option value="2.2" {cvf5}>{{rating_excellent}}</option>
                        </select>
                        {{to}} <select name="cachevote_2" class="form-control input200" onchange="javascript:sync_options(this)">
                            <option value="-0.999" {cvt1}>{{rating_poor}}</option>
                            <option value="0.099" {cvt2}>{{rating_mediocre}}</option>
                            <option value="1.399" {cvt4}>{{rating_avarage}}</option>
                            <option value="2.199" {cvt5}>{{rating_good}}</option>
                            <option value="3.000" {cvt6}>{{rating_excellent}}</option>
                        </select><br/>
                        <input type="checkbox" name="cachenovote" value="1" id="l_cachenovote" class="checkbox" onclick="javascript:sync_options(this)" {cachev}/><label for="l_cachenovote">{{with_hidden_score}}</label>
                    </td>
                </tr>
                <tr><td class="buffer" colspan="3"></td></tr>
                <tr class="form-group-sm">
                    <td class="content-title-noshade">{{search_recommendations}}:</td>

                    <td class="content-title-noshade" colspan="2">
                        <input type="radio" name="cache_rec" value="0" tabindex="0" id="l_all_caches" class="radio" onclick="javascript:sync_options(this)" {all_caches_checked} /> <label for="l_all_caches">{{search_all_caches}}</label>&nbsp;
                        <input type="radio" name="cache_rec" value="1" tabindex="1" id="l_recommended_caches" class="radio" onclick="javascript:sync_options(this)" {recommended_caches_checked} /> <label for="l_recommended_caches">{{search_recommended_caches}}</label>&nbsp;
                        <input type="text" name="cache_min_rec" value="{cache_min_rec}" maxlength="3" class="form-control input50" onchange="javascript:sync_options(this)" {min_rec_caches_disabled} />
                    </td>
                </tr>

            </table>
        </div>








    </div>
    <br/>
    <button type="submit" name="back_list" value="back_list" class="btn btn-default">{{back}}</button>&nbsp;&nbsp;
    <button type="submit" name="submit" value="submit" class="btn btn-primary">{{show_list}}</button>
    <button type="submit" name="submit_map" value="submit_map" class="btn btn-primary">{{show_gmap}}</button>

</form>
<br/><br/><br/>


