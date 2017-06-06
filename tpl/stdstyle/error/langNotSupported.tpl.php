<?php
use Utils\Uri\Uri;

/**
 * This template is displayed when user request not supported language
 * so it is always in English
 */
?>

<script type='text/javascript'>
    // load page css
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$view->localCss?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>


<?php $view->callChunk('infoBar', null, null,
    "Error: The specified language ($view->requestedLang) is not supported!" ); ?>

<div id="language-select-div">
    <h5>
        Please select one of supported language versions:
    </h5>
        <?php foreach($view->allLanguageFlags as $langFlag){ ?>
            <a rel="nofollow" href="<?=$langFlag['link']?>" />
              <img class="img-navflag" src="<?=$langFlag['img']?>" alt="<?=$langFlag['name']?> version"
                   title="<?=$langFlag['name']?> version">&nbsp;<?=$langFlag['name']?>&nbsp;
            </a>
        <?php } //forach-lang-flags ?>


    <h5>
        If you would like to help us traslate opencaching to other languages feel free to
        <a href="/articles.php?page=contact">contact us!</a>
    </h5>
</div>


