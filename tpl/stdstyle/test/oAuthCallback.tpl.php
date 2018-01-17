
<h1>This is debug oAuth callback for cooperation with <?=$view->service?> services</h1>
This is here for debug purpose only.

<?php if($view->error) { ?>

  <div>
    <h2 class="fail">FAIL!</h2>
    <h3>ERROR: <?=$view->errorDesc?></h3>
  </div>

<?php }else{ //if-error ?>
  <div>
    <h2 class="success">SUCCESS!</h2>
    <h3>Data retiverd from <?=$view->service?></h3>
    <ul>
      <li>Username: <?=$view->oAuthObj->getUserName()?></li>
      <li>Email: <?=$view->oAuthObj->getEmail()?></li>
      <li>External ID: <?=$view->oAuthObj->getExternalId()?></li>
    </ul>
  </div>
<?php } //if-error ?>

<a class="btn" href="/Test/oAuth">Back to the begining...</a>