
<div class="content2-container">

    <div class="content2-pagetitle">
      <?=tr('myNotes_title')?>
    </div>

    <?php if($view->rowCount > 0) { ?>
        <div class="content2-container">
          <?php $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);?>
        </div>
    <?php } else { //$view->cachesCount == 0 ?>
        <div>
            <?=tr('myNotes_emptyList')?>
        </div>
    <?php } //$view->cachesCount == 0 ?>

</div>


