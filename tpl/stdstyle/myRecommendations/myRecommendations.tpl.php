
<div class="content2-container">

    <div class="content2-pagetitle">
      <?=tr('my_recommendations')?>
    </div>

    <?php if($view->rowCount > 0) { ?>
        <div class="content2-container">
          <?php $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);?>
        </div>
    <?php } else { //$view->cachesCount == 0 ?>
        <div>
            <?=tr('myRecommendations_emptyList')?>
        </div>
    <?php } //$view->cachesCount == 0 ?>

</div>

<script>
    var tr = {
        'myRecommendations_actionRemove': '<?=tr('myRecommendations_actionRemove')?>',
        'myRecommendations_recommendationRemovingError': '<?=tr('myRecommendations_recommendationRemovingError')?>',
        'myRecommendations_recommendationRemovingSuccess': '<?=tr('myRecommendations_recommendationRemovingSuccess')?>'
    };
</script>