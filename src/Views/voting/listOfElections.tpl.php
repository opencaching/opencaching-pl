<?php
use src\Models\Voting\Election;
use src\Utils\View\View;
use src\Utils\Text\Formatter;
?>

<div class="content2-pagetitle"><?=tr('vote_elListTitle')?></div>
<div class="content2-container">

<?php /** @var $v View */ ?>
<?php /** @var $election Election */ ?>

<?php if (empty($v->elections)) { ?>
    <p><?=tr('vote_elListNoElections')?></p>
<?php } else { // if-empty($v->elections) ?>
    <table>
    <thead>
      <tr>
        <th><?=tr('vote_elListThName')?></th>
        <th><?=tr('vote_elListThDesc')?></th>
        <th><?=tr('vote_elListThStart')?></th>
        <th><?=tr('vote_elListThEnd')?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($v->elections as $election) { ?>
    <tr>
      <td>
        <a href="/voting/election/<?=$election->getElectionId()?>"><?=$election->getName()?></a>
      </td>
      <td><?=$election->getDescription()?></td>
      <td><?=Formatter::dateTime($election->getStartDate())?></td>
      <td><?=Formatter::dateTime($election->getEndDate())?></td>
    <?php } // foreach elections ?>
    </tbody>
    </table>
<?php } // if-empty($v->elections)?>

</div>
