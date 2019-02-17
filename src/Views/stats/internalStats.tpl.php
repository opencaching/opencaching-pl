
<h1>Internal stats for year <?=$view->year?></h1>

<h2>New users (all users):</h2>
<div>
  <table>
  <?php foreach ($view->allNewUsersPerMonth as $month=>$usersNumber) { ?>
    <tr><td><?=$month?></td><td><?=$usersNumber?></td></tr>
  <?php } //foreach month ?>
  </table>
</div>

<h2>New users (activated):</h2>
<div>
  <table>
  <?php foreach ($view->newActiveUsersPerMonth as $month=>$usersNumber) { ?>
    <tr><td><?=$month?></td><td><?=$usersNumber?></td></tr>
  <?php } //foreach month ?>
  </table>
</div>

<h2>New caches:</h2>
<div>
  <table>
  <?php foreach ($view->newCachesPerMonth as $month=>$usersNumber) { ?>
    <tr><td><?=$month?></td><td><?=$usersNumber?></td></tr>
  <?php } //foreach month ?>
  </table>
</div>



