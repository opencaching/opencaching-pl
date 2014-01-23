<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    *  UTF-8 ąść
    ***************************************************************************/
?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="" title="" align="middle"/>&nbsp;{{my_neighborhood}} - {{top_recommended}}</div>
<p>&nbsp;</p>
<div class="searchdiv">
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="99%">
<tr>
<td style="width: 90px;"><strong>{{date_hidden_label}}</strong></td>
<td style="width: 22px;"><img src="images/rating-star.png" border="0" alt="Recomended" title="Recomended"/></td>
<td colspan="2"><strong>Geocache</strong></td>
<td><strong>{{owner}}</strong>&nbsp;&nbsp;&nbsp;</td>
<td colspan="3"><strong>{{latest_logs}}</strong></td>
</tr>
<tr>
<td colspan="8"><hr></hr></td>
</tr>
        {file_content}
<tr>
<td colspan="8"><hr></hr></td>
</tr>
</table>
</div>
    <p>
        {pages}
    </p>

