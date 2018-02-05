
<script src="lib/js/wz_tooltip.js"></script>

<div class="content2-container">

    <div class="content2-pagetitle">
      <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache">
      &nbsp;{{newcaches_label}}
    </div>

    <div class="align-right">
        <a class="btn btn-default btn-sm" href="/newcachesrest.php">
          <?=$view->cachesOutsideOfCountry?>
        </a>
    </div>

    <!-- Text container -->
    <p>
        {pages}
    </p>
    <table style="border-collapse: separate; border-spacing: 2px; line-height: 1.4em; font-size: 13px; ">
        <tr>
            <td><strong>{{date}}</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>{{geocache}}</strong></td>
            <td><strong>{{region}}</strong></td>
            <td><strong>{{owner}}</strong></td>
        </tr>
        <tr>
            <td colspan="9"><hr></td>
        </tr>
        {newcaches}
        <tr>
            <td colspan="9"><hr></td>
        </tr>
    </table>
    <p>
        {pages}
    </p>
    <!-- End Text Container -->

</div>