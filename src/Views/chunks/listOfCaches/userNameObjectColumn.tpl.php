<?php

use src\Models\User\User;

/**
 * This is column which displays user name.
 * $date arg has to contains User object
 *
 * @param User $user
 */

return function (User $user) {
    ?>
    <a href="<?= $user->getProfileUrl() ?>" target=”_blank” class="links">
        <?= htmlentities($user->getUserName()) ?>
    </a>
    <?php
};