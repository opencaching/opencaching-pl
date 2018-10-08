<?php
/**
 * This chunk is used to load display warning about unsupported browser
 */
return function (){
    //start of chunk
?>

<div id="unsupportedBrowserWarning">
  <h3><?=tr('main_unsuportedBrowserHead')?></h3>
  <div><?=tr('main_unsuportedBrowserWarn')?></div>
  <div><?=tr('main_unsuportedBrowserSolution')?></div>
  <div>
    <a href="https://www.mozilla.org/firefox/" title="Mozilla Firefox" class="btn">
      <img src="/images/externalLogo/browsers/MozillaFirefox.png" alt="Mozilla Firefox" class="icon32">
    </a>
    <a href="https://www.google.com/chrome/" title="Google Chrome" class="btn">
      <img src="/images/externalLogo/browsers/GoogleChrome.png" alt="Google Chrome" class="icon32">
    </a>
    <a href="https://apple.com/safari" title="Apple Safari" class="btn">
      <img src="/images/externalLogo/browsers/AppleSafari.png" alt="Apple Safari" class="icon32">
    </a>
  </div>
  <div><a id="unsupportedBrowserBtn" class="btn btn-primary"><?=tr('main_unsuportedBrowserBtn')?></a></div>
</div>

<script>
function isEs6Supported() {
    "use strict";

    if (typeof Symbol == "undefined"){
      return false;
    }
    try {
        eval("class Foo {}");
        eval("var bar = (x) => x+1");
    } catch (e) { return false; }

    return true;
}

function isUnsBrowserCookieSet(){
  return (document.cookie.indexOf("unsupportedBrowser=continue") != -1);
}

function setUnsBrowserCookie(){
  document.cookie = "unsupportedBrowser=continue";
}

if( !isEs6Supported() && !isUnsBrowserCookieSet() ){
  $('#unsupportedBrowserWarning').show();

  $('#unsupportedBrowserBtn').click(function(){
    setUnsBrowserCookie();
    $('#unsupportedBrowserWarning').hide();
  });
}

</script>
<?php
}; //end of chunk
