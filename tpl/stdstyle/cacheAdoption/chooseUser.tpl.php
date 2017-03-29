<link rel="stylesheet" href="<?=$view->cacheAdoption_css?>">

<div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/email.png" class="icon32" align="middle" alt="email" />
    {{adopt_04}}
    <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>
</div>

<form action="chowner.php?action=addAdoptionOffer" method="post">
    <div>
      {{adopt_05}}
    </div>

    <div class="alertMsg">
      {{adopt_06}}
    </div>

    <div>
        <label for="username">{{adopt_07}}</label>
        <input id="username" type="text" size="30" name="username" />
        <input type="submit" class="btn btn-sm btn-primary" value="{{adopt_08}}" />
    </div>

    <input type="hidden" name ="cacheid" value="{cacheid}" />
</form>
