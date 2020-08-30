<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>
<tr>
    <td>
      <input name="cachePicUuid" type="hidden" value="{{uuid}}">
      <img class="picSortHdlr icon16" src="/images/icons/arrowUpDown.svg"
           title="<?=tr('editCache_changePicsOrder')?>" alt="<?=tr('editCache_changePicsOrder')?>">
      <span></span>
    </td>
    <td>
      <a href="{{fullPicUrl}}" target="_blank">
        <img src="{{thumbUrl}}">
      </a>
    </td>
    <td>
      <input type="text" value="{{title}}" disabled />
      <img src="/images/actions/edit-16.png" onclick="editPicTitleAction(this, '{{uuid}}')"
           class="icon16" alt="<?=tr('editCache_editPicTitle')?>" title="<?=tr('editCache_editPicTitle')?>">
    </td>
    <td>
      <input type="checkbox" onclick="picSpolerAction(this,'{{uuid}}')" {{#if isSpoiler}}checked{{/if}}>
      <span></span>
    </td>
    <td>
      <input type="checkbox" onclick="picHideAction(this,'{{uuid}}')" {{#if isHidden}}checked{{/if}}>
      <span></span>
    </td>
    <td class="center">
      <img src="/images/log/16x16-trash.png" onclick="removePicAction(this, '{{uuid}}')"
           class="icon16" alt="<?=tr('editCache_removePic')?>" title="<?=tr('editCache_removePic')?>">
    </td>
</tr>