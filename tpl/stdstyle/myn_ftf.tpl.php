
<script src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="">
  {{my_neighborhood}} - {{ftf_awaiting}}
</div>
<div class="content2-container">
  {info}

  <table class="table full-width table-striped">
    <tr>
      <th colspan="2">{{date_hidden_label}}</th>
            <th></th>
            <th>{{geocache}}</th>
            <th>{{owner}}</th>
    </tr>
    {file_content}
  </table>
  <div class="buffer"></div>
  <?php $view->callChunk('pagination', $view->paginationModel); ?>
</div>

