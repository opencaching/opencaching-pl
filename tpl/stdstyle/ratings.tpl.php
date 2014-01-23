
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="" title="Recommendation" align="middle"/>&nbsp;{{recommended_caches}} geocache</div>
<!-- Text container -->
    <p style="font-size: 12px; line-height: 1.6em;"><span class="content-title-noshade txt-blue08" >
        {{recommendation_rating}} </span>&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" />&nbsp; <a class="links"  href="articles.php?page=s5">({{show}})</a>
    </p>
<div class="searchdiv">
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px;line-hight: 1.4em; font-size: 13px;" width="97%">
<tr>
<td>&nbsp;</td>
<td><img src="images/rating-star.png" border="0" alt=""/></td>
<td>&nbsp;</td>
<td><strong>Cache</strong></td>
<td><strong>User</strong></td>
</tr>
<tr>
<td colspan="5"><hr></hr></td>
</tr>
<?php
    // Wzorzec jest generowany dynamicznie przez skrypt /tpl/stdstyle/etc/write_ratings.inc.php
    global $dynstylepath;
    include ($dynstylepath . "ratings.tpl.php");
?>
<tr>
<td colspan="5"><hr></hr></td>
</tr>
</table>
</div>
<br/><br/>

