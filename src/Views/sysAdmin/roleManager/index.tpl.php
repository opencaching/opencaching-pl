<?php

use src\Enums\RoleManagerEnum;
use src\Models\User\RoleManagerRepository;
use src\Utils\Uri\SimpleRouter;

$roleManagerUrl = SimpleRouter::getLink('SysAdmin.RoleManager');

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <?= tr('role_manager_title') ?>
    </div>

    <form method="get" action="<?= $roleManagerUrl ?>" class="w-100 d-flex flex-column mb-15">
        <div class="mb-10 d-flex align-items-center">
            <label for="roleManagerSearch" class="w-25 flex-shrink-0 mr-10"><?= tr('role_manager_find_user') ?></label>
            <input id="roleManagerSearch" type="text" name="search"
                   value="<?= htmlspecialchars($view->filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="form-control flex-grow-1">
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="roleManagerRole" class="w-25 flex-shrink-0 mr-10"><?= tr('role_manager_role_filter') ?></label>
            <select id="roleManagerRole" name="role" class="form-control flex-grow-1">
                <option value=""><?= tr('role_manager_all_roles') ?></option>
                <option value="<?= RoleManagerRepository::ROLE_FILTER_NONE ?>"
                    <?= $view->filters['role'] === RoleManagerRepository::ROLE_FILTER_NONE ? 'selected' : '' ?>>
                    <?= tr('role_manager_no_role') ?>
                </option>
                <?php foreach ($view->roleOptions as $roleValue => $roleLabel) { ?>
                    <option value="<?= htmlspecialchars($roleValue, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $view->filters['role'] === $roleValue ? 'selected' : '' ?>>
                        <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="roleManagerSort" class="w-25 flex-shrink-0 mr-10"><?= tr('sort_by') ?></label>
            <select id="roleManagerSort" name="sort" class="form-control flex-grow-1">
                <?php foreach ($view->sortOptions as $sortValue => $sortLabel) { ?>
                    <option value="<?= htmlspecialchars($sortValue, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $view->filters['sort'] === $sortValue ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sortLabel, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mt-10 d-flex align-items-center flex-wrap">
            <button type="submit" class="blue-button-small mr-10"><?= tr('search') ?></button>

            <a class="orange-button-small mr-10" href="<?= $roleManagerUrl ?>">
                <?= tr('clear_search') ?>
            </a>

            <span class="text-muted">
                <?= tr('role_manager_results_summary', [(int) $view->totalCount, (int) $view->limit]) ?>
            </span>
        </div>
    </form>

    <?php if (empty($view->users)) { ?>
        <p>
            <?= $view->filters['search'] !== null
                ? tr('role_manager_no_users_found')
                : tr('role_manager_no_users'); ?>
        </p>
    <?php } else { ?>
        <table class="table table-striped full-width">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?= tr('username') ?></th>
                    <th><?= tr('email_address') ?></th>
                    <th><?= tr('role_manager_roles') ?></th>
                    <th class="text-center"><?= tr('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->users as $user) {
                    $userId = (int) $user['userId'];
                    $isCurrentUser = $userId === (int) $view->currentUserId;
                    $setRolesUrl = SimpleRouter::getLink(
                        'SysAdmin.RoleManager',
                        'setRoles',
                        $userId
                    );
                    $removeAllUrl = SimpleRouter::getLink(
                        'SysAdmin.RoleManager',
                        'removeAllRoles',
                        $userId
                    );
                    ?>
                    <tr>
                        <td class="align-middle"><?= $userId ?></td>
                        <td class="align-middle">
                            <a href="<?= SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId); ?>"
                               class="links">
                                <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <?php if ($isCurrentUser) { ?>
                                <span class="label label-info"><?= tr('role_manager_you') ?></span>
                            <?php } ?>
                        </td>
                        <td class="align-middle"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="align-middle">
                            <div class="d-flex flex-column">
                                <?php foreach ($view->roles as $role) {
                                    $isAssigned = in_array($role['value'], $user['roleValues'], true);
                                    $isSelfSysAdmin = $isCurrentUser
                                        && $role['value'] === RoleManagerEnum::SYS_ADMIN->value;
                                    ?>
                                    <div class="d-flex align-items-center mb-1">
                                        <label class="d-flex align-items-center flex-shrink-0 m-0 font-weight-normal text-nowrap">
                                            <input type="checkbox"
                                                   class="m-0 mr-1"
                                                   form="role-manager-set-form-<?= $userId ?>"
                                                   name="roles[]"
                                                   value="<?= htmlspecialchars($role['value'], ENT_QUOTES, 'UTF-8') ?>"
                                                <?= $isAssigned ? 'checked' : '' ?>
                                                <?= $isSelfSysAdmin ? 'disabled' : '' ?>>
                                            <?= htmlspecialchars($role['label'], ENT_QUOTES, 'UTF-8') ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-wrap justify-content-center">
                                <form id="role-manager-set-form-<?= $userId ?>" method="post"
                                      action="<?= $setRolesUrl ?>"
                                      class="role-manager-form mr-10">
                                    <input type="hidden" name="userId" value="<?= $userId ?>">
                                    <?php if ($isCurrentUser) { ?>
                                        <input type="hidden" name="roles[]"
                                               value="<?= RoleManagerEnum::SYS_ADMIN->value ?>">
                                    <?php } ?>

                                    <button type="submit" class="blue-button-small-multiline">
                                        <?= tr('role_manager_set_roles') ?>
                                    </button>
                                </form>

                                <form id="role-manager-remove-form-<?= $userId ?>" method="post"
                                      action="<?= $removeAllUrl ?>"
                                      class="role-manager-form"
                                      data-ln-confirm-remove-all="<?= htmlspecialchars(
                                          tr('role_manager_confirm_remove_all'),
                                          ENT_QUOTES,
                                          'UTF-8'
                                      ) ?>">
                                    <input type="hidden" name="userId" value="<?= $userId ?>">

                                    <button type="submit" class="red-button-small-multiline"
                                            data-ln-action="remove_all"
                                        <?= $isCurrentUser ? 'disabled' : '' ?>
                                        <?= $isCurrentUser ? 'title="' . htmlspecialchars(
                                            tr('role_manager_cannot_remove_own_sysadmin_role'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) . '"' : '' ?>>
                                        <?= tr('role_manager_remove_all') ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php $view->callChunk('pagination', $view->paginationModel); ?>
    <?php } ?>
</div>
