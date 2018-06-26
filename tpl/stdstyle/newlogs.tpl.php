<script src="lib/js/wz_tooltip.js"></script>

<div class="content2-container">

  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/logs.png" class="icon22" alt="">
    {{latest_logs}}
    <a href="/rss/newlogs.xml">
      <img src="/tpl/stdstyle/images/misc/rss.svg" class="icon16" alt="RSS icon">
    </a>
  </div>

  <div class="align-right">
    <a class="btn btn-default btn-sm" href="/logmap.php">{{logmap_04}}</a>
  </div>

  <p>
    {pages}
  </p>

  <table class="full-width" style="border-collapse: separate; border-spacing: 2px; margin-left: 10px; line-height: 1.4em; font-size: 13px">
    <tr>
      <td><strong>{{date}}</strong></td>
      <td colspan="5"></td>
      <td><strong>{{geocache}}</strong></td>
      <td><strong>{{logged_by}}</strong></td>
    </tr>
    <tr>
      <td colspan="8"><hr></td>
    </tr>
    {file_content}
  </table>
  <div class="buffer"></div>
  <p>
    {pages}
  </p>
</div>
