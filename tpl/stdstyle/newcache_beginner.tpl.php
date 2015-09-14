<?php

global $NEED_FIND_LIMIT, $NEED_APPROVE_LIMIT;
?>

<table class="content" border="0">
    <tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}" align="middle" /><font size="4"><b>{{mc_beginn_00}}</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
</table>
<br />
<div class="searchdiv" style="background-color: #FFF9E3;">
    <p style="margin: 10px;font-size: 12.5px; line-height:1.6em; text-align: justify;"><b>{{mc_beginn_01}} <span style="font-size: 14px;color:red;"><?php echo $NEED_FIND_LIMIT; ?></span> {{mc_beginn_02}}
            <font color="blue">
            <ul>
                <li><img src="tpl/stdstyle/images/cache/traditional-i.png" alt="cache"> {{traditional}}, </li>
                <li><img src="tpl/stdstyle/images/cache/multi-i.png" alt="cache"> {{multicache}},</li>
                <li><img src="tpl/stdstyle/images/cache/quiz-i.png" alt="cache"> {{quiz}}, </li>
                <li><img src="tpl/stdstyle/images/cache/moving-i.png" alt="cache"> {{moving}}, </li>
                <li><img src="tpl/stdstyle/images/cache/unknown-i.png" alt="cache"> {{unknown_type}}.</li>
            </ul></font><br/>
            {{mc_beginn_03}} <span style="font-size: 14px;color:green;">{number_finds_caches}</span><br/><br/>

            {{mc_beginn_04}} <span style="font-size: 14px;color:red;"><?php echo $NEED_APPROVE_LIMIT; ?></span> {{mc_beginn_05}}</b>
    </p>
    <br />
</div>

