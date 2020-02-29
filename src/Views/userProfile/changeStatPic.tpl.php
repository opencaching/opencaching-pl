<div class="content2-container">

  <div class="content2-pagetitle">{{choose_statpic}}</div>

  <p class="content-title-noshade-size2">{{statpic_previews}}:</p>

  <form action="/UserProfile/saveStatPicSelection" method="post" enctype="application/x-www-form-urlencoded">

    <div>
      {{user_statpic_text}}:
      <input type="text" name="statpic_text" maxlength="30" value="<?=$v->statPicText?>" class="form-control input200"/>
      {{statpic_text_message}}
    </div>
    <table>
    <?php foreach ($v->allStatPics as $statPic) { ?>
        <tr>
          <td>
            <p class="content-title-noshade"><?=$statPic->getDescription()?></p>
          </td>
          <td>
            &nbsp;
            <input type="radio" name="statpic_logo" class="radio" value="<?=$statPic->getId()?>"
            <?=($v->statPicLogo == $statPic->getId())?'checked':''; ?> />
            &nbsp;
            <img src="/<?=$statPic->getPreviewPath()?>" />
          </td>
        </tr>
    <?php } //foreach statPic ?>
    </table>

    <div>
      <input type="reset" name="reset" value="{{reset}}" class="btn btn-default"/>&nbsp;&nbsp;
      <input type="submit" name="submit" value="{{change}}" class="btn btn-primary"/>
    </div>

  </form>
</div>
