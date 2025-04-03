<div class="content2-pagetitle">
    <img src="/images/blue/cache.png" class="icon32" alt="" title="<?=tr('new_cache')?>">&nbsp<?=tr('nc_begin_title')?>
</div>

<div class="callout callout-info callout-newcahe-info">
  <div class="callout-title"><?=tr('nc01')?></div>
  <div class="callout-highlight">
      <?=tr('nc02')?>
  </div>
  <ul>
    <li><?=tr('nc11')?> <a class="links" href="{wiki_link_rules}"><?=tr('nc12')?></a>?</li>
    <li><?=tr('nc03')?></li>
    <li><?=tr('nc04')?></li>
    <li><?=tr('nc06')?></li>
    <li><?=tr('nc05')?></li>
    <?php if(tr('nc07') !== " "): ?>
      <li><?=tr('nc07')?></li>
    <?php endif; ?>
  </ul>
  <?php if(tr('nc13') !== " "): ?>
    <?=tr('nc13')?> <a class="links" href="{wiki_link_placingCache}"><?=tr('nc14')?></a>
  <?php endif; ?>
  <?php if(tr('nc15') !== " "): ?>
    <?=tr('nc15')?> <a class="links" href="{wiki_link_cachingCode}"><?=tr('nc16')?></a> <?=tr('nc17')?>.<br>
  <?php endif; ?>
  <?php if(tr('nc08') !== " "): ?>
    <?=tr('nc08')?> <a class="links" href="/guide"><?=tr('nc09')?></a>, <?=tr('nc10')?>.
  <?php endif; ?>

  <div class="buffer"></div>
    <?php if(tr('nc21') !== " "): ?>
      <?=tr('nc21')?>
      <ul>
        <li><?=tr('nc22')?> <a class="links" href="{wiki_link_main}"><?=tr('nc17')?></a>: <a class="links" href="{wiki_link_placingCache2}"><?=tr('nc23')?></a>, <a class="links" href="{wiki_link_placingCache}"><?=tr('nc14')?></a>, <a class="links" href="{wiki_link_cachingCode}"><?=tr('nc16')?></a></li>
        <li><?=tr('nc24')?> <a class="links" href="/guide"><?=tr('nc09')?></a> <?=tr('nc25')?></li>
        <li><?=tr('nc26')?></li>
      </ul>
    <?php endif; ?>
  <div class="buffer"></div>
  <?=tr('nc18')?><br><?=tr('nc19')?>
</div>
<div class="align-center">
  <form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr">
    <input type="hidden" name="newcache_info" value="0"/>
    <button class="btn btn-primary btn-md" type="submit"><?=tr('nc20')?></button>
  </form>
</div>
