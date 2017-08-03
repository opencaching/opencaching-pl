<?php
/**
 * This is pagintaion chunk.
 * To use it prepare and use PaginationModel.
 *
 * Usage:
 * - in controller:
 *      TODO:
 * - in template:
 *      TODO:
 */
use lib\Objects\ChunkModels\PaginationModel;
use Utils\Uri\Uri;

return function (PaginationModel $pagination){

// begining of chunk

    $chunkCSS = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/pagination.css');

?>

<script type='text/javascript'>
    // load pagination chunk css
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>

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
