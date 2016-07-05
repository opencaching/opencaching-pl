
<!-- this is temporary style definitions - I'll remove it soon -->
<style>

#cachesOffered li,
#cachesOwned li {
    font-size:1.3em;
}

a:hover, a:visited, a:link, a:active {
    text-decoration: none;
}

.badge {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;

    width: 92%;
    text-align: center;
    text-transform: uppercase;
    font-weight: bold;
    padding:1px 10px;
    margin-bottom: 10px;

    border-radius: 5px;
}

.accept {
    color: green;
}

.abort, .refuse {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;

}

.message{
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;

    width: 92%;
    text-align: center;
    padding:15px;
    margin-bottom: 10px;

    border-radius: 15px;
}

.message-err{
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

.close-but {
    position: absolute;
    right: 5%;
}
</style>

<script type='text/javascript'>
    function reload(){
      window.location = 'chowner.php';
    }
</script>

<?php if($view->errorMsg!="") { ?>
<div class="message message-err">
  <h5>
    <?= $view->errorMsg ?>
    <span class="close-but" onclick="reload()">[X]</span>
  </h5>
</div>
<?php } ?>

<?php if($view->infoMsg!="") { ?>
<div class="message">
  <h5>
    <?= $view->infoMsg ?>
    <span class="close-but" onclick="reload()">[X]</span>
  </h5>
</div>
<?php } ?>


<?php if($view->adoptionOffers) { ?>

<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/email.png" class="icon32" align="middle" />
  <b>{{adopt_10}}</b>
</div>

<div>
  <p>{{adopt_11}}</p>
</div>


<div id="cachesOffered">
  <ul>

    <?php foreach ($view->adoptionOffers as $cache) { ?>

      <li>
        <a href='viewcache.php?cacheid=<?=$cache['cache_id']?>'>
          <?=$cache['name']?><?=$cache['offeredFromUserName']?>:&nbsp;&nbsp;
        </a>
        <a href='chowner.php?cacheid=<?=$cache['cache_id']?>&action=accept'>
          <span class="badge accept">{{adopt_12}}</span>
        </a>
        <a href='chowner.php?cacheid=<?=$cache['cache_id']?>&action=refuse'>
          <span class="badge refuse">{{adopt_13}}</span>
        </a>
      </li>

    <?php } ?>

  </ul>
</div>
<?php } ?>


<!-- Caches owns by current user -->

<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/email.png" border="0" align="middle" />
  <b>{{adopt_00}}</b>
</div>

<?php if( empty($view->userCaches) ) { ?>

    <div>
      <p>{{adopt_03}}</p>
    </div>

<?php } else { ?>

    <div>
      <p>{{adopt_01}}</p>
    </div>

    <div id="cachesOwned">
      <ul>
        <?php foreach ( $view->userCaches as $cache) { ?>
          <li>
              <?php if($cache['adoptionOfferId']) { ?>
                <!-- cache offered for adoption - offer can be aborted -->
                <?=$cache['name']?>
                <a href='chowner.php?cacheid=<?=$cache['cache_id']?>&action=abort'>
                  <span class="badge abort">{{adopt_14}} -> <?=$cache['offeredToUserName']?></span>
                </a>

              <?php } else { ?>
                <!-- cache is not offered for adoption - offer can be created -->
                <a href='chowner.php?cacheid=<?=$cache['cache_id']?>&action=selectUser'>
                  <?=$cache['name']?>
                </a>
              <?php } ?>
          </li>
        <?php } ?>
      </ul>
    </div>

<?php } ?>

