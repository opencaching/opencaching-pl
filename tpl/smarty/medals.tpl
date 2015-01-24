{block name=head}
    <link href="/tpl/smarty/medals.css" rel="stylesheet" type="text/css"/>
{/block}
<div class="mainContainer">

    <div class="medalWrapper">
        {foreach $smartyMedals.medals as $medal}
            <div class="medal">
                <img src="{$medal.imgSrc}" class="medalImage" /><br>
                <div class="medalName">
                    {$medal.name}
                </div>
                <div class="medalLevel">
                    {$smartyMedals.tr.level}: {$medal.level}
                </div>
            </div>
        {/foreach}
    </div>

</div>