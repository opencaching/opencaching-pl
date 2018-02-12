
<!--    CONTENT -->
<div class="content2-container">
    <div class="content2-pagetitle">
      <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{title_text}" title="{title_text}">
      &nbsp;{{statistics_users}} {username}
    </div>

    <div class="nav4">

        <ul id="statmenu">
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/viewprofile.php?userid=<?=$view->userId?>">
              <?=tr('general_stat')?>
            </a>
          </li>

          <?php if(isset($view->displayFindStats)) { ?>
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/ustatsg2.php?userid=<?=$view->userId?>">
              <?=tr('graph_find')?>
            </a>
          </li>
          <?php } //if-displayFindStats ?>

          <?php if(isset($view->displayCreatedStats)) { ?>
          <li class="group">
            <a style="background-image: url(images/actions/stat-18.png);background-repeat:no-repeat;"
               href="/ustatsg1.php?userid=<?=$view->userId?>">
              <?=tr('graph_created')?>
            </a>
          </li>
          <?php } //if-displayFindStats ?>

        </ul>

    </div>

    {content}
</div>

