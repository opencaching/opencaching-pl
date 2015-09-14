
<table width="100%">
    <tr><td class="content2-pagetitle" style="background-color:#E9E9E9;"><img src="tpl/stdstyle/images/misc/32x32-winner.png" class="icon32" alt="{t}Special caches{/t}" title="{t}Special caches{/t}" align="middle" /><font size="4">  <b>{t}Special caches{/t}</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
    <tr>
        <td style="padding-left:32px; padding-bottom:32px;">
            {t}The following list is generated automatically by the given recommendations of the users.{/t}
        </td>
    </tr>
    <tr>
        <td style="padding-left:32px; padding-bottom:32px;">
            <table width="100%">
                {foreach name=adm1 from=$tops item=adm1item}
                <tr>
                    <td valign="top" width="150px">{$adm1item.name|escape}</td>
                    <td>
                        {foreach name=adm2 from=$adm1item.adm2 item=adm2item}
                        {if $adm2item.name==null}
                        <a href="#{$adm1item.name|urlencode}null"><i>(ohne geogr. Bezug)</i><br /></a>
                        {else}
                        <a href="#{$adm1item.name|urlencode}{$adm2item.name|urlencode}">{$adm2item.name|escape}</a><br />
                        {/if}
                        {/foreach}
                    </td>
                </tr>
                {/foreach}
            </table>
        </td>
    </tr>
</table>

{foreach name=adm1 from=$tops item=adm1item}
{foreach name=adm2 from=$adm1item.adm2 item=adm2item}
<p>
<table width="100%">
    <tr>
        <td class="content2-pagetitle" style="background-color:#E9E9E9;" colspan="4">
            <a name="{$adm1item.name|urlencode}{if $adm2item.name==null}null{else}{$adm2item.name|urlencode}{/if}"></a>
            <font size="3">
            <b>
                {$adm1item.name|escape}
                &gt;

                {if $adm2item.name==null}
                (ohne geogr. Bezug)
                {else}
                {$adm2item.name|escape}
                {/if}
            </b>
            </font>
        </td>
    </tr>

    <tr>
        <td align="right">{t}Index{/t}</td>
        <td align="center"><img src="images/rating-star.png" border="0" alt="{t}Recommendations{/t}" /></td>
        <td align="center"><img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="{t}Found{/t}" /></td>
        <td>&nbsp;</td>
    </tr>
    {foreach name=cache from=$adm2item.items item=cacheItem}
    <tr>
        <td width="40px" align="right">
            {$cacheItem.idx}
        </td>
        <td width="40px" align="center">
            {$cacheItem.ratings}
        </td>
        <td width="60px" align="center">
            {$cacheItem.founds} ({$cacheItem.foundAfterRating})
        </td>
        <td>
            <a href="viewcache.php?wp={$cacheItem.wpoc}">{$cacheItem.name|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$cacheItem.userid}">{$cacheItem.username|escape}</a>
        </td>
    </tr>
    {/foreach}
</table>
</p>
{/foreach}
{/foreach}
