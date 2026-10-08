<?php

use src\Enums\PowerTrailStatusesEnum;
use src\Utils\Uri\SimpleRouter;

$powerTrail = $view->powerTrail;
$type = $view->types[(int) $powerTrail['type']] ?? null;
$statusKey = $view->statuses[(int) $powerTrail['status']] ?? null;

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <a href="<?= SimpleRouter::getLink('Admin.PowerTrailAdmin'); ?>">
            <?= tr('menu_octeam_power_trail'); ?>
        </a>
        / <?= tr('menu_octeam_power_trail_edit'); ?>
        / <?= htmlspecialchars($powerTrail['name'], ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <table class="table">
        <tbody>
            <tr>
                <td class="content-title-noshade"><?= tr('pt008'); ?></td>
                <td>
                    <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>">
                        <?= htmlspecialchars($powerTrail['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt023'); ?></td>
                <td><?= $type ? tr($type['translationKey']) : (int) $powerTrail['type']; ?></td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('status_label'); ?></td>
                <td class="powerTrail-status">
                    <span class="powerTrail-status-label"><?= $statusKey ? tr($statusKey) : (int) $powerTrail['status']; ?></span>
                    <button type="button" class="blue-button-small powerTrail-status-change-button"><?= tr('powertrail_admin_change_status'); ?></button>
                    <form class="powerTrail-status-form" method="post" hidden
                          data-ln-error-message="<?= htmlspecialchars(tr('powertrail_admin_change_status_error'), ENT_QUOTES, 'UTF-8'); ?>"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'changeStatus',
                              (int) $powerTrail['id']
                          ); ?>">
                        <select name="status">
                            <?php foreach ([
                                PowerTrailStatusesEnum::STATUS_OPEN,
                                PowerTrailStatusesEnum::STATUS_INSERVICE,
                                PowerTrailStatusesEnum::STATUS_CLOSED,
                            ] as $statusId) { ?>
                                <option value="<?= $statusId; ?>"
                                    <?= (int) $powerTrail['status'] === $statusId ? 'selected' : ''; ?>>
                                    <?= tr($view->statuses[$statusId]); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <button type="submit" class="blue-button-small"><?= tr('save'); ?></button>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt020'); ?></td>
                <td><?= (int) $powerTrail['cacheCount']; ?></td>
            </tr>
            <tr>
                <td class="content-title-noshade"><?= tr('pt033'); ?></td>
                <td>
                    <form method="post"
                          action="<?= SimpleRouter::getLink(
                              'Admin.PowerTrailAdmin',
                              'refreshPowerTrail',
                              (int) $powerTrail['id']
                          ); ?>">
                        <button type="submit" class="blue-button-small"><?= tr('pt033'); ?></button>
                    </form>
                </td>
        </tbody>
    </table>

    <h2><?= tr('pt020'); ?></h2>
    <?php if (empty($view->caches)) { ?>
        <p><?= tr('powertrail_admin_no_caches'); ?></p>
    <?php } else { ?>
        <table class="table w-100">
            <thead>
                <tr>
                    <th><?= tr('name_label'); ?></th>
                    <th><?= tr('owner'); ?></th>
                    <th><?= tr('waypoint'); ?></th>
                    <th><?= tr('number_founds'); ?></th>
                    <th><?= tr('status_label'); ?></th>
                    <th><?= tr('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->caches as $cache) { ?>
                    <tr>
                        <td>
                            <a href="/viewcache.php?cacheid=<?= (int) $cache['cache_id']; ?>">
                                <?= htmlspecialchars($cache['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td>
                            <?php if (! empty($cache['user_id']) && ! empty($cache['username'])) { ?>
                                <a href="/viewprofile.php?userid=<?= (int) $cache['user_id']; ?>"
                                   target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars($cache['username'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php } else { ?>
                                <?= htmlspecialchars($cache['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            <?php } ?>
                        </td>
                        <td>
                            <?php $waypoint = (string) ($cache['wp_oc'] ?? ''); ?>
                            <a href="/viewcache.php?wp=<?= rawurlencode($waypoint); ?>">
                                <?= htmlspecialchars($waypoint, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td><?= (int) $cache['founds']; ?></td>
                        <td>
                            <?= htmlspecialchars(
                                $view->cacheStatuses[(int) $cache['status']] ?? (string) $cache['status'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </td>
                        <td>
                            <form class="powerTrail-remove-cache-form" method="post"
                                  data-refresh-url="<?= SimpleRouter::getLink(
                                      'Admin.PowerTrailAdmin',
                                      'refreshPowerTrail',
                                      (int) $powerTrail['id']
                                  ); ?>"
                                  data-ln-confirm-message="<?= htmlspecialchars(tr('powertrail_admin_confirm_remove_cache'), ENT_QUOTES, 'UTF-8'); ?>"
                                  data-ln-error-message="<?= htmlspecialchars(tr('powertrail_admin_remove_cache_error'), ENT_QUOTES, 'UTF-8'); ?>"
                                  action="<?= SimpleRouter::getLink(
                                      'Admin.PowerTrailAdmin',
                                      'removeCache',
                                      (int) $powerTrail['id']
                                  ); ?>">
                                <input type="hidden" name="cacheId" value="<?= (int) $cache['cache_id']; ?>">
                                <button type="submit" class="blue-button-small"><?= tr('powertrail_admin_remove'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
