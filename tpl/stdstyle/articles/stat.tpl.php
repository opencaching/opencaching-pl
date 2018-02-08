<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
  google.charts.load('current', {'packages':['corechart'], 'language' : '{language4js}'});
  google.charts.setOnLoadCallback(drawChartCacheType);
  google.charts.setOnLoadCallback(drawChartCachesFound);
  function drawChartCacheType() {
        var data = new google.visualization.DataTable({cachetype_chart_data});
        var options = {'title': '{{oc_stat}}',
                       'legend':{
                                 'alignment': 'center'
                                },
                       'height': '450',
                       'pieHole': '0.3'};
        var chart = new google.visualization.PieChart(document.getElementById('chart_cachetype_div'));
        chart.draw(data, options);
      }
  function drawChartCachesFound() {
      var data = new google.visualization.DataTable({cachesfound_chart_data});
      var options = {
                     'height': '400',
                     'titlePosition': 'in',
                     'animation':{
                         'duration': '1000',
                         'startup': true
                         },
                     'legend':{
                               'position': 'bottom'
                               },
                     'series': {
                                0: {targetAxisIndex: 0},
                                1: {targetAxisIndex: 1}
                                },
                     'vAxes': {
                               0: {title: '{{graph_statistics_02}}'},
                               1: {title: '{{graph_statistics_03}}'}
                               }
                     };
      var formatter = new google.visualization.DateFormat({pattern: 'MMM yyyy'});
      formatter.format(data, 0);
      var chart = new google.visualization.LineChart(document.getElementById('chart_cachesfound_div'));
      chart.draw(data, options);
    }

</script>

  <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">&nbsp;{{statistics}}</div>
  <div class="content2-container-1col">
    <p class="content-title-noshade-size2">{{user_ranking}}</p>
    <div class="content-txtbox-noshade">
      <ul class="indent tick">
        <li><a class="links" href="articles.php?page=s102&init=1&stat=NumberOfFinds">{{ranking_by_number_of_finds_new}}</a></li>
        <li><a class="links" href="articles.php?page=s1">{{ranking_by_number_of_created_active_caches}}</a></li>
        <li><a class="links" href="articles.php?page=s1b">{{ranking_by_number_of_created_caches}}</a></li>
        <li><a class="links" href="articles.php?page=s6">{{ranking_by_number_of_recommnedations}}</a></li>
        <li><a class="links" href="articles.php?page=s3">{{user_ranking_by_number_of_finds_of_their_caches}}</a></li>
        <li><a class="links" href="articles.php?page=s2">{{ranking_by_number_of_finds}}</a></li>
        <li><a class="links" href="articles.php?page=s102&init=1&stat=MaintenanceOfCaches">{{ranking_by_maintenace}}</a></li>
      </ul>
    </div>
    <p class="content-title-noshade-size2">{{cache_ranking}}</p>
    <div class="content-txtbox-noshade">
      <ul class="indent tick">
        <li><a class="links" href="articles.php?page=s4">{{cache_ranking_by_number_of_finds}}</a></li>
        <li><a class="links" href="articles.php?page=s11a">{{cache_ranking_by_finds_per_region}}</a></li>
          <?php
          if ($usr !== false) {
              echo '<li><a class="links" href="cacheratings.php">{{cache_ranking_by_number_of_recommendations}}</a></li>';
          }
          ?>
        <li><a class="links" href="articles.php?page=s5">{{cache_ranking_by_calculated_indicator}}</a></li>
      </ul>
    </div>
    <p class="content-title-noshade-size2">{{region_ranking}}</p>
    <div class="content-txtbox-noshade">
      <ul class="indent tick">
        <li><a class="links" href="articles.php?page=s7">{{number_of_caches_by_region}}</a></li>
        <li><a class="links" href="articles.php?page=s8">{{activity_by_region}}</a></li>
      </ul>
    </div>
  </div>
  <p class="content-title-noshade-size2">{{rise_charts}}</p>
  <div class="content-txtbox-noshade">
    <div id="chart_cachesfound_div"></div>
    <div class="buffer"></div>
    <div id="chart_cachetype_div"></div>
  </div>
