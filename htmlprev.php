<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

$no_tpl_build = false;
//Preprocessing
if ($error == false) {
    $the_text = isset($_REQUEST['thetext']) ? $_REQUEST['thetext'] : '';
    $the_html = isset($_REQUEST['thehtml']) ? $_REQUEST['thehtml'] : '';

    if (isset($_REQUEST['toStep2'])) {
        $tplname = 'htmlprev_step2';

        $the_html = htmlspecialchars(mb_ereg_replace("\n", "<br />\n", stripslashes(htmlspecialchars($the_text, ENT_COMPAT, 'UTF-8'))), ENT_COMPAT, 'UTF-8');
    } else if (isset($_REQUEST['toStep3'])) {
        global $rootpath, $stylepath;

        //check the html ...
        require_once($rootpath . 'lib/class.inputfilter.php');
        require_once($stylepath . '/htmlprev.inc.php');

        $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
        $orghtml = $the_html;
        $the_html = $myFilter->process($the_html);

        $tplname = 'htmlprev_step3';

        tpl_set_var('orghtml', htmlspecialchars($orghtml, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('thecode', $the_html);

        tpl_set_var('thehtmlcode', nl2br(stripslashes(htmlspecialchars($the_html, ENT_COMPAT, 'UTF-8'))));
    } else if (isset($_REQUEST['backStep2'])) {
        $tplname = 'htmlprev_step2';
        $the_html = stripslashes(htmlspecialchars($the_html, ENT_COMPAT, 'UTF-8'));
    } else {
        //start
        $tplname = 'htmlprev';
    }

    tpl_set_var('thetext', stripslashes(htmlspecialchars($the_text, ENT_COMPAT, 'UTF-8')));
    tpl_set_var('thehtml', $the_html);
}

if ($no_tpl_build == false) {
    //make the template and send it out
    tpl_BuildTemplate(false);
}
?>
