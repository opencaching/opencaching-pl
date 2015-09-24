<!DOCTYPE html>
<html>
<head>
<style>
#div_cacheInfo_main { margin: 5px }
#div_cacheName {float:left; font-size:120%;
    max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    padding-right: 10px}
#div_cacheCode {float:right; }
#div_cacheParams {float:left; padding-right: 20px }
#div_cacheCounters {float:right }
#div_cachePtLabel {
    float:left; clear: left;
}
#div_cachePT {
    clear:left; padding-left:20px;
    max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-size:12px}

#div_cacheInfo_main a { text-decoration: none }

</style>
</head>
<body>


<?php //cords needs to be also pushed to browser in hidden form ?>
<input type='hidden' name='cache_cords' value='{"latitude":  {cache_lat}, "longitude": {cache_lon}}' />

<div id='div_cacheInfo_main'>

    <div id='div_cacheName'>
        <a style='' title='{cache_name}' href='{cache_url}' target='_blank'>
            <img style='width: 20px; height: 20px; vertical-align: middle;' src='{cache_icon}'/>
            {cache_name}
        </a>
    </div>

    <div id='div_cacheCode'>
        <b>{cache_code}</b>
        <a href='/viewcache.php?wp={cache_code}&print_list=y' target='_blank'>
            <img src='/images/actions/list-add-16.png' title='{{add_to_list}}' alt='{{add_to_list}}'>
        </a>
    </div>

<div style="clear:both; padding-top: 5px"></div>


    <div id='div_cacheParams'>
        <?php //events doesn't have any size
        if(! {is_event} ){ ?>
        <p>
            <b>{{size}}:</b> {cache_size_desc}
        </p>
        <?php } //if-is-event ?>

        <?php //there is enough votes => score present
        if( {is_scored} ){ ?>
        <p>
            <b>{{score}}:</b> {rating_desc}
        </p>
        <?php } //if-is-scored ?>

        <p>
            <b>{{owner}}:</b>
            <a href='{user_profile}' style='text-decoration: none;' target='_blank'>{user_name}</a>
        </p>

    </div>

    <div id='div_cacheCounters'>

        <p>
            <img src='/tpl/stdstyle/images/log/16x16-found.png' width='10' height='10' />
            x {cache_founds}
        </p>
        <p>
            <img src='/tpl/stdstyle/images/log/16x16-dnf.png' width='10' height='10' />
            <?php //for events founds = attended
                if(! {is_event} ) { ?>
                    x {cache_not_founds}
            <?php } else { ?>
                    x {cache_willattends}
            <?php } ?>
        </p>
        <p>
            <img src='/tpl/stdstyle/images/action/16x16-adddesc.png' width='10' height='10' />
            x {cache_rating_votes}
        </p>
        <?php //cache has recomendations
            if( {is_recommended} ){ ?>
                <p>
                    <img width="10" height="10" src='/images/rating-star.png' alt="{{recommendation}}" />
                    x {cache_recommendations}
                </p>
        <?php } //if-is-recommended ?>
        
       <?php //cache is titled
             if( {is_titled} ) {               ?>
                <p>
                    <img width="10" height="10" src='/tpl/stdstyle/images/free_icons/award_star_gold_1.png' alt="{{titled_cache}}" />
                    {{titled_cache}}
                </p>
       <?php } //cache is titled ?>
       
    </div>

    <?php if( {is_powertrail_part} ){ ?>
    <div id='div_cachePtLabel'>
        <p>
            <b>{{pt000}}:</b>
        </p>
    </div>
    <?php } //if-is-powerTrail-part ?>

    <?php if( {is_powertrail_part} ){ ?>
    <div id='div_cachePT'>
       <a style='text-decoration: none;' href='{pt_url}' title='{pt_name}' target='_blank'>
       <img width="20" height="20" src="{pt_icon}" alt="{{PowerTrail}}" title='{pt_name}' />
       {pt_name}</a>
    </div>
    <?php } //if-is-powertrail_part ?>

</div>

</body>
</html>