
<div class="content2-pagetitle">
  <b>{{rating_title}}</b>
</div>

<p>{{rating_comment}}</p>

<div class="results">
    <h3>{{rating_diffResult}}&nbsp;<?=$view->diffResult?></h3>

    <p class="<?=($view->diffResult == 1)?'emphasis':''?>">
        <span class="level">*</span>{{rating_difResult0}}
    </p>

    <p class="<?=($view->diffResult == 2)?'emphasis':''?>">
        <span class="level">**</span>{{rating_difResult1}}
    </p>

    <p class="<?=($view->diffResult == 3)?'emphasis':''?>">
        <span class="level">***</span>{{rating_difResult2}}
    </p>

    <p class="<?=($view->diffResult == 4)?'emphasis':''?>">
        <span class="level">****</span>{{rating_difResult3}}
    </p>

    <p class="<?=($view->diffResult == 5)?'emphasis':''?>">
        <span class="level">*****</span>{{rating_difResult4}}
    </p>
</div>

<div class="results">
    <h3>{{rating_terrainResult}}&nbsp;<?=$view->terrainResult?></h3>

    <p class="<?=($view->terrainResult == 1)?'emphasis':''?>">
        <span class="level">*</span>{{rating_terResult0}}
    </p>

    <p class="<?=($view->terrainResult == 2)?'emphasis':''?>">
        <span class="level">**</span>{{rating_terResult1}}
    </p>

    <p class="<?=($view->terrainResult == 3)?'emphasis':''?>">
        <span class="level">***</span>{{rating_terResult2}}
    </p>

    <p class="<?=($view->terrainResult == 4)?'emphasis':''?>">
        <span class="level">****</span>{{rating_terResult3}}
    </p>

    <p class="<?=($view->terrainResult == 5)?'emphasis':''?>">
        <span class="level">*****</span>{{rating_terResult4}}
    </p>
</div>

<div class="rating-buttons">
    <form action="">
        <input type="submit" value="{{rating_rateAgain}}" class="btn btn-default">
    </form>
</div>

<p>({{rating_disclaimer}})</p>
