
<div class="content2-pagetitle">
  <b>{{rating_title}}</b>
</div>

<div id="difficult-form-result">

    <p>{{rating_comment}}</p>

    <div class="results">

        <h3>{{rating_diffResult}}&nbsp;<?=$view->diffResult?></h3>

        <div class="result-row<?=($view->diffResult == 1)?' emphasis':''?>">
            <div class="level">1/5</div><div class="text">{{rating_difResult0}}</div>
        </div>

        <div class="result-row<?=($view->diffResult == 2)?' emphasis':''?>">
            <div class="level">2/5</div><div class="text">{{rating_difResult1}}</div>
        </div>

        <div class="result-row<?=($view->diffResult == 3)?' emphasis':''?>">
            <div class="level">3/5</div><div class="text">{{rating_difResult2}}</div>
        </div>

        <div class="result-row<?=($view->diffResult == 4)?' emphasis':''?>">
            <div class="level">4/5</div><div class="text">{{rating_difResult3}}</div>
        </div>

        <div class="result-row<?=($view->diffResult == 5)?' emphasis':''?>">
            <div class="level">5/5</div><div class="text">{{rating_difResult4}}</div>
        </div>

    </div>

    <div class="results">

        <h3>{{rating_terrainResult}}&nbsp;<?=$view->terrainResult?></h3>


        <div class="result-row<?=($view->terrainResult == 1)?' emphasis':''?>">
            <div class="level">1/5</div><div class="text">{{rating_terResult0}}</div>
        </div>

        <div class="result-row<?=($view->terrainResult == 2)?' emphasis':''?>">
            <div class="level">2/5</div><div class="text">{{rating_terResult1}}</div>
        </div>

        <div class="result-row<?=($view->terrainResult == 3)?' emphasis':''?>">
            <div class="level">3/5</div><div class="text">{{rating_terResult2}}</div>
        </div>

        <div class="result-row<?=($view->terrainResult == 4)?' emphasis':''?>">
            <div class="level">4/5</div><div class="text">{{rating_terResult3}}</div>
        </div>

        <div class="result-row<?=($view->terrainResult == 5)?' emphasis':''?>">
            <div class="level">5/5</div><div class="text">{{rating_terResult4}}</div>
        </div>

    </div>

    <div class="difficultyForm-buttons">
        <form action="">
            <input type="submit" value="{{rating_rateAgain}}" class="btn btn-default">
        </form>
    </div>

    <p class= "idea-source">({{rating_disclaimer}})</p>

</div>
