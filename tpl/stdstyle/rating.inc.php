<?php

use Controllers\MyRecommendationsController;
use Utils\Uri\SimpleRouter;

$rating_tpl = '<tr class="form-group-sm">
                <td class="content-title-noshade"><img src="images/rating-star.png" class="icon16" alt="">&nbsp;'. tr('my_recommend') . ':</td>
                <td class="options">
                    {rating_msg}
                    <noscript><br><b>Rekomendacje mogą być dodawane tylko podczas robienia wpisu do logu!</b></noscript>
                </td>
            </tr>';

$rating_allowed = '<input type="checkbox" name="rating" id="l_rating" value="1" class="checkbox" {chk_sel}/><label for="l_rating">' . tr('want_to_recommend'). '.</label>';
$recommendationsUrl = SimpleRouter::getLink(MyRecommendationsController::class, 'recommendations');
$rating_maxreached = '<b>' . tr('alternative_recommend') . '<a href="'.$recommendationsUrl.'">' . tr('here') . '</a>.</b>';
$rating_too_few_founds = tr('possible_recommend') . ': {recommendationsNr}';
$rating_stat = tr('number_my_recommend') . ': {curr} ' . tr('number_possible_recommend') . ' {max}.';
$rating_own = '<input type="checkbox" name="rating" id="l_rating" value="1" class="checkbox" {chk_dis}/><label for="l_rating"><b>' . tr('not_recommend_own') . '.</b></label>';
