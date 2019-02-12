<?php

?>
<style>
    .ui-widget-overlay { background: #c5c5c5; }
    .ui-dialog { box-shadow: 0px 5px 66px rgba(0, 0, 0, 0.5); }
    .ui-dialog { font-size: 14px; }
    .ui-dialog p { font-size: 14px; }
    .ui-dialog form { font-size: 14px; }
    .ui-dialog-titlebar { display: none; }
    .ui-dialog .ui-widget-content { background: #fff; }
    .ui-dialog h2 { font-size: 22px; font-weight: normal; }
    .ui-dialog h2 b.multi { color: #d00; }
    .ui-dialog a { color: #448; text-decoration: none; cursor: pointer; }
    .ui-dialog a:hover { text-decoration: underline; }
    .ui-dialog section { margin: 10px 0; }
    .ui-dialog form section p { font-weight: bold; color: #444; }
    .ui-dialog form section div { padding-left: 10px; }
    .ui-dialog form section label span { display: inline-block; width: 540px; vertical-align: top; }
</style>
<div style='display: none'>
    <div id='okapiGpxFormatterDialogContentsTemplate'>
        <section data-string-id="infoHeaderHTML">
            {{ogpx_infoHeaderHTML}}
        </section>
        <section>
            <h2>
                <span data-string-id="numberOfCachesHeader">
                    {{ogpx_numberOfCachesHeader}}
                </span>
                <b class='okapi-number-of-cachecodes'>0</b>
            </h2>
        </section>
        <form>
            <section>
                <p data-string-id="paramLpcHeader">
                    {{ogpx_paramLpcHeader}}
                </p>
                <div><label>
                    <input type='radio' name='lpc' value='0'>
                    <span data-string-id="paramLpc_0">{{ogpx_paramLpc_0}}</span>
                </label></div>
                <div><label>
                    <input type='radio' name='lpc' value='10'>
                    <span data-string-id="paramLpc_10">{{ogpx_paramLpc_10}}</span>
                </label></div>
                <div><label>
                    <input type='radio' name='lpc' value='mine'>
                    <span data-string-id="paramLpc_mine">{{ogpx_paramLpc_mine}}</span>
                </label></div>
                <div><label>
                    <input type='radio' name='lpc' value='all' checked="checked">
                    <span data-string-id="paramLpc_all">{{ogpx_paramLpc_all}}</span>
                </label></div>
            </section>
            <section>
                <p data-string-id="paramTrackablesHeader">
                    {{ogpx_paramTrackablesHeader}}
                </p>
                <div><label>
                    <input type='radio' name='trackables' value='none'>
                    <span data-string-id="paramTrackables_none">
                        {{ogpx_paramTrackables_none}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='trackables' value='desc:count'>
                    <span data-string-id="paramTrackables_count">
                        {{ogpx_paramTrackables_count}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='trackables' value='desc:list'>
                    <span data-string-id="paramTrackables_all">
                        {{ogpx_paramTrackables_all}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='trackables' value='gc:travelbugs' checked="checked">
                    <span data-string-id="paramTrackables_gc">
                        {{ogpx_paramTrackables_gc}}
                    </span>
                </label></div>
            </section>
            <section>
                <p data-string-id="paramAttrsHeader">
                    {{ogpx_paramAttrsHeader}}
                </p>
                <div><label>
                    <input type='checkbox' name='attrs_desctext' checked="checked">
                    <span data-string-id="paramAttrs_desctext">
                        {{ogpx_paramAttrs_desctext}}
                    </span>
                </label></div>
                <div><label>
                    <input type='checkbox' name='attrs_oxtags' checked="checked">
                    <span data-string-id="paramAttrs_oxtags_HTML">
                        {{ogpx_paramAttrs_oxtags_HTML}}
                    </span>
                </label></div>
                <div><label>
                    <input type='checkbox' name='attrs_gcattrs' checked="checked">
                    <span data-string-id="paramAttrs_gcattrs_HTML">
                        {{ogpx_paramAttrs_gcattrs_HTML}}
                    </span>
                </label></div>
            </section>
            <section>
                <p data-string-id="paramMyNotesHeader">
                    {{ogpx_paramMyNotesHeader}}
                </p>
                <div><label>
                    <input type='checkbox' name='my_notes_desctext' checked="checked">
                    <span data-string-id="paramMyNotes_desctext">
                        {{ogpx_paramMyNotes_desctext}}
                    </span>
                </label></div>
                <div><label>
                    <input type='checkbox' name='my_notes_gcpersonalnote'>
                    <span data-string-id="paramMyNotes_gcpersonalnote_HTML">
                        {{ogpx_paramMyNotes_gcpersonalnote_HTML}}
                    </span>
                </label></div>
            </section>
            <section>
                <p data-string-id="paramLocationSourceHeader">
                    {{ogpx_paramLocationSourceHeader}}
                </p>
                <div><label>
                    <input type='radio' name='location_source' value='default-coords' checked="checked">
                    <span data-string-id="paramLocationSource_default">
                        {{ogpx_paramLocationSource_default}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='location_source' value='alt_wpt:user-coords'>
                    <span data-string-id="paramLocationSource_usercoords">
                        {{ogpx_paramLocationSource_usercoords}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='location_source' value='alt_wpt:parking'>
                    <span data-string-id="paramLocationSource_parking">
                        {{ogpx_paramLocationSource_parking}}
                    </span>
                </label></div>
            </section>
            <section>
                <p data-string-id="paramImagesHeader">
                    {{ogpx_paramImagesHeader}}
                </p>
                <div><label>
                    <input type='radio' name='images' value='none'>
                    <span data-string-id="paramImages_none">
                        {{ogpx_paramImages_none}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='images' value='descrefs:thumblinks'>
                    <span data-string-id="paramImages_thumblinks">
                        {{ogpx_paramImages_thumblinks}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='images' value='descrefs:nonspoilers'>
                    <span data-string-id="paramImages_nonspoilers">
                        {{ogpx_paramImages_nonspoilers}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='images' value='descrefs:all' checked="checked">
                    <span data-string-id="paramImages_all">
                        {{ogpx_paramImages_all}}
                    </span>
                </label></div>
                <div><label>
                    <input type='radio' name='images' value='ox:all'>
                    <span data-string-id="paramImages_oxall_HTML">
                        {{ogpx_paramImages_oxall_HTML}}
                    </span>
                </label></div>
            </section>
            <section>
                <p data-string-id="otherOptionsHeader">
                    {{ogpx_otherOptionsHeader}}
                </p>
                <div><label>
                    <input type='checkbox' name='protection_areas'>
                    <span data-string-id="otherOptions_protection_areas">
                        {{ogpx_otherOptions_protection_areas}}
                    </span>
                </label></div>
                <div><label>
                    <input type='checkbox' name='recommendations' checked="checked">
                    <span data-string-id="otherOptions_recommendations">
                        {{ogpx_otherOptions_recommendations}}
                    </span>
                </label></div>
                <div><label>
                    <input type='checkbox' name='alt_wpts' checked="checked">
                    <span data-string-id="otherOptions_alt_wpts">
                        {{ogpx_otherOptions_alt_wpts}}
                    </span>
                </label></div>
                <div><label>
                    <input type='checkbox' name='mark_found' checked="checked">
                    <span data-string-id="otherOptions_mark_found">
                        {{ogpx_otherOptions_mark_found}}
                    </span>
                </label></div>
            </section>
        </form>
    </div>
    <div id='okapiGpxFormatterDialogContentsTemplate2'>
        <section data-string-id="additionalDownloadsHeaderHTML">
            {{ogpx_additionalDownloadsHeaderHTML}}
        </section>
        <section>
            <ul>
            </ul>
        </section>
    </div>
</div>
