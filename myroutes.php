<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'myroutes';
        $user_id = $usr['userid'];

        $route_rs = XDb::xSql(
            "SELECT `route_id` ,`description` `desc`, `name`,`radius`,`length`
            FROM `routes`  WHERE `user_id`= ?
            ORDER BY `route_id` DESC", $user_id);


        if ( $routes_record = XDb::xFetchArray($route_rs) ) {

            $routes .= '<div class="headitems">';
            $routes .= '<div style="width:80px;" class="myr">' . tr('route_name') . '</div><div class="ver">&nbsp;</div><div style="width:295px;" class="myr">&nbsp;' . tr('route_desc') . '</div><div class="ver">&nbsp;</div><div style="width:60px;" class="myr">&nbsp;' . tr('radius') . '</div><div class="ver">&nbsp;</div><div style="width:60px;" class="myr">&nbsp;' . tr('length') . '</div><div class="ver">&nbsp;</div><div style="width:70px;" class="myr">&nbsp;' . tr('caches') . '</div><div class="ver">&nbsp;</div><div style="width:50px;" class="myr">' . tr('edit') . '</div><div class="ver">&nbsp;</div><div style="width:20px;" class="myr">&nbsp;' . tr('delete') . '</div></div>';

            do{
                $desc = $routes_record['desc'];
                if ($desc != '') {
                    require_once($rootpath . 'lib/class.inputfilter.php');
                    $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
                    $desc = $myFilter->process($desc);
                }
                $routes .= '<div class="listitems">';
//                          $routes .= '<div style="margin-left:5px;width:75px;" class="myr">'.$routes_record['name']. '</div><div class="ver35">&nbsp;</div><div style="width:295px;" class="myr">'.nl2br($desc).'</div><div class="ver35">&nbsp;</div><div style="width:60px;text-align:center;" class="myr">'.$routes_record['radius']. ' km</div><div class="ver35">&nbsp;</div><div style="width:60px;text-align:center;" class="myr">'.round($routes_record['length'],0). ' km</div><div class="ver35">&nbsp;</div><div style="width:70px;float:left;text-align:center;"><a class="links" href="myroutes_search.php?routeid='.$routes_record['route_id'].'"><img src="tpl/stdstyle/images/action/16x16-search.png" alt="" title="Search caches along route" /></a></div><div class="ver35">&nbsp;</div><div style="width:50px;float:left;text-align:center;"><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'"><img src="images/actions/edit-16.png" alt="" title="Edit route" /></a></div><div class="ver35">&nbsp;</div><div style="width:20px;float:left;text-align:center;"><a class="links" href="myroutes_edit.php?routeid='.$routes_record['route_id'].'&delete" onclick="return confirm(\'Czy chcesz usunąć tę trase?\');"><img src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title="Usuń" /></a></div></div>';
                $routes .= '<table border="0" class="myr"><tr><td style="margin-left:3px;width:75px;" class="myr">' . $routes_record['name'] . '</td><td width="2" style="border-right:solid thin #7fa2ca"></td>
                            <td style="width:297px;" class="myr">' . nl2br($desc) . '</td><td width="2" style="border-right:solid thin #7fa2ca"></td>
                            <td style="width:65px;" class="myr">' . $routes_record['radius'] . ' km</td><td width="2" style="border-right:solid thin #7fa2ca"></td>
                            <td style="width:62px;" class="myr">' . $routes_record['length'] . ' km</td><td width="2" style="border-right:solid thin #7fa2ca"></td>
                            <td style="width:73px;" class="myr"><a class="links" href="myroutes_search.php?routeid=' . $routes_record['route_id'] . '"><img src="tpl/stdstyle/images/action/16x16-search.png" alt="" title=' . tr("search_caches_along_route") . ' /></a></td><td width="2" style="border-right:solid thin #7fa2ca"></td>
                            <td style="width:53px;" class="myr"><a class="links" href="myroutes_edit.php?routeid=' . $routes_record['route_id'] . '"><img src="images/actions/edit-16.png" alt="" title=' . tr('edit_route') . ' /></a></td><td width="2" style="border-right:solid thin #7fa2ca"></td>
                            <td style="width:23px;" class="myr"><a class="links" href="myroutes_edit.php?routeid=' . $routes_record['route_id'] . '&delete" onclick="return confirm(\'' . tr("confirm_remove_route") . '\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title=' . tr('delete') . ' /></a></td></tr></table></div>';

            }while( $routes_record = XDb::xFetchArray($route_rs));
            $routes .= '';


            tpl_set_var('content', $routes);

        } else {
            tpl_set_var('content', "<div class=\"listitems\"><br/><center><span style=\"font-size:140%;font-weight:bold \">&nbsp;&nbsp;" . tr('no_routes') . "</span><br/><br/></center></div>");
        }
        XDb::xFreeResults($route_rs);
    }
}

//make the template and send it out
tpl_BuildTemplate();

