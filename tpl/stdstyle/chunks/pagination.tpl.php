<?php
/**
 * This is pagintaion chunk.
 * To use it prepare and use PaginationModel.
 *
 * Usage:
 * - in controller:
 *
 * - in template:
 *
 */
use lib\Objects\ChunkModels\PaginationModel;

return function (PaginationModel $pagination){


// begining of chunk
?>

<style>
    .pagination {
        margin: 10px 0;
        padding: 0px;
        display: flex;
        font-size: 12px;
        text-align: center;
        justify-content: center;
    }

    .pagination li {
        display: inline;
        box-sizing: border-box;
    }

    .pagination a {
        box-sizing: border-box;
        padding: 6px;
        margin-left: -1px;
        border: 1px solid #ccc;
        display: block;
        min-width: 30px;
    }

    .pagination li:first-child > a {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .pagination li:last-child > a {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .pagination a,
    .pagination a:visited {
        color: #337ab7;
    }

    .pagination a:hover {
        color: #23527c;
        background-color: #ddd;
    }

    .pagination a,
    .pagination a:hover,
    .pagination a:visited {
        text-decoration: none;
    }

    .pagination a.active,
    .pagination a.active:hover,
    .pagination a.active:visited {
        background-color: #337ab7;
        color: #fff;
    }

</style>

<div>
  <?php if(!$pagination->error()) { ?>
    <ul class="pagination">
      <?php foreach($pagination->getPagesList() as $page) { ?>

        <?php if($page->isActive){ ?>
        <li>
            <a class="active" href="<?=$page->link?>" title="<?=$page->tooltip?>">
                <?=$page->text?></a></li>

        <?php } else { // isActive ?>

        <li>
            <a href="<?=$page->link?>" title="<?=$page->tooltip?>">
                <?=$page->text?></a></li>

        <?php } // isActive ?>

      <?php } // foreach ?>
    </ul>
  <?php } else { // $pagination->error() ?>

  <p>Pagination error:&nbsp;<?=$pagination->getErrorMsg()?></p>

  <?php } // if $pagination->error()?>

</div>

<?php
};

// end of chunk - nothing should be added below
