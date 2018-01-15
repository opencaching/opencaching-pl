<?php foreach($view->feeds as $feedName => $feedPosts) { ?>
  <div class="feedArea">
    <p class="content-title-noshade-size3"><?=tr('feed_'.$feedName)?></p>
    <ul class="feedList">
      <?php foreach($feedPosts as $post){ ?>
          <li>
            <?=$post->date?>
            <a class="links" href="<?=$post->link?>">
              <?=$post->title?>
            </a>
            (<?=$post->author?>)
          </li>
      <?php } //foreach-feedPosts ?>
    </ul>
  </div>
<?php }//foreach-feeds ?>
