<?php

use Utils\Uri\Uri;
use lib\Objects\Cron\CronCommons;
?>
<div class="content2-pagetitle">
  Cron Status
</div>
<div class="content2-container">
<div id="cronStatus">
    <div class="refresh-bar-outline">
        <div is="refresh-bar" :tr="tr" :date-time-format="dateTimeFormat"></div>
    </div>
    <div class="ctrl-bar">
        <div is="refresh-suspend" :tr="tr" :parent-method="toggleSuspend"></div>
    </div>
    <div is="cron-status-views" :views="views" :tr="tr" defaultSelected="cron-task-based-view"></div>
</div>
<script src="<?=Uri::getLinkWithModificationTime('/tpl/stdstyle/admin/cron/cron.js')?>"></script>
<script>
$(function() {
    var sections = {
        '<?php print CronCommons::SECTION_SPECIALS?>': '<?php print CronCommons::SECTION_SPECIALS?>',
        '<?php print CronCommons::SECTION_TASKS?>': '<?php print CronCommons::SECTION_TASKS?>'
    };
    
    var data = {
        tr: {
            'last_refresh': 'Ostatnio odswieżono',
            'next_refresh': 'Następne odświeżenie',
            'next_refresh_in': 'Następne odświeżenie za',
            'sus_ctrl_suspend': 'Zatrzymaj',
            'sus_ctrl_continue': 'Kontynuuj',
            'view_task_based': 'Widok wg zadań',
            'view_time_based': 'Widok wg czasu',
            'view_result_based': 'Widok wg wyników',
            'no_tasks': 'Brak elementów do wyświetlenia',
            'entrypoint_label': 'ZADANIE',
            'section': 'Sekcja',
            'entrypoint': 'Zadanie',
            'cron_string': 'Definicja cron',
            'description': 'Opis zadania',
            'max_history': 'Maks. rozmiar historii',
            'ttl': 'Maks. czas',
            'current': 'Bieżące',
            'history': 'Historyczne',
            'uuid': 'UUID',
            'scheduled_time': 'Czas zaplanowany',
            'start_time': 'Czas rozpoczęcia',
            'end_time': 'Czas zakończenia',
            'result': 'Wynik',
            'failed': 'Błąd',
            'yes': 'Tak',
            'no': 'Nie',
            'success': 'Sukces',
            'failure': 'Porażka',
            'unknown': 'Nieznany',
            'output': 'Tekst',
            'error_msg': 'Komunikat',
            'result_errors': 'Błędy',
            'result_successes': 'Sukcesy',
            'result_failures': 'Porażki',
            'result_unknowns': 'Inne',
            'summary_tasks': 'Łącznie',
            'summary_results': 'Wyniki',
            'summary_results_ok': 'Sukcesy',
            'summary_results_failed': 'Porażki',
            'summary_results_unknown': 'Nieokreślone',
            'summary_errors': 'Błędy',
            'summary_durations': 'Czasy działania',
            'summary_duration_min': 'Minimalny',
            'summary_duration_max': 'Maksymalny',
            'summary_duration_avg': 'Średni'
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
    
   // Object.freeze(data);
    
    var cronStatusApp = new Vue({
        el: '#cronStatus',
        store: store,
        data: data,
        mounted: function() {
            this.$nextTick(function () {
                this.$store.dispatch('updateRefreshInterval', this.refreshInterval);
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