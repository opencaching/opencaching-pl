<?php
use src\Models\ApplicationContainer;

?>
<div class="content2-pagetitle"><img src="/images/blue/stat1.png" class="icon32" alt="{title_text}" title="{title_text}" />&nbsp;{{statistics}}</div>
<div style="line-height: 1.8em; font-size: 13px;">

    <?php
    $loggedUser = ApplicationContainer::GetAuthorizedUser();
    if ($loggedUser && $loggedUser->hasOcTeamRole()) {
        echo '<br/><img src="graphs/COGstat.php" alt="" title="" align="middle" /><br/><br/>';
    }
    ?>
</div>
