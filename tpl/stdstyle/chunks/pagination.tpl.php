<?php
/**
 * This is pagintaion chunk.
 * To use it prepare and use PaginationModel.
 *
 */
use lib\Objects\ChunkModels\PaginationModel;

return function (PaginationModel $pagination){


// begining of chunk
?>

<div>
  <?php if(!$pagination->error()) { ?>

      <?php foreach($pagination->getPagesList() as $page) { ?>

        <?php if($page->isActive){ ?>

        <span class="active" title="<?=$page->tooltip?>">
          --<a href="<?=$page->link?>" >
            <?=$page->text?>
          --</a>
        </span>

        <?php } else { // isActive ?>

        <span title="<?=$page->tooltip?>">
          <a href="<?=$page->link?>" >
            <?=$page->text?>
          </a>
        </span>

        <?php } // isActive ?>

      <?php } // foreach ?>

  <?php } else { // $pagination->error() ?>

  <p>Pagination error:&nbsp;<?=$pagination->getErrorMsg()?></p>

  <?php } // if $pagination->error()?>

</div>

<?php
};

// end of chunk - nothing should be added below
