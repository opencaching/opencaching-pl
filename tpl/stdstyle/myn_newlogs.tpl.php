
<script src="lib/js/wz_tooltip.js"></script>

<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="">
  {{my_neighborhood}} - {{new_logs_myn}}
</div>


<div class="content2-container">
  {info}

  <table class="table full-width table-striped">
    <tr>
      <th>{{date}}</th>
      <th colspan="5"></th>
      <th>{{geocache}}</th>
      <th>{{logged_by}}</th>
    </tr>
    {file_content}
  </table>
  <div class="buffer"></div>
  <p class="align-center">{pages}</p>
</div>
