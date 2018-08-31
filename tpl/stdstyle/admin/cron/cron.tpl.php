<?php
use Utils\Uri\Uri;
use lib\Objects\Cron\CronCommons;
?>
<div class="content2-pagetitle">
  <?php print tr('cron_status_title') ?>
</div>
<div class="content2-container">
<div id="cronStatus">
    <div class="refresh-bar-outline">
        <div is="refresh-bar" :tr="tr" :date-time-format="dateTimeFormat"></div>
    </div>
    <div class="ctrl-bar">
        <div is="refresh-suspend" :tr="tr" :parent-method="toggleSuspend"></div>
    </div>
    <div
        is="cron-status-views"
        :views="views"
        :tr="tr"
        :default-selected="'cron-time-based-view'"
    >
    </div>
</div>
<script
    src="<?php print Uri::getLinkWithModificationTime('/tpl/stdstyle/admin/cron/cron.js')?>">
</script>
<script>
$(function() {
    var sections = [
        '<?php print CronCommons::SECTION_SPECIALS?>',
        '<?php print CronCommons::SECTION_TASKS?>'
    ];

    var data = {
        tr: {
<?php
foreach ($view->trs as $key => $tr) {
    print "'" . substr($key, $view->trPrefixLen) . "': '" . $tr . "',\n";
}
?>
        },
        dateTimeFormat: '<?php print $view->dateTimeFormat ?>',
        refreshInterval: <?php print $view->refreshInterval ?>,
        views: [
            {
                'name': 'cron-time-based-view',
                'title': 'view_time_based',
                'parameters': {
                    'sections': sections
                }
            },
            {
                'name': 'cron-task-based-view',
                'title': 'view_task_based',
                'parameters': {
                    'sections': sections
                }
            },
            {
                'name': 'cron-result-based-view',
                'title': 'view_result_based',
                'parameters': {
                    'sections': sections
                }
            }
        ]
    }

   Object.freeze(data);

    var cronStatusApp = new Vue({
        el: '#cronStatus',
        store: store,
        data: data,
        mounted: function() {
            this.$nextTick(function () {
                this.$store.dispatch(
                    'updateRefreshInterval',
                    this.refreshInterval
                );
                this.$store.dispatch('updateStatus');
                this.tick();
            })
        },
        computed: {
            suspended: function() {
                return this.$store.state.suspended;
            },
            timeRemaining: function() {
                return this.$store.state.timeRemaining;
            },
            lastSuspend: function() {
                return this.$store.state.lastSuspend;
            }
        },
        watch: {
            timeRemaining: function() {
                if (
                    typeof(this.timeRemaining) == 'undefined'
                    || this.timeRemaining > 0
                ) {
                    setTimeout(this.tick, 1000);
                } else {
                    this.$store.dispatch('updateStatus');
                }
            },
            lastSuspend: function() {
                if (this.suspended) {
                    setTimeout(this.tick, 1000);
                }
            }
        },
        methods: {
            tick: function() {
                if (this.suspended) {
                    this.$store.dispatch('updateLastSuspend', new Date());
                } else {
                    this.$store.dispatch('updateNow', new Date());
                }
            },
            toggleSuspend: function() {
                this.$store.dispatch(
                    'updateSuspended',
                    [ !this.suspended, new Date() ]
                );
            }
        }
    });

});
</script>
</div>
