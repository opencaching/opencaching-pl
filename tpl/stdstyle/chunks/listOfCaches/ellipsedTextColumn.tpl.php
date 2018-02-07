<?php

use Utils\Generators\TextGen;

/**
 * This class just display text returned by dataRowExtractor.
 * If text is longer it will be trimmed to $maxChars + popup will be generated on click
 *
 * Example:
 *
 * $model->addColumn(
 *      new Column_EllipsedText(
 *              tr('columnTitle'),
 *              function($row){
 *                 return [
 *                   'text' => '<text-to-display',
 *                   'maxChars' => '<max-chars-to-display>'
 *                 ];
 *              }
 *      )
 * );
 *
 */

return function ($data){

    if( strlen($data['text']) > $data['maxChars'] ){
        //trim the text
        $text = substr($data['text'], 0, $data['maxChars']).'...';
        $fullText = $data['text'];
    }else{
        $text = $data['text'];
        $fullText = '';
    }

    $popupId = 'elipsed_'.TextGen::randomText(12);
?>

  <?php if(!empty($fullText)){ ?>

    <div onclick="showLightPopup(this, '<?=$popupId?>')">

  <?php } //if-empty-fulltext ?>

      <div><?=$text?></div>

  <?php if(!empty($fullText)){ ?>
      <div class="btn btn-xs" ><?=$data['labelShow']?></div>
      <div id="<?=$popupId?>" class="lightPopupHidden">
        <div class="popupClose">&#10006;</div>
        <div class="popupContainer"><?=$fullText?></div>
      </div>
    </div>
  <?php } //if-empty-fulltext ?>
<?php

};

