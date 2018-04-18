if (!String.prototype.padStart) {
    String.prototype.padStart = function padStart(targetLength,padString) {
        targetLength = targetLength>>0;
        padString = String(
            (typeof padString !== 'undefined' ? padString : ' ')
        );
        if (this.length > targetLength) {
            return String(this);
        }
        else {
            targetLength = targetLength-this.length;
            if (targetLength > padString.length) {
                padString += padString.repeat(targetLength/padString.length);
            }
            return padString.slice(0,targetLength) + String(this);
        }
    };
}

var sectionMixin = {
    methods: {
        getSectionSummary: function(name) {
            let result = {};
            let summaries = this.$store.state.summaries;
            if (
                typeof(summaries) != 'undefined'
                && typeof(summaries[name]) != undefined
                && typeof(summaries[name]['summary']) != undefined
            ) {
                result = summaries[name]['summary'];
            }
            return result;
        },
        getEPSummary: function(sectionName, entryPoint) {
            let result = {};
            let summaries = this.$store.state.summaries;
            if (
                typeof(summaries) != 'undefined'
                && typeof(summaries[sectionName]) != undefined
                && typeof(summaries[sectionName]['entrypoints']) != undefined
                && typeof(summaries[sectionName]['entrypoints'][entryPoint])
                    != undefined
            ) {
                result = summaries[sectionName]['entrypoints'][entryPoint];
            }
            return result;
        }
    }
};

var taskSetMixin = {
    methods: {
        sortTasks: function(tasks) {
            tmp = [];
            for (let uuid in tasks) tmp.push([uuid, tasks[uuid]]);
            tmp.sort(function(a, b) {
                r = b[1]['scheduled_time'] - a[1]['scheduled_time'];
                if (r == 0 && a[1]['start_time'] && b[1]['start_time']) {
                    r = b[1]['start_time'] - a[1]['start_time'];
                }
                if (r == 0 && a[1]['start_time'] && !b[1]['start_time']) {
                    r = 1;
                }
                if (r == 0 && !a[1]['start_time'] && b[1]['start_time']) {
                    r = -1;
                }
                if (r == 0 && a[1]['end_time'] && b[1]['end_time']) {
                    r = b[1]['end_time'] - a[1]['end_time'];
                }
                if (r == 0 && a[1]['end_time'] && !b[1]['end_time']) {
                    r = 1;
                }
                if (r == 0 && !a[1]['end_time'] && b[1]['end_time']) {
                    r = -1;
                }
                if (r == 0 && a[1]['start_time'] && b[1]['start_time']) {
                    r = b[1]['start_time'] - a[1]['start_time'];
                }
                if (r == 0 && a[1]['start_time'] && !b[1]['start_time']) {
                    r = 1;
                }
                if (r == 0) {
                    r = a[0].localeCompare(b[0]);
                }
                return r;
            });
            result = {};
            for (let i = 0; i < tmp.length; i++) {
                result[tmp[i][0]] = tmp[i][1];
            }
            return result;
        }
    }
};

var taskMixin = {
    methods: {
        formatDateTime: function(value) {
            result = "";
            if (typeof(value) != 'undefined' && value) {
                result = moment(value * 1000).format(
                    this.$root.$data.dateTimeFormat
                );
            }
            return result;
        },
        formatResult: function(value) {
            result = 'unknown';
            if (typeof(value) != 'undefined') {
                switch (value) {
                    case 0: result = 'failure'; break;
                    case 1: result = 'success'; break;
                }
            }
            return this.tr[result];
        }
    }
};

var commonMixin = {
    methods: {
        cc2Usc: function(camelCaseValue) {
            return camelCaseValue.replace(/[A-Z]/g, function(a) {
                return '_' + String.fromCharCode(a.charCodeAt() ^ 32);
            });
        }
    }
}

var store = new Vuex.Store({ 
    state: {
        suspended: undefined,
        now: undefined,
        lastRefresh: undefined,
        lastSuspend: undefined,
        refreshInterval: undefined,
        nextRefresh: undefined,
        timeRemaining: undefined,
        sections: undefined,
        summaries: undefined
    },
    mutations: {
        setSuspended(state, { suspended, now }) {
            state.suspended = suspended;
            state.now = now;
        },
        setNow(state, now) {
            state.now = now;
        },
        setLastRefresh(state, { lastRefresh, lastSuspend }) {
            state.lastRefresh = lastRefresh;
            state.lastSuspend = lastSuspend;
        },
        setLastSuspend(state, lastSuspend) {
            state.lastSuspend = lastSuspend;
        },
        setRefreshInterval(state, refreshInterval) {
            state.refreshInterval = refreshInterval;
        },
        setNextRefresh(state, nextRefresh) {
            state.nextRefresh = nextRefresh;
        },
        setTimeRemaining(state, timeRemaining) {
            state.timeRemaining = timeRemaining;
        },
        setSections(state, sections) {
            state.sections = sections;
        },
        setSummaries(state, summaries) {
            state.summaries = summaries;
        }
    },
    actions: {
        updateSuspended({ dispatch, commit}, data) {
            commit('setSuspended', { suspended: data[0], now: data[1] });
            dispatch('updateTimes');
        },
        updateLastRefresh({ dispatch, commit}, data) {
            commit('setLastRefresh', {
                lastRefresh: data[0], lastSuspend: data[1]
            });
            dispatch('updateTimes');
        },
        updateLastSuspend({ dispatch, commit}, lastSuspend) {
            commit('setLastSuspend', lastSuspend);
            dispatch('updateTimes');
        },
        updateRefreshInterval({ dispatch, commit}, refreshInterval) {
            commit('setRefreshInterval', refreshInterval);
            dispatch('updateTimes');
        },
        updateNow({ dispatch, commit}, now) {
            commit('setNow', now);
            dispatch('updateTimes');
        },
        updateTimes({ dispatch, commit, state}, debug) {
            let nextRefresh = undefined;
            let timeRemaining = undefined;
            if (!state.suspended) {
                if (
                    typeof(state.refreshInterval) != 'undefined'
                    && typeof(state.lastRefresh) != 'undefined' 
                ) {
                    let lastRefresh = state.lastRefresh;
                    let addTime = undefined;
                    if (
                        typeof(state.lastSuspend) != 'undefined'
                        && state.lastSuspend != null
                        && state.lastSuspend.getTime() > lastRefresh.getTime()
                    ) {
                        nextRefresh = state.nextRefresh;
                    } else {
                        nextRefresh = 
                        moment(state.lastRefresh)
                            .add(state.refreshInterval, 's')
                            .toDate();
                    }
                }
                if (
                    typeof(nextRefresh) != 'undefined'
                    && typeof(state.now) != 'undefined'
                ) {
                    timeRemaining = (
                        nextRefresh.getTime() - state.now.getTime()
                    );
                    if (timeRemaining < 0) {
                        timeRemaining = 0;
                    }
                }
            } else {
                if (
                    typeof(state.timeRemaining) != 'undefined'
                    && typeof(state.lastSuspend) != 'undefined' 
                    && state.lastSuspend != null
                ) {
                    nextRefresh = 
                        moment(state.lastSuspend)
                            .add(state.timeRemaining, 'milliseconds')
                            .toDate();
                }
            }
            if (typeof(nextRefresh) != 'undefined') {
                commit('setNextRefresh', nextRefresh);
            }
            if (typeof(timeRemaining) != 'undefined') {
                commit('setTimeRemaining', timeRemaining);
            }
        },
        updateStatus({ dispatch, commit, state}) {
            return new Promise((resolve) => {
                Vue.http.post('Admin.CronStatus/getStatus', {}).then(
                    (response) => {
                        commit('setSections', response.data['sections']);
                        commit('setSummaries', response.data['summaries']);
                        dispatch('updateLastRefresh', [ new Date(), null ]);
                        resolve();
                    }
                );
            });
        }
    }
});

Vue.use(Vuex);

Vue.component('refresh-bar', {
    props: {
        tr: Object,
        dateTimeFormat: String,
    },
    template: `
        <div class="refresh-bar">
            <div class="refresh-bar-date-time">
                {{ tr['last_refresh'] }}:
                <span class='refresh-bar-value'>{{ lastRefresh }}</span>
            </div>
            <div class="refresh-bar-date-time">
                {{ tr['next_refresh'] }}:
                <span class='refresh-bar-value'>{{ nextRefresh }}</span>
            </div>
            <div class="refresh-bar-time">
                {{ tr['next_refresh_in'] }}:
                <span class='refresh-bar-value'>{{ timeRemaining }}</span>
            </div>
        </div>
    `,
    computed: {
        lastRefresh: function() {
            let result = "";
            let lastRefresh = this.$store.state.lastRefresh;
            if (typeof(lastRefresh) !== 'undefined') {
                result = moment(lastRefresh).format(this.dateTimeFormat);
            }
            return result;
        },
        nextRefresh: function() {
            let result = "";
            let nextRefresh = this.$store.state.nextRefresh;
            if (typeof(nextRefresh) !== 'undefined') {
                result = moment(nextRefresh).format(this.dateTimeFormat);
            }
            return result;
        },
        timeRemaining: function() {
            let result = "";
            let timeRemaining = this.$store.state.timeRemaining;
            if (typeof(timeRemaining) !== 'undefined') {
                timeRemaining = Math.floor(timeRemaining/1000);
                result = "";
                for (let i = 0; i < 2; i++) {
                    result = 
                        ":"
                        + ( "" + (timeRemaining % 60) ).padStart(2, "0")
                        + result;
                    timeRemaining = Math.floor(timeRemaining / 60);
                }
                result = 
                    ( "" + Math.floor(timeRemaining / 60)).padStart(2, "0")
                    + result;
            }
            return result;
        }
    }
});

Vue.component('refresh-suspend', {
    props: {
        tr: Object,
        parentMethod: Object
    },
    data: function() {
        return {
            suspended: Boolean
        }
    },
    template: `
        <button
            class="suspend-button"
            @click="toggle"
            :title="
                suspended ? tr['sus_ctrl_continue'] : tr['sus_ctrl_suspend']
            "
        >{{ suspended ? tr['sus_ctrl_continue'] : tr['sus_ctrl_suspend'] }}
        </button>
    `,
    created: function() {
        this.suspended = false;
    },
    methods: {
        toggle: function() {
            this.suspended = !this.suspended
            this.parentMethod();
        }
    }
});

Vue.component('cron-status-views', {
    props: {
        tr: Object,
        views: Object,
        defaultSelected: String,
    },
    data: function() {
        return {
            sel: Object
        }
    },
    template: `
        <div class="cron-status-views">
            <div class="cron-status-views-navbar">
                <a
                    v-for="(view, index) in views"
                    class="cron-status-views-navtab"
                    :class="{
                        'cron-status-views-navtab-selected': sel == view
                    }"
                    :href="'#' + view.name"
                    @click.prevent="sel = view"
                    :ref="'ntab_' + index"
                    :id="'ntab-' + view.name"
                >{{ tr[view.title] }}</a>
            </div>
            <div class="cron-status-views-tabs">
                <div
                    v-for="view in views"
                    class="cron-status-views-tab"
                    :class="{
                        'cron-status-views-tab-hidden': sel != view
                    }"
                >
                    <component
                        :is="view.name"
                        :parameters="view.parameters"
                        :tr="tr"
                    />
                </div>
            </div>
       </div>
    `,
    mounted: function() {
        let toSelect = 
            (this.defaultSelected != null)
            ? 'ntab-' + this.defaultSelected
            : null
        ;
        for (r in this.$refs) {
            if (toSelect == null) {
                toSelect = this.$refs[r][0].id;
            }
            if (this.$refs[r][0].id == toSelect) {
                this.$refs[r][0].click();
                break;
            }
        }
    }
});

Vue.component('cron-summary', {
    props: {
        tr: Object,
        name: String,
        summary: Object
    },
    template: `
        <div class="cron-summary-outline">
            <div class="cron-summary">
                <div class="cron-summary-item" v-if="name">
                    <div class="cron-summary-name">
                        <div class="cron-summary-name-title">{{ name }}:</div>
                    </div>
                </div>
                <div class="cron-summary-item">
                    <div class="cron-summary-tasks">
                        <div class="cron-summary-tasks-title">
                            {{ tr['summary_tasks'] }}:
                        </div>
                        <div class="cron-summary-lines">
                            <div class="cron-summary-line">
                                <div
                                    class="cron-summary-tasks-value
                                    cron-summary-value-last"
                                >
                                    {{ summary.tasks }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cron-summary-item">
                    <div class="cron-summary-results">
                        <div class="cron-summary-results-title">
                            {{ tr['summary_results'] }}:
                        </div>
                        <div class="cron-summary-lines">
                            <div class="cron-summary-line">
                                <div class="cron-summary-results-label">
                                    {{ tr['summary_results_ok'] }}:
                                </div>
                                <div class="cron-summary-results-label">
                                    {{ tr['summary_results_failed'] }}:
                                </div>
                                <div
                                    class="cron-summary-results-label
                                    cron-summary-label-last"
                                >
                                    {{ tr['summary_results_unknown'] }}:
                                </div>
                            </div>
                            <div class="cron-summary-line">
                                <div
                                    class="cron-summary-results-value"
                                    :class="{
                                        'cron-value-ok': summary.successes > 0
                                    }"
                                >
                                    {{ summary.successes }}
                                </div>
                                <div
                                    class="cron-summary-results-value"
                                    :class="{
                                        'cron-value-error': summary.failures > 0
                                    }"
                                >
                                    {{ summary.failures }}
                                </div>
                                <div
                                    class="cron-summary-results-value
                                    cron-summary-value-last"
                                >
                                    {{ summary.unknowns }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cron-summary-item">
                    <div class="cron-summary-errors">
                        <div class="cron-summary-errors-title">
                            {{ tr['summary_errors'] }}:
                        </div>
                        <div class="cron-summary-lines">
                            <div class="cron-summary-line">
                                <div
                                    class="cron-summary-errors-value
                                    cron-summary-value-last"
                                    :class="{
                                        'cron-value-error': summary.errors > 0
                                    }"
                                >
                                    {{ summary.errors }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cron-summary-item">
                    <div class="cron-summary-durations">
                        <div class="cron-summary-durations-title">
                            {{ tr['summary_durations'] }}:
                        </div>
                        <div class="cron-summary-lines">
                            <div class="cron-summary-line">
                                <div class="cron-summary-durations-label">
                                    {{ tr['summary_duration_min'] }}:
                                </div>
                                <div class="cron-summary-durations-label">
                                    {{ tr['summary_duration_max'] }}:
                                </div>
                                <div
                                    class="cron-summary-durations-label
                                    cron-summary-label-last"
                                >
                                    {{ tr['summary_duration_avg'] }}:
                                </div>
                            </div>
                            <div class="cron-summary-line">
                                <div class="cron-summary-durations-value">
                                    {{ summary.duration_min }}
                                </div>
                                <div class="cron-summary-durations-value">
                                    {{ summary.duration_max }}
                                </div>
                                <div
                                    class="cron-summary-durations-value
                                    cron-summary-value-last"
                                >
                                    {{ summary.duration_avg }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
});

Vue.component('cron-short-task', {
    props: {
        tr: Object,
        uuid: String,
        task: Object,
        showSection: Boolean,
        showEntrypoint: Boolean
    },
    mixins: [ taskMixin ],
    template: `
        <div class="cron-tasks-group">
            <div class="cron-tasks-item" :class="{
                'cron-task-success': task['result'] == 1,
                'cron-task-failure': task['result'] == 0,
                'cron-task-error': task['failed']
            }">
                <div
                    v-if="showSection && task['section']"
                    class="cron-task-field cron-task-field-inrow
                    cron-task-field-inrow-first"
                >
                    {{ task['section'] }}
                </div>
                <div
                    v-if="showEntrypoint && task['entry_point']"
                    class="cron-task-field cron-task-field-inrow"
                    :class="{
                        'cron-task-field-inrow-first': !showSection
                    }"
                >
                    {{ task['entry_point'] }}
                </div>
                <div
                    class="cron-task-field cron-task-field-inrow"
                    :class="{
                        'cron-task-field-inrow-first':
                            !showSection && !showEntrypoint
                    }"
                >
                    {{ uuid }}
                </div>
                <div
                    class="cron-task-field cron-task-field-inrow
                    cron-task-oneline"
                >
                    {{ formatDateTime(task['scheduled_time']) }}
                </div>
                <div
                    class="cron-task-field cron-task-field-inrow
                    cron-task-oneline"
                >
                    {{ formatDateTime(task['start_time']) }}
                </div>
                <div
                    class="cron-task-field cron-task-field-inrow
                    cron-task-field-inrow-last cron-task-oneline"
                >
                    {{ formatDateTime(task['end_time']) }}
                </div>
            </div>
        </div>
    `
});

Vue.component('cron-detailed-task', {
    props: {
        tr: Object,
        uuid: String,
        task: Object
    },
    mixins: [ taskMixin ],
    template: `
        <div class="cron-task-outline" :class="{
            'cron-task-success': task['result'] == 1,
            'cron-task-failure': task['result'] == 0,
            'cron-task-error': task['failed']
        }">
            <div class="cron-task">
                <div class="cron-task-labels">
                    <div class="cron-task-label">{{ tr['uuid'] }}</div>
                    <div class="cron-task-label">
                        {{ tr['scheduled_time'] }}
                    </div>
                    <div class="cron-task-label">{{ tr['start_time'] }}</div>
                    <div class="cron-task-label">{{ tr['end_time'] }}</div>
                    <div class="cron-task-label">{{ tr['ttl'] }}</div>
                    <div class="cron-task-label">{{ tr['result'] }}</div>
                    <div class="cron-task-label">{{ tr['failed'] }}</div>
                </div>
                <div class="cron-task-field">{{ uuid }}</div>
                <div class="cron-task-field oneline">
                    {{ formatDateTime(task['scheduled_time']) }}
                </div>
                <div class="cron-task-field oneline">
                    {{ formatDateTime(task['start_time']) }}
                </div>
                <div class="cron-task-field oneline">
                    {{ formatDateTime(task['end_time']) }}
                </div>
                <div class="cron-task-field">{{ task['ttl'] }}</div>
                <div class="cron-task-field">
                    {{ formatResult(task['result']) }}
                </div>
                <div class="cron-task-field">
                    {{ task['failed'] ? tr['yes'] : tr['no'] }}
                </div>
            </div>
            <div class="cron-task-output" v-if="task['output']">
                <div class="cron-task-label cron-task-output-label">
                    {{ tr['output'] }}:
                </div>
                <div class="cron-task-field cron-task-output-field">
                    {{ task['output'] }}
                </div>
            </div>
            <div class="cron-task-error-msg" v-if="task['error_msg']">
                <div class="cron-task-label cron-task-error-msg-label">
                    {{ tr['error_msg'] }}:
                </div>
                <div class="cron-task-field cron-task-error-msg-field">
                    {{ task['error_msg'] }}
                </div>
            </div>
        </div>
    `
});

Vue.component('cron-time-based-view', {
    props: {
        tr: Object,
        parameters: Object
    },
    template: `
        <div>
            <a name="cron-time-based-view"/>
            <cron-time-section
                v-for="(title, name) in parameters.sections"
                :tr="tr"
                :name="name"
                :title="title"
            />
        </div>
    `
});

Vue.component('cron-time-section', {
    props: {
        tr: Object,
        name: String,
        title: String
    },
    mixins: [ sectionMixin, taskSetMixin ],
    template: `
        <div class="cron-section">
            <div class="cron-section-title">{{ title }}:</div>
            <cron-summary :tr="tr" :summary="sectionSummary"/>
            <div class="cron-short-tasks" v-if="tasks">
                <div class="cron-task-labels">
                    <div class="cron-task-label">{{ tr['entrypoint'] }}</div>
                    <div class="cron-task-label">{{ tr['uuid'] }}</div>
                    <div class="cron-task-label">
                        {{ tr['scheduled_time'] }}
                    </div>
                    <div class="cron-task-label">{{ tr['start_time'] }}</div>
                    <div class="cron-task-label">{{ tr['end_time'] }}</div>
                </div>
                <cron-short-task
                    v-for="(task, uuid) in sortedTasks"
                    :uuid="uuid"
                    :task="task"
                    :tr="tr"
                    :showEntrypoint="true"
                />
            </div>
            <div class="cron-no-tasks" v-if="!tasks">
                {{ tr['no_tasks'] }}
            </div>
        </div>
    `,
    created: function() {
        this.modes = [ 'current', 'history' ];
    },
    computed: {
        tasks: function() {
            let result = {};
            let sections = this.$store.state.sections;
            if (
                typeof(sections) != 'undefined'
                && typeof(sections[this.name]) != undefined
            ) {
                for (entryPoint in sections[this.name]) {
                    for (m = 0 ; m < this.modes.length; m++) {
                        let mode = this.modes[m];
                        if (typeof(
                                sections[this.name][entryPoint][mode]
                            ) != 'undefined'
                        ) {
                            for (
                                uuid
                                in sections[this.name][entryPoint][mode]
                            ) {
                                    let task = sections[this.name][entryPoint]
                                                [mode][uuid];
                                    task['entry_point'] = entryPoint;
                                    result[uuid] = task;
                            }
                        }
                    }
                }
            }
            return (Object.keys(result).length > 0) ? result : false;
        },
        sortedTasks: function() {
            return this.sortTasks(this.tasks);
        },
        sectionSummary: function() {
            return this.getSectionSummary(this.name);
        }
    }
});

Vue.component('cron-task-based-view', {
    props: {
        tr: Object,
        parameters: Object
    },
    template: `
        <div>
            <a name="cron-task-based-view"/>
            <cron-task-section
                v-for="(title, name) in parameters.sections"
                :tr="tr"
                :name="name"
                :title="title"
            />
        </div>
    `
});

Vue.component('cron-task-section', {
    props: {
        tr: Object,
        name: String,
        title: String,
    },
    mixins: [ sectionMixin ],
    template: `
        <div class="cron-section">
            <div class="cron-section-title">{{ title }}:</div>
            <cron-summary :tr="tr" :summary="sectionSummary"/>
            <div class="cron-section-entrypoints">
                <cron-task-section-entrypoint
                    v-for="(modes, entrypoint) in entrypoints"
                    :entrypoint="entrypoint"
                    :modes="modes"
                    :tr="tr"
                    :summary="getEPSummary(name, entrypoint)"
                />
            </div>
        </div>
    `,
    computed: {
        entrypoints: function() {
            let result = {};
            let sections = this.$store.state.sections;
            if (
                typeof(sections) != 'undefined'
                && typeof(sections[this.name]) != undefined
            ) {
                result = sections[this.name];
            }
            return result;
        },
        sectionSummary: function() {
            return this.getSectionSummary(this.name);
        }
    }
});

Vue.component('cron-task-section-entrypoint', {
    props: {
        tr: Object,
        entrypoint: String,
        modes: Object,
        summary: Object
    },
    template: `
            <div class="cron-entrypoint">
                    <div class="cron-entrypoint-line">
                        <div class="cron-entrypoint-title">
                            <span class="cron-entrypoint-title-label">
                                {{ tr['entrypoint_label'] }}</span>:&#160;
                            <span class="cron-entrypoint-title-value">
                                {{ entrypoint }}
                            </span>
                        </div>
                    </div>
                    <div class="cron-entrypoint-line">
                        <cron-entrypoint-info :info="modes.info" :tr="tr"/>
                    </div>
                    <div class="cron-entrypoint-line">
                        <div class="cron-entrypoint-item">
                            <cron-summary :tr="tr" :summary="summary"/>
                        </div>
                    </div>
                    <div class="cron-entrypoint-line">
                        <cron-entrypoint-current
                            :tasks="modes.current"
                            :tr="tr"
                        />
                    </div>
                    <div class="cron-entrypoint-line">
                        <cron-entrypoint-history
                            :tasks="modes.history"
                            :tr="tr"
                        />
                    </div>
            </div>
    `
});

Vue.component('cron-entrypoint-info', {
    props: {
        tr: Object,
        info: Object
    },
    template: `
        <div class="cron-entrypoint-info">
            <div class="cron-entrypoint-info-details">
                <cron-entrypoint-info-item
                    v-for="name in columns"
                    v-if="info[name]"
                    :name="name"
                    :value="info[name]"
                    :tr="tr"
                />
            <div>
        </div>
    `,
    created: function() {
        this.columns = [ "cronString", "description", "maxHistory", "ttl" ];
    }
});

Vue.component('cron-entrypoint-info-item', {
    props: {
        tr: Object,
        name: String,
        value: String
    },
    mixins: [ commonMixin ],
    template: `
        <div class="cron-entrypoint-info-item">
            <div class="cron-entrypoint-info-item-label">
                {{ tr[cc2Usc(name)] }}
            </div>
            <div class="cron-entrypoint-info-item-value">{{ value }}</div>
        </div>
    `
});

Vue.component('cron-entrypoint-current', {
    props: {
        tr: Object,
        tasks: Object
    },
    mixins: [ taskSetMixin ],
    template: `
        <div class="cron-entrypoint-current">
            <div class="cron-entrypoint-current-title">
                {{ tr['current'] }}:
            </div>
            <div class="cron-short-tasks" v-if="tasks">
                <div class="cron-task-labels">
                    <div class="cron-task-label">{{ tr['uuid'] }}</div>
                    <div class="cron-task-label">
                        {{ tr['scheduled_time'] }}
                    </div>
                    <div class="cron-task-label">{{ tr['start_time'] }}</div>
                    <div class="cron-task-label">{{ tr['end_time'] }}</div>
                </div>
                <cron-short-task
                    v-for="(task, uuid) in sortedTasks"
                    :uuid="uuid"
                    :task="task"
                    :tr="tr"
                />
            </div>
            <div class="cron-no-tasks" v-if="!tasks">
                {{ tr['no_tasks'] }}
            </div>
        </div>
    `,
    computed: {
        sortedTasks: function() {
            return this.sortTasks(this.tasks);
        }
    }
});

Vue.component('cron-entrypoint-history', {
    props: {
        tr: Object,
        tasks: Object
    },
    mixins: [ taskSetMixin ],
    template: `
        <div class="cron-entrypoint-history">
            <div class="cron-entrypoint-history-title">
                {{ tr['history'] }}:
            </div>
            <div class="cron-entrypoint-tasks-notable" v-if="tasks">
                <cron-detailed-task
                    v-for="(task, uuid) in sortedTasks"
                    :uuid="uuid"
                    :task="task"
                    :tr="tr"
                />
            </div>
            <div class="cron-no-tasks" v-if="!tasks">
                No tasks!
            </div>
        </div>
    `,
    computed: {
        sortedTasks: function() {
            return this.sortTasks(this.tasks);
        }
    }
});

Vue.component('cron-result-based-view', {
    props: {
        tr: Object,
        parameters: Object
    },
    template: `
        <div>
            <a name="cron-result-based-view"/>
            <cron-result-based-summaries :tr="tr"/>
            <cron-result
                :type="'error'"
                :title="tr['result_errors']"
                :tr="tr"
            />
            <cron-result
                :type="'failure'"
                :title="tr['result_failures']"
                :tr="tr"
            />
            <cron-result
                :type="'success'"
                :title="tr['result_successes']"
                :tr="tr"
            />
            <cron-result
                :type="'unknown'"
                :title="tr['result_unknowns']"
                :tr="tr"
            />
        </div>
    `
});

Vue.component('cron-result-based-summaries', {
    props: {
        tr: Object
    },
    template: `
        <div>
            <cron-summary
                v-for="(items, section) in summaries"
                :summary="items['summary']"
                :name="section"
                :tr="tr"
            />
        </div>
    `,
    computed: {
        summaries: function() {
            let result = {};
            let summaries = this.$store.state.summaries;
            if (
                typeof(summaries) != 'undefined'
            ) {
                result = summaries;
            }
            return result;
        }
    },
});

Vue.component('cron-result', {
    props: {
        tr: Object,
        type: String,
        title: String,
    },
    mixins: [ taskSetMixin ],
    template: `
        <div class='cron-result'>
            <div class='cron-result-title'>{{ title }}:</div>
            <div class="cron-short-tasks" v-if="tasks">
                <div class="cron-task-labels">
                    <div class="cron-task-label">{{ tr['section'] }}</div>
                    <div class="cron-task-label">{{ tr['entrypoint'] }}</div>
                    <div class="cron-task-label">{{ tr['uuid'] }}</div>
                    <div class="cron-task-label">
                        {{ tr['scheduled_time'] }}
                    </div>
                    <div class="cron-task-label">{{ tr['start_time'] }}</div>
                    <div class="cron-task-label">{{ tr['end_time'] }}</div>
                </div>
                <cron-short-task
                    v-for="(task, uuid) in sortedTasks"
                    :uuid="uuid"
                    :task="task"
                    :tr="tr"
                    :showEntrypoint="true"
                    :showSection="true"
                />
            </div>
            <div class="cron-no-tasks" v-if="!tasks">
                {{ tr['no_tasks'] }}
            </div>
        </div>
    `,
    computed: {
        tasks: function() {
            let result = {};
            let sections = this.$store.state.sections;
            if (typeof(sections) != 'undefined') {
                for (section in sections) {
                    for (entryPoint in sections[section]) {
                        if (
                            typeof(sections[section][entryPoint]['history'])
                            != 'undefined'
                        ) {
                            for (
                                uuid
                                in sections[section][entryPoint]['history']
                            ) {
                                let task = sections[section][entryPoint]
                                                ['history'][uuid];
                                let isValid = false;
                                switch (this.type) {
                                    case "error":
                                        if (task['failed']) {
                                            isValid = true;
                                        }
                                        break;
                                    case "failure":
                                        if (
                                            !task['failed']
                                            && task['result'] == 0
                                        ) {
                                            isValid = true;
                                        }
                                        break;
                                    case "success":
                                        if (task['result'] == 1) {
                                            isValid = true;
                                        }
                                        break;
                                    default:
                                        if (task['result'] > 1) {
                                            isValid = true;
                                        }
                                        break;
                                }
                                if (isValid) {
                                    task['section'] = section;
                                    result[uuid] = task;
                                }
                            }
                        }
                    }
                }
            }
            return (Object.keys(result).length > 0) ? result : false;
        },
        sortedTasks: function() {
            return this.sortTasks(this.tasks);
        }
    }
});