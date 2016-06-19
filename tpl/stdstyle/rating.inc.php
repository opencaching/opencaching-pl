<?php

$rating_tpl = "<tr>
                <td valign=\"top\"><img src=\"images/rating-star.png\" class=\"icon16\" alt=\"\" title=\"\" align=\"middle\" />&nbsp;<strong>" . tr('my_recommend') . ":</strong></td>
                <td valign=\"top\">
                    {rating_msg}
                    <noscript><br /><b>Rekomendacje mogą być dodawane tylko podczas robienia wpisu do logu!</b></noscript>
                </td>
            </tr>
            <tr><td class=\"spacer\" colspan=\"2\"></td></tr>";

$rating_allowed = '<input type="checkbox" name="rating" id="l_rating" value="1" class="checkbox" {chk_sel}/><label for="l_rating">' . $language[$lang]['want_to_recommend'] . '.</label>';
$rating_maxreached = '<b>' . tr('alternative_recommend') . '<a href="mytop5.php">' . tr('here') . '</a>.</b>';
$rating_too_few_founds = '' . $language[$lang]['possible_recommend'] . ': {recommendationsNr}';
$rating_stat = '' . $language[$lang]['number_my_recommend'] . ': {curr} ' . $language[$lang]['number_possible_recommend'] . ' {max}.';
$rating_own = '<input type="checkbox" name="rating" id="l_rating" value="1" class="checkbox" {chk_dis}/><label for="l_rating"><b>' . tr('not_recommend_own') . '.</b></label>';

