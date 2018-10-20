<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="col">
    <div class="row searchResult" >
        <span>{{name}}</span>
        {{#if countryCode}}
            <span>({{countryCode}}{{#if region}}, {{region}}{{/if}})</span>
        {{else}}
            {{#if region}}
                <span>({{region}})</span>
            {{/if}}
        {{/if}}
    </div>
</div>
