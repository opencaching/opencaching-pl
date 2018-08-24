<?php
use Utils\Uri\Uri;
use lib\Objects\GeoCache\GeoCacheLogCommons;

return function($isUserAuthorized, $cacheType) {
    $chunkCSS = Uri::getLinkWithModificationTime(
        '/tpl/stdstyle/chunks/logFilter.css'
    );

    if ($isUserAuthorized) {
?>
<div class="content2-container bg-blue02 form-group-sm logfilter-container logfilter-hidden" id="logFilter">
    <span class="content-title-noshade-size1">
        <img src="tpl/stdstyle/images/blue/search3.png" class="icon32" alt="<?=tr('log_filter')?>"> <?=tr('log_filter')?>:

        <span class="logfilter-field">
            <span class="logfilter-prompt"><?=tr('log_filter_type')?>:&#160;</span>
            <select name='log_filter_type' class="form-control logfilter-select" filternum="0">
                <option name='filter_opt_any'><?=tr('log_filter_type_any')?></option>
                <?php foreach(GeoCacheLogCommons::getLogTypeTplKeys($cacheType) as $logType => $logTypeKey) { ?>
                <option name='filter_opt_<?=$logType?>'><?=tr($logTypeKey)?></option>
                <?php } //foreach-getLogTypeTplKeys ?>
            </select>
        </span>

        <span class="logfilter-field">
            <span class="logfilter-prompt"><?=tr('log_filter_author')?>:&#160;</span>
            <select name='log_filter_author' class="form-control logfilter-select" filternum="1">
                <option name='filter_opt_any'><?=tr('log_filter_author_any')?></option>
                <option name='filter_opt_current'><?=tr('log_filter_author_currentuser')?></option>
                <option name='filter_opt_owner'><?=tr('CacheOwner')?></option>
                <option name='filter_opt_octeam'><?=tr('cog_user_name')?></option>
            </select>
        </span>
</div>
<script>
    /* include CSS for log filtering */
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>
<script>
/* current filter options */
var filterOptions = [];
/* current filter regexp, based on filter options */
var fiterRx = null;

/**
 * Executed on change of any filter select element value. Based on filternum
 * attribute sets corresponding filterOptions value, updates the regexp and
 * calls applyLogFilters
 */
function changeFilterOption(ev) {
    var num = parseInt($(this).attr("filternum"));
    var val = $(this).find('option:selected').attr("name")
        .substr("filter_opt_".length);
    if (filterOptions[num] != val) {
        filterOptions[num] = val;
        setFilterRx();
        applyLogFilters();
    }
}

/**
 * Updates filterRx variable with regexp based on filterOptions
 */
function setFilterRx() {
    var filterRxStr = '';
    filterOptions.forEach(function(item, index) {
        filterRxStr += ':' + (item != "any" ? item : '[^:]*');
    });
    filterRxStr += ':';
    filterRx = new RegExp(filterRxStr);
}

/**
 * Called upon change of filterOptions or update of log entries. Adds
 * logfilter-hidden class to each entry not matching filterRx and removes this
 * class from entries mathcing regexp.
 * If no entries matching current options are visible, tries to load more
 * entries, if available.
 */
function applyLogFilters() {
    $(".logs").filter(function() {
        return !$(this).attr('filterable').match(filterRx)
    }).addClass("logfilter-hidden");
    $(".logs").filter(function() {
        return $(this).attr('filterable').match(filterRx)
    }).removeClass("logfilter-hidden");
    if (
        $(".logs:not(.logfilter-hidden)").length == 0
        && typeof(loadLogEntries) === "function"
    ) {
        loadLogEntries.call(this);
    }
}

$(document).ready(function() {
    /*
     * adds applyLogFilters to logEntriesPostLoadHooks to enable this function
     * call upon every load of log entries
     */
    if (
        typeof(logEntriesPostLoadHooks) != "undefined"
        && logEntriesPostLoadHooks.constructor == Array
    ) {
        logEntriesPostLoadHooks.push(applyLogFilters);
    }

    /*
     * shows the logs filter elements, assigns changeFilterOption to change
     * event for each select in filter and creates initial filterRx regexp
     */
    if ($("#logFilter").length) {
        $("#logFilter").removeClass("logfilter-hidden");
        $("select[filternum]").each(function() {
            var num = parseInt($(this).attr("filternum"));
            filterOptions[num] = $(this).find('option:selected').attr("name")
                .substr("filter_opt_".length);
            $(this).change(changeFilterOption);
        });
        setFilterRx();
    }
});
</script>
<?php
    }
};
