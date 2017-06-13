<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/Badge.css" />
<link rel="stylesheet" href="tpl/stdstyle/js/PieProgress/dist/css/asPieProgress.css">
<script type="text/javascript" src="tpl/stdstyle/js/PieProgress/js/jquery.js"></script>
<script type="text/javascript" src="tpl/stdstyle/js/PieProgress/dist/jquery-asPieProgress.js"></script>

  <script type="text/javascript">
    jQuery(function($) {
      $('.Badge-pie-progress').asPieProgress({
        namespace: 'pie_progress'
      });

      $('.pie_progress').asPieProgress('start');

    });

  </script>

<div class="content2-pagetitle">
<img src="tpl/stdstyle/images/blue/merit_badge.png" class="icon32" alt="" title="" align="middle" />&nbsp;
{{merit_badges}}
</div>

<br>
<br>
<div>
{content}
</div>