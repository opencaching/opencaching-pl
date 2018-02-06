
<h3>HTML to test cleaning</h3>
<form action="" method="post">

  <textarea cols="100" rows="15" name="html"><?=$view->html?></textarea>
  <br/>
  <input type="submit" />
</form>

<?php if(!empty($view->errorHTML)) { ?>
    <h3>Error:</h3>
    <?=$view->errorHTML?>
    <hr/>
<?php } //if-empty(errorHTML) ?>

<?php if(!empty($view->cleanedHTML)) { ?>
  <h3>Cleaned HTML:</h3>

  <hr/>
  <pre>
    <?=$view->cleanedHTML?>
  </pre>
  <hr/>

  <h3>Result of cleaned HTML:</h3>
  <hr/>
    <?=$view->rawCleanedHtml?>
  <hr/>

<?php } //if-empty-cleanedHTML ?>