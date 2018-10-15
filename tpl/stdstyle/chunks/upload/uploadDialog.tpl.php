<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>
<div id="upload_dialog">
<div id="upload_btnClose" class="btn btn-sm">✖</div>
<div id="upload_boxParent">
  {{#if dialog.title}}
  <h3>{{dialog.title}}</h3>
  {{/if}}

  {{#if dialog.preWarning}}
  <div class="callout callout-warning">{{dialog.preWarning}}</div>
  {{/if}}

  {{#if dialog.preInfo}}
  <div class="callout callout-info">{{dialog.preInfo}}</div>
  {{/if}}


  <div id="upload_box">
    <input type="file" id="upload_fileInput" name="fileInput[]"
           {{#if multiplyFilesAllowed}}multiple="multiple"{{/if}}
           {{#if allowedTypesRegex}}accept="{{allowedTypesRegex}}"{{/if}}/>

    <div id="upload_dragFileBox">
      <div><h3><?=tr('upload_clickOrDropFile')?></h3></div>
      <div>
          {{#if multiplyFilesAllowed}}
          <?=tr('upload_allowedNumberOfFiles')?>: {{maxFilesNumber}}<br/>
          {{/if}}

          {{#if allowedTypesRegex}}
          <?=tr('upload_allowedTypesOfFile')?>: {{allowedTypesRegex}}<br/>
          {{/if}}
          <?=tr('upload_maxFileSize')?>: {{formattedMaxFileSize}}
      </div>
    </div>

    <div id="upload_progressBar">
      <div id="upload_progressDone"></div>
    </div>

  </div>

  <div id="upload_previewBox"></div>

  <div id="upload_btnBox">
    <div id="upload_uploadBtn" class="btn btn-primary btn-disabled"><?=tr('upload_startUpload')?></div>
  </div>
</div>
</div>
