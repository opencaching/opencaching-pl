
<script src="lib/js/wz_tooltip.js"></script>

<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="">
  {{my_neighborhood}} - {{top_recommended}}
</div>

{info}

<div class="content2-container">
  <table class="table full-width table-striped">
    <tr>
      <th>{{date_hidden_label}}</th>
      <th><img src="images/rating-star.png" alt="{{recommendations}}"></th>
      <th></th>
      <th colspan="2">{{geocache}}</th>
      <th>{{owner}}</th>
      <th colspan="3">{{latest_logs}}</th>
    </tr>
    {file_content}
  </table>
  <div class="buffer"></div>
  <p class="align-center">{pages}</p>
</div>
