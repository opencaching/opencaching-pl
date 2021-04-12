<?php
use src\Models\Voting\Election;
use src\Models\Voting\ChoiceOption;
use src\Utils\Text\Formatter;
use src\Utils\View\View;
use src\Models\Voting\ElectionResult;
use src\Utils\Generators\ColorGenerator;

/** @var $v View */
/** @var $el Election */
$el = $v->election;

/** @var $result ElectionResult */
$result = $v->results;

?>
<div class="content2-pagetitle"><?=$el->getName()?></div>

<div class="content2-container">
    <div class="callout callout-success">
      <?=tr('vote_electionWereOpened')?>:
      <?=Formatter::dateTime($el->getStartDate())?> - <?=Formatter::dateTime($el->getEndDate())?>
      (<?=tr('vote_timezone')?>:&nbsp;<?=$el->getEndDate()->getTimezone()->getName()?>)
      <br>
      <?=tr('vote_votersCount')?>:&nbsp;<?=$result->getVotersNum()?>
    </div>

    <p><?=$el->getDescription()?></p>
</div>

<div class="content2-container bg-blue02">
  <span class="content-title-noshade-size1"><?=tr('vote_resultsSectionTitle')?></span>
</div>

<div class="content2-container">
    <table>
    <thead>
        <tr>
          <th><?=tr('vote_choiceNameTh')?></th>
          <th><?=tr('vote_choiceDescTh')?></th>
          <th><?=tr('vote_resultsTh')?></th>
          <th><?=tr('vote_resultShareTh')?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
          <td colspan="2"></td>
          <td colspan="2"><?=tr('vote_votes')?>:&nbsp;<?=$result->getVotesNum()?></td>
        </tr>

        <?php foreach ($result->getOptionsList() as $opt) { ?>
        <tr>
          <td><?=$opt->getName()?></td>
          <td><?=$opt->getDescription()?></td>
          <td><?=$result->getOptVotesCount($opt)?></td>
          <td><?=$result->getOptPercent($opt)?> %</td>
        </tr>
        <?php } // foreach elections ?>
    </tbody>
    </table>
</div>

<div class="content2-container bg-blue02">
  <span class="content-title-noshade-size1"><?=tr('vote_chartsSectionTitle')?></span>
</div>

<div class="content2-container">
    <canvas id="resultsBarChart" height="100"></canvas>
</div>

<div class="content2-container">
    <canvas id="votesInTimeChart" height="300"></canvas>
</div>

<script>
new Chart(document.getElementById('resultsBarChart'), {
    type: 'bar',
    data: {
        // labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        labels: <?=$result->getListOfOptNamesAsJson()?>,
        datasets: [{
            //label: '<?=tr('vote_numOfVotesChartTitle')?>',
            // data: [12, 19, 3, 5, 2, 3],
            data: <?=$result->getListOfVotesCountAsJson()?>,
            backgroundColor: [
                // 'rgba(255, 99, 132, 0.2)',
                <?php foreach($result->getOptionsList() as $opt) { ?>
                  <?php $rgb = ColorGenerator::rgb($result->getColorForOption($opt));?>
                  'rgba(<?=implode(',', $rgb)?>,0.2)', /* <?=$result->getColorForOption($opt)?> */
                <?php } //foreach ?>
            ],
            borderColor: [
              // 'rgba(255, 99, 132, 1)',
              <?php foreach($result->getOptionsList() as $opt) { ?>
                <?php $rgb = ColorGenerator::rgb($result->getColorForOption($opt));?>
                'rgba(<?=implode(',', $rgb)?>,1)',
              <?php } //foreach ?>
            ],
            borderWidth: 1
        }]
    },
    options: {
        legend: {
            display: false
        },
        title:      {
            display: true,
            text:    '<?=tr('vote_numOfVotesChartTitle')?>',
        },
        scales: {
            yAxes: [{
                scaleLabel: {
                    display:     true,
                    labelString: '<?=tr('vote_numOfVotesChartAxisY')?>'
                },
                ticks: {
                    beginAtZero: true,
                    stepSize: 10
                }
            }]
        }
    }
});

// line chart of results
new Chart(document.getElementById('votesInTimeChart'), {
  type:    'line',
  data:    {
      datasets: [
          <?php /** @var $opt ChoiceOption */ ?>
          <?php foreach ($result->getOptionsList() as $opt) { ?>
          {
              label: "<?=$opt->getName()?>",
              data: <?=$result->getListOfVotesInTimeJson($opt)?>,
              fill: false,
                    <?php $rgb = ColorGenerator::rgb($result->getColorForOption($opt));?>
              borderColor: 'rgba(<?=implode(',', $rgb)?>,0.5)',
              steppedLine: 'before',
          },
          <?php } //foreach ?>
      ]
  },
  options: {
      responsive: true,
      title:      {
          display: true,
          text:    '<?=tr('vote_votesInTimeChartTitle')?>',
      },
      scales:     {
          xAxes: [{
              type:       "time",
              time:       {
                  parser: 'X',        //format of dates
                                      // (https://momentjs.com/docs/#/parsing/string-format/)
                  tooltipFormat: 'll',
                  unit: 'day',
              },
              scaleLabel: {
                  display:     false,
              }
          }], // xAxes
          yAxes: [{
              type: 'logarithmic',
              ticks: {
                  callback: function (value, index, values) {
                      return Number(value.toString());//pass tick values as a string into Number function
                  }
              },
              scaleLabel: {
                display:     true,
                labelString: '<?=tr('vote_votesInTimeChartYAxisY')?>'
              }
          }] // yAxes
      } // scales
  }
});
</script>
