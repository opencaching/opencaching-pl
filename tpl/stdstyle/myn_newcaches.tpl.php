
<script src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="">
  {{my_neighborhood}} - {{new_caches_myn}}
</div>
<div class="content2-container">
  {info}

  <table class="table full-width table-striped">
    <tr>
      <th colspan="3">{{date_hidden_label}}</th>
            <th>{{geocache}}</th>
            <th>{{owner}}</th>
            <th colspan="3">{{latest_logs}}</th>
        </tr>
        {file_content}
    </table>
  <div class="buffer"></div>
  <?php $view->callChunk('pagination', $view->paginationModel); ?>
</div>
