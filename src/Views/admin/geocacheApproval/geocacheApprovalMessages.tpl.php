<?php

use src\Controllers\ViewCacheController;
use src\Utils\Uri\SimpleRouter;

// Here are handlebars-js templates - see https://handlebarsjs.com/ for format details
?>
<script id="geocacheApproval_acceptTemplate" type="text/x-handlebars-template">
<div class="geocacheApproval-confirmDialog-content">
    <div class="geocacheApproval-confirmDialog-content2">
        <?= tr('viewPending_13'); ?> "<a href='<?= SimpleRouter::getLink(ViewCacheController::class); ?>?cacheid={{cache_id}}'>{{cache_name}}</a>" <?= tr('viewPending_14'); ?> {{cache_owner}}.<br/>
        <?= tr('viewPending_15'); ?>.
    </div>
</div>
</script>

<script id="geocacheApproval_blockTemplate" type="text/x-jsrender">
<div class="geocacheApproval-confirmDialog-content">
    <div class="geocacheApproval-confirmDialog-content2">
        <?= tr('viewPending_18'); ?> "<a href='<?= SimpleRouter::getLink(ViewCacheController::class); ?>?cacheid={{cache_id}}'>{{cache_name}}</a>" <?= tr('viewPending_14'); ?> {{cache_owner}}.<br/>
        <?= tr('viewPending_19'); ?>.
    </div>
</div>
</script>

<script id="geocacheApproval_generalErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon">{{error_thrown}}: {{text_status}}
</p>
</script>

<script id="geocacheApproval_assignedTemplate" type="text/x-jsrender">
<p><?= tr('viewPending_07'); ?> {{assigned_username}} <?= tr('viewPending_08'); ?>.</p>
</script>

<script id="geocacheApproval_assignErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
{{#if message}}<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon">{{message}}{{/if}}
</p>
</script>

<script id="geocacheApproval_acceptedTemplate" type="text/x-jsrender">
<p class="geocacheApproval-acceptedInfo">
<img src="images/log/16x16-published.png" alt="accepted" class="geocacheApproval-icon"><?= tr('viewPending_09'); ?><br>
[ <?= tr('cache'); ?>: {{cache_name}} - {{cache_wp}}; <?= tr('owner_label'); ?>: {{cache_owner}}; <?= tr('cache_approval_changed_time'); ?>: {{updated}} ]
</p>
</script>

<script id="geocacheApproval_acceptErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon"><?= tr('viewPending_10'); ?>
{{#if message}}<br>[ {{message}} ]{{/if}}
</p>
</script>

<script id="geocacheApproval_rejectedTemplate" type="text/x-jsrender">
<p class="geocacheApproval-rejectedInfo">
<img src="images/log/16x16-trash.png" alt="rejected" class="geocacheApproval-icon"><?= tr('viewPending_11'); ?><br>
[ <?= tr('cache'); ?>: {{cache_name}} - {{cache_wp}}; <?= tr('owner_label'); ?>: {{cache_owner}}; <?= tr('cache_approval_changed_time'); ?>: {{updated}} ]
</p>
</script>

<script id="geocacheApproval_rejectErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon"><?= tr('viewPending_12'); ?>
{{#if message}}<br>[ {{message}} ]{{/if}}
</p>
</script>
