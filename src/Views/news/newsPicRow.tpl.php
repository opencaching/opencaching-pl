<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>
<tr>
    <td>
      <a href="{{fullPicUrl}}" target="_blank">
        <img src="{{thumbUrl}}">
      </a>
    </td>
    <td class="center">
      <input type="text" value="{{fullPicUrl}}" readonly size="50">
    </td>
    <td class="center">
      <img src="/images/log/16x16-trash.png" onclick="removePicAction(this, '{{uuid}}')"
           class="icon16" alt="<?=tr('news_removePic')?>" title="<?=tr('news_removePic')?>">
    </td>
</tr>