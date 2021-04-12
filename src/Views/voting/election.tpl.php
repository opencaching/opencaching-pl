<?php
use src\Models\Voting\Election;
use src\Models\Voting\ChoiceOption;
use src\Utils\Text\Formatter;
use src\Utils\View\View;

/**
 Main voting view

 state vars:
 - enableVoting - voting is active and user can send vote on server
 - alreadyVoted - user has voted => inactive voting + callout info
 - votingCriteriaConflict - user can't vote because of voting criteria => inactive voting + callout info

 */

/** @var $v View */
/** @var $el Election */
$el = $v->election;

/** @var $opt ChoiceOption */

?>
<div class="content2-pagetitle"><?=$el->getName()?></div>
<div class="content2-container">

    <div class="callout callout-warning">
        <?php if (isset($v->enableVoting)) { ?>
          <?=tr('vote_electionOpenUntil')?>: <b><?=Formatter::dateTime($el->getEndDate())?></b>
          (<?=tr('vote_timezone')?>:&nbsp;<?=$el->getEndDate()->getTimezone()->getName()?>)
        <?php } else { //!if (isset($v->enableVoting))?>
          <?=tr('vote_electionWillBeOpen')?>:
          <b><?=Formatter::dateTime($el->getStartDate())?> - <?=Formatter::dateTime($el->getEndDate())?></b>
          (<?=tr('vote_timezone')?>:&nbsp;<?=$el->getEndDate()->getTimezone()->getName()?>)
        <?php } ?>
    </div>

    <p><?=$el->getDescription()?></p>

    <?php if (isset($v->votingCriteriaConflict)) { ?>
    <div class="callout callout-danger"><?=tr('vote_criteriaNotPassed')?></div>
    <?php } //if-!isset($v->voterCriteriaPassed) ?>

</div>

<div class="content2-container bg-blue02">
  <span class="content-title-noshade-size1"><?=tr('vote_')?></span>
</div>

<div class="content2-container">
    <?php if (isset($v->alreadyVoted)) { ?>
    <div class="callout callout-danger"><?=tr('vote_alreadyVoted')?></div>
    <?php } //if-!isset($v->voterCriteriaPassed) ?>

    <form id="votingForm" class="form">
        <table>
            <thead>
                <tr>
                  <th></th>
                  <th><?=tr('vote_choiceNameTh')?></th>
                  <th><?=tr('vote_choiceDescTh')?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($v->optionsArr as $opt) { ?>
                <?php $optStr = "opt_".$opt->getOptionId() ?>
                <tr>
                <td>
                    <input class="" id="<?=$optStr?>" type="checkbox" name="votes[]" value="<?=$opt->getOptionId()?>"
                           <?=!isset($v->enableVoting)?'disabled':''?>>
                </td>
                <td>
                    <label for="<?=$optStr?>">
                      <?php if ($opt->getLink()) { ?>
                        <a href="<?=$opt->getLink()?>"><?=$opt->getName()?></a>
                      <?php } else { // if-$opt->getLink ?>
                        <?=$opt->getName()?>
                      <?php } // if-$opt->getLink ?>
                    </label>
                </td>
                <td><?=$opt->getDescription()?></td>
                </tr>
            <?php } // foreach elections ?>
            </tbody>
        </table>

        <div>
          <?php if (isset($v->enableVoting)) { ?>
              <button type="button" class="btn btn-primary btn-lg" onclick="submitFormOverAjax()">
          <?php } else { ?>
              <button type="button" class="btn btn-disabled btn-lg" disabled>
          <?php } ?>
                <?=tr('vote_saveMyVote')?>
              </button>
        </div>
        <div id="errorCallout"></div>
    </form>
</div>

<script type="text/javascript">

  function submitFormOverAjax() {

    if (!validateVotesForm()) {
      return;
    }

    var dataArr = $('#votingForm').serializeArray();

    $.ajax({
      type:  "post",
      cache: false,
      data: $.param(dataArr),
      url:   "/voting/saveVote/<?=$el->getElectionId()?>"
    }).fail( function( jqXHR, textStatus, errorThrown ) {
          console.debug("submitFormOverAjax: " + jqXHR.responseText);
          var jsonResponse = JSON.parse(jqXHR.responseText);
          displayError (jsonResponse.message);
    }).done( function( data, textStatus, jqXHR ) {
        console.log("Ajax done: ", data, textStatus);

        var errorDiv = $("#errorCallout");
        errorDiv.empty();
        errorDiv.html('<?=tr('vote_saveResultSuccess')?>');
        errorDiv.removeClass("callout callout-danger");
        errorDiv.addClass("callout callout-success");

        $('#votingForm button').removeClass("btn-primary").addClass("btn-disabled")
          .prop("onclick", null).off("click")
    });
  }

  // check if try to save validate vote (check election rules)
  function validateVotesForm () {
    var maxNumOfVotesAllowed = <?=json_encode($el->getMaxAllowedNumOfVotes())?>;
    var emptyVotesDisallowed = <?=json_encode($el->isEmptyVoteDisallowed())?>;
    var partialVoteDisallowed = <?=json_encode($el->isPartialVoteDisallowed())?>;

    // how many options are selected
    var selectedOptions =  $("input[name='votes[]']:checkbox:checked").length;

    //class="callout callout-danger"
    if (maxNumOfVotesAllowed && selectedOptions > maxNumOfVotesAllowed) {
      // too many options selected
       displayError('<?=tr('vote_tooManyOptionsSelected')?>'+": "+maxNumOfVotesAllowed);
      return false;
    }

    if (emptyVotesDisallowed && selectedOptions == 0) {
      // empty vote
      displayError ('<?=tr('vote_noOptionsSelected')?>');
      return false;
    }

    if (maxNumOfVotesAllowed && emptyVotesDisallowed && selectedOptions < maxNumOfVotesAllowed) {
      // not enough options
      displayError ('<?=tr('vote_notEnoughOptionsSelected')?>'+": "+maxNumOfVotesAllowed);
      return false;
    }

    displayError ();
    return true;
  }

  function displayError(errorMsg) {
    var errorDiv = $("#errorCallout");
    errorDiv.empty();
    errorDiv.html(errorMsg);
    errorDiv.removeClass("callout callout-danger");
    if (errorMsg) {
      errorDiv.addClass("callout callout-danger");
    }
  }
</script>

