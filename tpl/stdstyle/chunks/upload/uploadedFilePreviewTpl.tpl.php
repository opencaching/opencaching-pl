<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="upload_previewEntry" data-filename="{{file.name}}">
  <div class="upload_previewEntryData">
      <div class="upload_previewImg"><img src="{{src}}"></div>
      <div class="upload_previewProps">
        <b>{{file.name}}</b> ({{size}})
        {{#if error}}
          <br/>
          <div class="upload_previewError">
            {{#if errorType}}<?=tr('upload_improperFileType')?>{{/if}}
            {{#if errorSize}}<?=tr('upload_improperFileSize')?>{{/if}}
          </div>
        {{/if}}
      </div>
  </div>
  <div class="upload_previewEntryRemove">✖</div>
</div>
