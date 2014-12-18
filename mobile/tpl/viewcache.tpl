{$pagename=$view_title1}

{include file="./tpl/header.inc.tpl"}

<div id="content">

    {if $error eq '1'}
        <span class="error"><center>{$no_wp}</center></span>
            {else}

        <div>{if $cache['if_found'] == '1'}
            <img src="../images/16x16-found.png"/>
        {elseif $cache['if_found'] == '2'}
            <img src="../images/16x16-dnf.png"/>
        {/if}

        {if $cache['status2'] == '2'}
            <img src="../images/flag.png" alt=""/>
        {/if}
        {if $cache['status2'] == '3'}
            <img src="../images/bin.png" alt=""/>
        {/if}

        <b>{$cache['name']}</b></div>

    <div><i>{$cache['short_desc']}</i></div>

    <hr/>

    {if $show_coords}
        <b>N {$cache['N']}</b> (N {$cache['latitude']})<br/>
        <b>E {$cache['E']}</b> (E {$cache['longitude']})<br/><br/>
    {else}
        <font size="smaller">{$map_msg}</font>
    {/if}

    <table class="tableview">
        <tr><td>{$type}</td><td><b>{$cache['type']}</b></td></tr>
        <tr><td>{$size}</td><td><b>{$cache['size']}</b></td></tr>
        <tr><td>{$status}</td><td><b>

                    {if $cache['status2'] != '1'}
                        <span class="error">
                        {/if}
                        {$cache['status']}
                        {if $cache['status2'] != '1'}
                        </span>
                    {/if}
                </b></td></tr>
        <tr><td>{$hidden_date}</td><td><b>{$cache['hidden_date']}</b></td></tr>
        <tr><td>{$view_wpt}</td><td><b>{$cache['wp_oc']}</b></td></tr>
        <tr><td>{$score}</td><td><b>

                {if $cache['score']=='0'}{$rate0}{/if}
            {if $cache['score']=='1'}{$rate1}{/if}
        {if $cache['score']=='2'}{$rate2}{/if}
    {if $cache['score']=='3'}{$rate3}{/if}
{if $cache['score']=='4'}{$rate4}{/if}
{if $cache['score']=='5'}N/A{/if}

</b></td></tr>
<tr><td>{$view_owner}</td><td>

        {if $smarty.session.user_id}
            <a href='./user.php?id={$cache['user_id']}'>{$cache['owner']}</a>
        {else}
            {$cache['owner']}
        {/if}
    </td></tr>
<tr><td>{$topratings}</td><td>{$cache['topratings']}</td></tr>
<tr><td>{$stats}</td><td><b><span style="color:green">{$cache['founds']}</span> / <span style="color:red">{$cache['notfounds']}</span> / {$cache['notes']}</b></td></tr>
</table>
<br/>
{if $attr_text != ""}
    <div class='button' style="width:16%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='javascript:alert("{$attr_text}");'><img style="vertical-align: middle;" src="../images/attributes.png" alt="{$show_attrib}"/></a></div>
        {/if}
        {if $show_coords}
    <div class="button" style="width:16%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./file2.php?wp={$cache['wp_oc']}'><img style="vertical-align: middle; " src="../images/download.png" alt="{$download_file}"/></a></div>
    <div class="button" style="width:16%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./googlemaps.php?wp={$cache['wp_oc']}'><img style="vertical-align: middle;" src="../images/geo.png" alt="{$show_map}"/></a> </div>

    <div class="button" style="width:16%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./osmap.php?wp={$cache['wp_oc']}'><img style="vertical-align: middle;" src="../images/osm.png" alt="{$show_map}"/></a> </div>
        {/if}
        {if $cache['watched']==-1}
    <div class="button" style="width:16%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./watchcache.php?wp={$cache['wp_oc']}'><img style="vertical-align: middle;" src="../images/eye.png" alt="{$watch}"/></a> </div>
        {/if}
        {if $cache['watched']>-1}
    <div class="button" style="width:16%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./removewatch.php?id={$cache['watched']}'><img style="vertical-align: middle;" src="../images/eye2.png" alt="{$not_watch}"/></a> </div>
        {/if}
<br/><hr/><br/>

<div id="description">{$cache['desc']}</div><br/>

{if $gk != ""}
    <hr/><br/><div class='button'><a href='javascript:alert("\n{$gk}");'>{$show_gk}</a></div><br/>
    {/if}

{if $cache['hint'] != "" && $show_coords}
    <br/><div class='button'><a href='javascript:alert("\n{$cache['hint']}\n\n");'>{$show_spoiler}</a></div><br/>
    {/if}

{if $cache['picturescount'] > '0'}
    <hr/><br/><b>{$photos}:</b><br/><br/>
    {section name=i loop=$photos_list}
        <div class='button'>
            {if {$photos_list[i].spoiler} eq '1' && !$show_coords}
                <a href="#" onclick='alert("{$vc_spoiler_disable_msg}");
                        return false;'>{$photos_list[i].title} (spoiler)
                </a>
            {else}
                <a target=blank href={$photos_list[i].url}>{$photos_list[i].title}
                    {if {$photos_list[i].spoiler} eq '1'}(spoiler){/if}
                </a>
            {/if}
        </div><br/>
    {/section}
{/if}

{if $cache['founds']>0 || $cache['notfounds']>0 || $cache['notes']>0 }
    <hr/><br/><div class='button'><a href=./logs.php?wp={$cache['wp_oc']}>{$show_entries}</a></div><br/>
    {elseif $smarty.session.user_id}
    <hr/><br/>
{/if}


{if $smarty.session.user_id}
    <div class='button'><a href="./logentry.php?wp={$cache['wp_oc']}">{$add_entry}</a></div><br/>
    {/if}

{/if}

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}
